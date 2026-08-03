<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private SettingService $settingService,
    ) {}
    public function index(Request $request): View
    {
        $query = Sale::with('items.product');

        $query->dateRange($request->date_from, $request->date_to);
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', "%{$request->search}%")
                  ->orWhereHas('items', function ($iq) use ($request) {
                      $iq->where('product_name', 'like', "%{$request->search}%");
                  });
            });
        }

        $sales = $query->latest()->paginate(20);
        $totalRevenue = Sale::sum('total');

        return view('sales.index', compact('sales', 'totalRevenue'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->where(function ($q) {
            $q->where('stock', '>', 0)->orWhere('is_unlimited', true);
        })->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cash,transfer,xendit',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $productIds = collect($validated['items'])->pluck('product_id');
            $lockedProducts = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $saleItems = [];

            foreach ($validated['items'] as $item) {
                $product = $lockedProducts->get($item['product_id']);
                if (! $product) {
                    throw new \Exception("Produk dengan ID {$item['product_id']} tidak ditemukan.");
                }
                if (! $product->isUnlimited() && $product->stock < $item['quantity']) {
                    return back()->withErrors("Stok {$product->name} tidak mencukupi.");
                }
                $effectivePrice = $product->sale_price ?? $product->price;
                $itemSubtotal = $effectivePrice * $item['quantity'];
                $subtotal += $itemSubtotal;
                $saleItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'price' => $effectivePrice,
                ];
            }

            $settings = $this->settingService->getSettings();
            $discount = min($validated['discount'] ?? 0, $subtotal);
            $tax = 0;
            if ($settings['tax_enabled'] ?? false) {
                $rate = ($settings['tax_rate'] ?? 0) / 100;
                if (($settings['tax_type'] ?? 'exclude') === 'include') {
                    $tax = $subtotal - ($subtotal / (1 + $rate));
                } else {
                    $tax = $subtotal * $rate;
                }
            }
            $total = $subtotal - $discount + $tax;
            $paidAmount = $validated['paid_amount'];
            $changeAmount = max(0, $paidAmount - $total);

            $sale = Sale::create([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $paidAmount >= $total ? 'paid' : 'partial',
                'notes' => $validated['notes'] ?? null,
                'branch_id' => session('branch_id'),
            ]);

            foreach ($saleItems as $item) {
                $product = $item['product'];
                SaleItem::create([
                    'sale_id' => $sale->id,
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

            DB::commit();

            return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function show(Sale $sale): View
    {
        $sale->load('items.product', 'transaction');
        return view('sales.show', compact('sale'));
    }
}