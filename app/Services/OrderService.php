<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function getCatalogProducts(): Collection
    {
        return Cache::remember('catalog_products', 60, function () {
            return Product::with('category')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->where('stock', '>', 0)->orWhere('is_unlimited', true);
                })
                ->get()
                ->groupBy(fn ($p) => $p->category->name ?? 'Lainnya');
        });
    }

    public function getPublicCatalogProducts(Branch $branch): Collection
    {
        $key = "public_products_{$branch->id}";

        $cached = Cache::get($key);
        if ($cached instanceof Collection) {
            return $cached;
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('stock', '>', 0)->orWhere('is_unlimited', true);
            })
            ->where(function ($q) use ($branch) {
                $q->where('branch_id', $branch->id)
                    ->orWhereHas('branches', fn ($q) => $q->where('branch_product.branch_id', $branch->id));
            })
            ->get()
            ->groupBy(fn ($p) => $p->category->name ?? 'Lainnya');

        Cache::put($key, $products, 60);

        return $products;
    }

    public function getSavedOrders(): Collection
    {
        return Order::with('items')
            ->where('user_id', auth()->id())
            ->where('order_status', 'pending')
            ->where('payment_status', 'pending')
            ->oldest('created_at')
            ->get();
    }

    public function getProducts(array $items): Collection
    {
        $productIds = collect($items)->pluck('product_id');
        return Product::whereIn('id', $productIds)->get()->keyBy('id');
    }

    public function buildOrderItems(array $items, Collection $products): array
    {
        $orderItems = [];
        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            if (! $product) {
                throw new \InvalidArgumentException('Produk tidak ditemukan.');
            }
            if (! $product->isUnlimited() && $product->stock < $item['quantity']) {
                throw new \InvalidArgumentException('Stok produk tidak mencukupi.');
            }
            $effectivePrice = $product->sale_price ?? $product->price;
            $orderItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $effectivePrice * $item['quantity'],
                'price' => $effectivePrice,
            ];
        }
        return $orderItems;
    }

    public function createOrderItems(Order $order, array $orderItems, bool $adjustStock = true): void
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
            if ($adjustStock && ! $product->isUnlimited()) {
                $affected = Product::where('id', $product->id)
                    ->where('stock', '>=', $item['quantity'])
                    ->decrement('stock', $item['quantity']);
                if ($affected === 0) {
                    throw new \RuntimeException("Stok {$product->name} tidak mencukupi.");
                }
            }
        }
    }

    public function clearCartCookie(): void
    {
        cookie()->queue(cookie()->forget('cart'));
    }

    public function applyHistoryFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): void
    {
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
            if ($paymentStatus === 'lunas') {
                $query->whereIn('payment_status', ['paid', 'success']);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }
        if ($orderStatus = $request->input('order_status')) {
            $query->where('order_status', $orderStatus);
        }
    }

    public function getStatusCounts(): array
    {
        $today = today();
        return [
            'pending' => Order::where('order_status', 'pending')->whereDate('created_at', $today)->count(),
            'confirmed' => Order::where('order_status', 'confirmed')->whereDate('created_at', $today)->count(),
            'completed' => Order::where('order_status', 'completed')->whereDate('created_at', $today)->count(),
        ];
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['payment_status' => 'failed']);
            try {
                $order->transitionStatus('cancelled');
            } catch (\InvalidArgumentException) {
            }
        });
    }

    public function validateOrderAccess(Order $order): void
    {
        if (! auth()->user()->isSuperadmin() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }
    }

    public function processVoucher(string $code, Branch $branch, float $subtotal, string $customerIdentifier): array
    {
        $voucher = Voucher::where('code', $code)
            ->where(function ($q) use ($branch) {
                $q->where('branch_id', $branch->id)
                  ->orWhereNull('branch_id');
            })
            ->lockForUpdate()->first();

        if (! $voucher) {
            throw new \InvalidArgumentException('Kode voucher tidak valid.');
        }

        if (! $voucher->isValidFor($subtotal, $customerIdentifier)) {
            throw new \InvalidArgumentException('Kode voucher tidak dapat digunakan.');
        }

        return [
            'voucher' => $voucher,
            'discount' => $voucher->calculateDiscount($subtotal),
            'voucher_id' => $voucher->id,
        ];
    }
}
