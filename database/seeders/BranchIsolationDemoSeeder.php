<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Voucher;
use App\Services\SettingService;
use Illuminate\Database\Seeder;

/**
 * Not part of the default DatabaseSeeder — run on demand with:
 *   php artisan db:seed --class=BranchIsolationDemoSeeder
 *
 * Creates two branches with deliberately distinct products, promos,
 * vouchers, raw materials, and a sale each, so switching between them
 * in the UI makes any branch-scoping regression immediately visible:
 * if a "Cabang B" product/promo/voucher/sale ever shows up while
 * "Cabang A" is active (or vice versa), something broke
 * BranchScope/ProductBranchScope or a branch_id stamp on create.
 */
class BranchIsolationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $businessType = BusinessType::firstOrCreate(
            ['slug' => 'kedai-kopi'],
            ['name' => 'Kedai Kopi', 'is_active' => true]
        );

        $branchA = Branch::firstOrCreate(
            ['slug' => 'demo-isolasi-a'],
            ['name' => 'Demo Isolasi Cabang A', 'is_active' => true, 'is_online' => true]
        );
        $branchB = Branch::firstOrCreate(
            ['slug' => 'demo-isolasi-b'],
            ['name' => 'Demo Isolasi Cabang B', 'is_active' => true, 'is_online' => true]
        );
        $branchA->businessTypes()->syncWithoutDetaching([$businessType->id]);
        $branchB->businessTypes()->syncWithoutDetaching([$businessType->id]);

        $this->seedBranch($branchA, 'A');
        $this->seedBranch($branchB, 'B');

        $this->command?->info("Demo branches ready: #{$branchA->id} {$branchA->name}, #{$branchB->id} {$branchB->name}");
        $this->command?->info('Switch between them in the UI — products, promotions, vouchers, raw materials, and today\'s sales total should never overlap.');
    }

    private function seedBranch(Branch $branch, string $label): void
    {
        $product = Product::withoutGlobalScopes()->firstOrCreate(
            ['sku' => "DEMO-{$label}-001"],
            [
                'name' => "Produk Eksklusif Cabang {$label}",
                'price' => 30000,
                'cost_price' => 18000,
                'stock' => 50,
                'min_stock' => 5,
                'is_active' => true,
                'branch_id' => $branch->id,
            ]
        );

        RawMaterial::withoutGlobalScopes()->firstOrCreate(
            ['name' => "Bahan Eksklusif Cabang {$label}"],
            ['unit' => 'kg', 'current_stock' => 25, 'min_stock' => 5, 'branch_id' => $branch->id]
        );

        Voucher::withoutGlobalScopes()->firstOrCreate(
            ['code' => "DEMO{$label}10"],
            [
                'type' => 'percentage',
                'value' => 10,
                'min_order' => 0,
                'max_uses' => 0,
                'max_uses_per_user' => 0,
                'is_active' => true,
                'branch_id' => $branch->id,
            ]
        );

        $settingService = app(SettingService::class);
        $settings = $settingService->getSettings($branch->id);
        $settings['promotions'] = [[
            'id' => 1,
            'title' => "Promo Khusus Cabang {$label}",
            'description' => "Promo ini hanya tampil saat Cabang {$label} aktif.",
            'link' => '',
            'active' => true,
        ]];
        $settingService->saveSettings($settings, $branch->id);

        if (! Sale::withoutGlobalScopes()->where('notes', "Demo isolasi cabang {$label}")->exists()) {
            $sale = Sale::withoutGlobalScopes()->create([
                'subtotal' => $product->price,
                'discount' => 0,
                'tax' => 0,
                'total' => $product->price,
                'paid_amount' => $product->price,
                'change_amount' => 0,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
                'notes' => "Demo isolasi cabang {$label}",
                'branch_id' => $branch->id,
            ]);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
            ]);
        }
    }
}
