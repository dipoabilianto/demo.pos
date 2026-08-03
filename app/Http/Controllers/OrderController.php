<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Voucher;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;


class OrderController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private SettingService $settingService,
        private OrderService $orderService,
    ) {}

    private function createOrderItems(Order $order, array $orderItems): void
    {
        foreach ($orderItems as $item) {
            $product = $item['product'];
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ]);
            if (! $product->isUnlimited()) {
                $product->decrement('stock', $item['quantity']);
            }
        }
    }

    private function calculateTax(float $subtotal): float
    {
        $settings = $this->settingService->getSettings();
        if (! ($settings['tax_enabled'] ?? false)) {
            return 0;
        }
        $rate = ($settings['tax_rate'] ?? 0) / 100;
        return ($settings['tax_type'] ?? 'exclude') === 'include'
            ? $subtotal - ($subtotal / (1 + $rate))
            : $subtotal * $rate;
    }

    public function catalog(): View
    {
        $products = Product::with('category')->where('is_active', true)->where(function ($q) {
            $q->where('stock', '>', 0)->orWhere('is_unlimited', true);
        })->get()->groupBy(fn ($p) => $p->category->name ?? 'Lainnya');
        $settings = $this->settingService->getSettings();

        return view('orders.catalog', compact('products', 'settings'));
    }

    public function publicCatalog(Request $request): View
    {
        $firstKey = collect($request->query())->keys()->first();
        $firstVal = $request->query($firstKey);
        $branchId = $request->query('branch_id')
            ?? $request->query('branch')
            ?? (is_numeric($firstKey) && $firstVal === '' ? $firstKey : null)
            ?? (is_numeric($firstVal) ? $firstVal : null)
            ?? session('branch_id');
        $branch = $branchId ? Branch::find($branchId) : null;
        $branch = $branch?->is_online ? $branch : Branch::active()->online()->first();

        $products = $this->orderService->getPublicCatalogProducts($branch);

        $settings = $this->settingService->getSettings();
        $previewOrderNumber = Order::previewOrderNumber('ORDON');
        $isOnline = $branch->is_online ?? false;

        return view('orders.public-catalog', compact('products', 'settings', 'previewOrderNumber', 'branch', 'isOnline'));
    }

    public function checkout(Request $request): View
    {
        $public = $request->boolean('public');
        $settings = $this->settingService->getSettings();
        $previewOrderNumber = Order::previewOrderNumber($public ? 'ORDON' : 'ORDOF');

        return view('orders.checkout', compact('settings', 'public', 'previewOrderNumber'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $productIds = collect($validated['items'])->pluck('product_id');
            $lockedProducts = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = $lockedProducts->get($item['product_id']);
                if (! $product) {
                    return response()->json(['error' => 'Produk tidak ditemukan.'], 422);
                }
                if (! $product->isUnlimited() && $product->stock < $item['quantity']) {
                    return response()->json(['error' => 'Stok produk tidak mencukupi.'], 422);
                }
                $effectivePrice = $product->sale_price ?? $product->price;
                $itemSubtotal = $effectivePrice * $item['quantity'];
                $subtotal += $itemSubtotal;
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'price' => $effectivePrice,
                ];
            }

            $discount = min($validated['discount'] ?? 0, $subtotal);
            $tax = $this->calculateTax($subtotal);
            $total = $subtotal - $discount + $tax;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber('ORDOF'),
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'payment_method' => 'xendit',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->createOrderItems($order, $orderItems);

            DB::commit();

            $redirectRoute = auth()->check() ? route('orders.payment', $order) : route('orders.public-payment', $order);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'redirect' => $redirectRoute,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Gagal membuat pesanan. Silakan coba lagi.'], 500);
        }
    }

    public function publicStore(Request $request): JsonResponse
    {
        $key = 'public-store:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['error' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.'], 429);
        }
        RateLimiter::hit($key, 60);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'voucher_code' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $productIds = collect($validated['items'])->pluck('product_id');
            $lockedProducts = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = $lockedProducts->get($item['product_id']);
                if (! $product) {
                    return response()->json(['error' => 'Produk tidak ditemukan.'], 422);
                }
                if (! $product->isUnlimited() && $product->stock < $item['quantity']) {
                    return response()->json(['error' => 'Stok produk tidak mencukupi.'], 422);
                }
                $effectivePrice = $product->sale_price ?? $product->price;
                $itemSubtotal = $effectivePrice * $item['quantity'];
                $subtotal += $itemSubtotal;
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'price' => $effectivePrice,
                ];
            }

            $discount = 0;
            $voucherId = null;
            $voucher = null;

            if ($voucherCode = $validated['voucher_code'] ?? null) {
                $voucher = Voucher::where('code', $voucherCode)->lockForUpdate()->first();

                if (! $voucher) {
                    return response()->json(['error' => 'Kode voucher tidak valid.'], 422);
                }

                $customerIdentifier = $validated['customer_email'] ?? $validated['customer_phone'] ?? $request->ip();

                if (! $voucher->isValidFor($subtotal, $customerIdentifier)) {
                    return response()->json(['error' => 'Kode voucher tidak dapat digunakan.'], 422);
                }

                $discount = $voucher->calculateDiscount($subtotal);
                $voucherId = $voucher->id;
            }

            $tax = $this->calculateTax($subtotal);
            $total = $subtotal - $discount + $tax;

            $order = Order::create([
                'order_number' => Order::generateOrderNumber('ORDON'),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'payment_method' => 'xendit',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'voucher_id' => $voucherId,
                'voucher_code' => $voucher->code ?? null,
            ]);

            $this->createOrderItems($order, $orderItems);

            if ($voucherId && $voucher ?? null) {
                $customerIdentifier = $validated['customer_email'] ?? $validated['customer_phone'] ?? $request->ip();
                $voucher->markUsed($order, $customerIdentifier);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'redirect' => route('orders.public-payment', $order),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Gagal membuat pesanan. Silakan coba lagi.'], 500);
        }
    }

    public function checkVoucher(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'subtotal' => 'required|numeric|min:0',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $branchId = $validated['branch_id'] ?? session('branch_id');
        if (! $branchId) {
            $branchId = Branch::active()->first()?->id;
        }

        if ($branchId) {
            $result = $this->paymentService->checkVoucherAvailability($validated['code'], $branchId, $validated['subtotal']);
        } else {
            $voucher = Voucher::where('code', $validated['code'])->first();
            $result = $voucher && $voucher->isValidFor($validated['subtotal'])
                ? ['valid' => true, 'discount' => $voucher->calculateDiscount($validated['subtotal']), 'type' => $voucher->type]
                : ['valid' => false];
        }

        if (! ($result['valid'] ?? false)) {
            return response()->json(['valid' => false, 'error' => 'Kode voucher tidak valid.'], 422);
        }

        return response()->json($result);
    }

    public function payment(Order $order): View|RedirectResponse
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (in_array($order->payment_status, ['paid', 'success'])) {
            return redirect()->route('orders.show', $order)->with('success', 'Pesanan ini sudah dibayar.');
        }
        $order->load('items.product');
        $cashMethod = PaymentMethod::where('code', 'cash')->first();
        $transferMethod = PaymentMethod::where('code', 'transfer')->first();
        $paymentMethods = PaymentMethod::active()->whereNotIn('code', ['cash', 'transfer'])->get();
        $settings = $this->settingService->getSettings();

        return view('orders.payment', compact('order', 'paymentMethods', 'settings', 'cashMethod', 'transferMethod'));
    }

    public function publicPayment(Order $order): View
    {
        if (in_array($order->payment_status, ['paid', 'success'])) {
            return view('orders.public-payment')->with('order', $order)->with('paid', true);
        }
        $order->load('items.product');
        $cashMethod = PaymentMethod::where('code', 'cash')->first();
        $transferMethod = PaymentMethod::where('code', 'transfer')->first();
        $paymentMethods = PaymentMethod::active()->whereNotIn('code', ['cash', 'transfer', 'qris_manual'])->get();
        $settings = $this->settingService->getSettings();

        return view('orders.public-payment', compact('order', 'paymentMethods', 'settings', 'cashMethod', 'transferMethod'));
    }

    public function history(Request $request): View
    {
        Order::where('payment_status', 'pending')
            ->whereNotNull('payment_method')
            ->where('created_at', '<', now()->subMinutes(10))
            ->each(function ($o) {
                $o->update(['payment_status' => 'failed', 'order_status' => 'cancelled']);
            });

        $query = Order::with('items', 'voucher', 'processedBy');

        if (! auth()->user()->isSuperadmin()) {
            $query->where(fn ($q) => $q->where('user_id', auth()->id())->orWhereNull('user_id'));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        if ($paymentMethod = $request->input('payment_method')) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($orderStatus = $request->input('order_status')) {
            $query->where('order_status', $orderStatus);
        }

        if ($processStatus = $request->input('process_status')) {
            match ($processStatus) {
                'unprocessed' => $query->whereNull('processed_by'),
                'processing' => $query->whereNotNull('processed_by')->where('order_status', '!=', 'completed'),
                'completed' => $query->where('order_status', 'completed'),
                default => null,
            };
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        if ($request->boolean('mark_seen')) {
            Order::where('order_number', 'like', 'ORDON-%')->whereNull('seen_at')->update(['seen_at' => now()]);
        }
        $unseenCount = Order::where('order_number', 'like', 'ORDON-%')->whereNull('seen_at')->count();
        $paymentMethods = PaymentMethod::all(['code', 'name']);
        $settings = $this->settingService->getSettings();

        return view('orders.history', compact('orders', 'settings', 'paymentMethods', 'unseenCount'));
    }

    public function process(Order $order): RedirectResponse
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->processed_by) {
            return back()->with('error', 'Pesanan ini sudah diproses oleh ' . ($order->processedBy->name ?? 'kasir lain'));
        }

        $order->update([
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'order_status' => 'confirmed',
        ]);

        return back();
    }

    public function complete(Order $order): RedirectResponse
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if ($order->processed_by !== auth()->id() && ! auth()->user()->isSuperadmin()) {
            abort(403, 'Hanya kasir yang memproses pesanan ini yang dapat menyelesaikannya.');
        }

        if ($order->order_status === 'completed') {
            return back()->with('error', 'Pesanan ini sudah selesai.');
        }

        $order->update([
            'order_status' => 'completed',
        ]);

        return back();
    }

    public function show(Order $order): View
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        $order->load('items.product', 'transaction', 'voucher');
        if (!$order->seen_at) {
            $order->update(['seen_at' => now()]);
        }
        $settings = $this->settingService->getSettings();

        return view('orders.show', compact('order', 'settings'));
    }

    public function createInvoice(Request $request, Order $order): JsonResponse
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Anda tidak memiliki akses ke pesanan ini.'], 403);
        }

        if (in_array($order->payment_status, ['paid', 'success'])) {
            return response()->json(['error' => 'Pesanan ini sudah dibayar.'], 422);
        }

        if (in_array($order->payment_status, ['failed', 'expired']) || $order->order_status === 'cancelled') {
            return response()->json(['error' => 'Pesanan ini sudah tidak dapat diproses.'], 422);
        }

        $activeCodes = PaymentMethod::active()->pluck('code')->toArray();
        if (! in_array('xendit', $activeCodes)) {
            $activeCodes[] = 'xendit';
        }
        $request->validate([
            'payment_method' => 'required|string|in:'.implode(',', $activeCodes),
        ]);

        if ($order->payment_status === 'pending' && $order->created_at->diffInMinutes(now()) > 10) {
            $order->update(['payment_status' => 'failed', 'order_status' => 'cancelled']);
            return response()->json(['error' => 'Pesanan telah kadaluwarsa.'], 422);
        }

        if (in_array($request->payment_method, ['cash', 'transfer'])) {
            if (auth()->check() && !$request->input('is_public')) {
                DB::transaction(function () use ($order, $request) {
                    $lockedOrder = Order::withoutGlobalScopes()->where('id', $order->id)->lockForUpdate()->firstOrFail();
                    $lockedOrder->update([
                        'payment_method' => $request->payment_method,
                        'payment_status' => 'paid',
                        'order_status' => 'processing',
                    ]);
                    $lockedOrder->load('items');
                    $this->paymentService->createSaleFromOrder($lockedOrder);
                });

                return response()->json([
                    'success' => true,
                    'paid_directly' => true,
                    'payment_method' => $request->payment_method,
                ]);
            }

            $order->update([
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'is_direct' => true,
                'payment_method' => $request->payment_method,
            ]);
        }

        if ($request->payment_method === 'qris_manual') {
            $order->update([
                'payment_method' => 'qris_manual',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'is_direct' => true,
                'payment_method' => 'qris_manual',
            ]);
        }

        try {
            $result = $this->paymentService->createXenditInvoice($order, $request->payment_method);

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat invoice pembayaran.'], 500);
        }
    }

    public function publicStatus(string $xenditId): JsonResponse
    {
        $result = $this->paymentService->checkXenditStatus($xenditId);

        if ($result['status'] === 'not_found') {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    public function publicReceipt(Order $order): View
    {
        $order->load('items', 'voucher', 'transaction');
        $settings = $this->settingService->getSettings();

        return view('receipts.consumer', compact('order', 'settings'));
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->orderService->validateOrderAccess($order);
        $this->orderService->cancelOrder($order);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function getItems(int $id): JsonResponse
    {
        $order = Order::withoutGlobalScopes()->findOrFail($id);
        $this->orderService->validateOrderAccess($order);
        $order->load('items');

        return response()->json($order->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'product_name' => $item->product_name,
            'price' => (float) $item->price,
            'quantity' => $item->quantity,
            'subtotal' => (float) $item->subtotal,
        ]));
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        $products = $this->orderService->getProducts($validated['items']);
        $orderItems = $this->orderService->buildOrderItems($validated['items'], $products);
        $subtotal = collect($orderItems)->sum('subtotal');

        try {
            DB::beginTransaction();

            if ($validated['order_id'] ?? null) {
                $order = Order::findOrFail($validated['order_id']);
                $this->orderService->validateOrderAccess($order);
                $order->items()->delete();
                $order->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'customer_name' => $validated['customer_name'] ?? $order->customer_name,
                    'customer_phone' => $validated['customer_phone'] ?? $order->customer_phone,
                    'notes' => $validated['notes'] ?? $order->notes,
                ]);
            } else {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber('ORDOF'),
                    'user_id' => auth()->id(),
                    'customer_name' => $validated['customer_name'] ?? auth()->user()->name,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'notes' => $validated['notes'] ?? null,
                ]);
            }

            $this->createOrderItems($order, $orderItems);

            DB::commit();

            return response()->json([
                'success' => true,
                'is_update' => isset($validated['order_id']),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger('OrderController@save error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return response()->json(['error' => 'Gagal menyimpan pesanan.'], 500);
        }
    }

    public function savedList(): View
    {
        return view('orders.saved-list-partial', [
            'savedOrders' => $this->orderService->getSavedOrders(),
        ]);
    }
}
