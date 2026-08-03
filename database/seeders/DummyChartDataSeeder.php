<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyChartDataSeeder extends Seeder
{
    private array $products = [];

    public function run(): void
    {
        $this->products = Product::where('is_active', true)->pluck('name', 'id')->toArray();
        $branches = Branch::active()->pluck('id');

        if (empty($this->products) || $branches->isEmpty()) {
            $this->command->error('Butuh minimal 1 produk & 1 cabang aktif.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        SaleItem::query()->delete();
        Sale::query()->delete();
        Expense::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $productIds = array_keys($this->products);
        $today = now()->startOfDay();

        foreach ($branches as $branchId) {
            $dailyOrderBase = match ((int) $branchId) {
                1 => 5,
                2 => 2,
                3 => 3,
                default => 2,
            };

            for ($day = 6; $day >= 0; $day--) {
                $date = (clone $today)->subDays($day);
                $numOrders = $dailyOrderBase + random_int(-1, 1);

                for ($o = 0; $o < $numOrders; $o++) {
                    $hour = random_int(8, 17);
                    $minute = random_int(0, 59);
                    $second = random_int(0, 59);
                    $createdAt = (clone $date)->setTime($hour, $minute, $second);

                    $itemsCount = random_int(1, 4);
                    $subtotal = 0;
                    $items = [];

                    for ($i = 0; $i < $itemsCount; $i++) {
                        $pid = $productIds[array_rand($productIds)];
                        $qty = random_int(1, 3);
                        $product = Product::find($pid);
                        if (! $product) continue;
                        $price = (float) ($product->sale_price ?: $product->price);
                        $items[] = [
                            'product_id' => $pid,
                            'product_name' => $product->name,
                            'price' => $price,
                            'quantity' => $qty,
                            'subtotal' => $price * $qty,
                        ];
                        $subtotal += $price * $qty;
                    }

                    if (empty($items)) continue;

                    $discount = random_int(0, 1) ? round($subtotal * random_int(0, 15) / 100, -2) : 0;
                    $total = $subtotal - $discount;

                    $sale = Sale::create([
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax' => 0,
                        'total' => $total,
                        'paid_amount' => $total,
                        'change_amount' => 0,
                        'payment_method' => ['cash', 'transfer', 'qris'][array_rand(['cash', 'transfer', 'qris'])],
                        'payment_status' => 'paid',
                        'branch_id' => $branchId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    foreach ($items as $item) {
                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'product_id' => $item['product_id'],
                            'product_name' => $item['product_name'],
                            'price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'subtotal' => $item['subtotal'],
                        ]);
                    }
                }

                $numExpenses = random_int(1, 2);
                $categories = ['Bahan Baku', 'Listrik', 'Air', 'Gas', 'Kebersihan', 'Transport', 'Lainnya'];
                for ($e = 0; $e < $numExpenses; $e++) {
                    $hour = random_int(6, 18);
                    $minute = random_int(0, 59);
                    $expenseDate = (clone $date)->setTime($hour, $minute, 0);

                    Expense::create([
                        'title' => $categories[array_rand($categories)],
                        'description' => 'Pengeluaran ' . $categories[array_rand($categories)],
                        'amount' => random_int(2, 30) * 10000,
                        'category' => $categories[array_rand($categories)],
                        'expense_date' => $expenseDate,
                        'branch_id' => $branchId,
                    ]);
                }
            }
        }

        $saleCount = Sale::count();
        $expenseCount = Expense::count();
        $itemCount = SaleItem::count();
        $this->command->info("Data dummy berhasil dibuat: {$saleCount} penjualan, {$itemCount} item, {$expenseCount} pengeluaran.");
    }
}
