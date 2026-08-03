<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeds each branch with its own distinct business type, category, and
 * 5 products (with real photos already in storage/app/public/products).
 * Run standalone with:
 *   php artisan db:seed --class=BranchProductSeeder
 * or via the default DatabaseSeeder, which calls it after branches exist.
 */
class BranchProductSeeder extends Seeder
{
    public function run(): void
    {
        $coffeeType = BusinessType::firstOrCreate(
            ['slug' => 'kedai-kopi'],
            ['name' => 'Kedai Kopi', 'description' => 'Kopi, minuman, dan makanan ringan', 'is_active' => true]
        );
        $bakeryType = BusinessType::firstOrCreate(
            ['slug' => 'toko-roti-kue'],
            ['name' => 'Toko Roti & Kue', 'description' => 'Roti, kue kering, dan pastry', 'is_active' => true]
        );

        $coffeeCategory = Category::firstOrCreate(
            ['name' => 'Kopi'],
            ['description' => 'Kopi spesial dari biji pilihan']
        );
        $bakeryCategory = Category::firstOrCreate(
            ['name' => 'Roti & Kue'],
            ['description' => 'Roti dan kue panggang segar setiap hari']
        );

        $branchA = Branch::firstOrCreate(
            ['slug' => 'cabang-utama'],
            ['name' => 'Cabang Utama', 'is_active' => true, 'is_online' => true]
        );
        $branchB = Branch::firstOrCreate(
            ['slug' => 'cabang-pulung-kencana'],
            ['name' => 'Cabang Pulung Kencana', 'is_active' => true, 'is_online' => true]
        );

        // Each branch gets exactly one business type — that's what makes
        // its product lineup distinct instead of overlapping.
        $branchA->businessTypes()->sync([$coffeeType->id]);
        $branchB->businessTypes()->sync([$bakeryType->id]);

        $this->seedProducts($branchA->id, $coffeeCategory->id, [
            ['sku' => 'ESP-001', 'name' => 'Espresso', 'description' => 'Shot kopi pekat dengan crema sempurna, dibuat dari biji arabika pilihan.', 'price' => 20000, 'cost_price' => 12000, 'image' => 'ESP-001.jpg'],
            ['sku' => 'CAP-003', 'name' => 'Cappuccino', 'description' => 'Perpaduan espresso, steamed milk, dan foam susu yang tebal dan lembut.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'CAP-003.jpg'],
            ['sku' => 'LAT-004', 'name' => 'Cafe Latte', 'description' => 'Espresso dicampur steamed milk creamy dengan lapisan foam tipis di atasnya.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'LAT-004.jpg'],
            ['sku' => 'MOC-006', 'name' => 'Mocha', 'description' => 'Perpaduan sempurna antara espresso, coklat Belgian, dan steamed milk.', 'price' => 30000, 'cost_price' => 18000, 'image' => 'MOC-006.jpg'],
            ['sku' => 'EKS-017', 'name' => 'Es Kopi Susu', 'description' => 'Kopi susu gula aren, disajikan dingin — favorit sepanjang hari.', 'price' => 22000, 'cost_price' => 13000, 'image' => 'es-kopi-susu.webp'],
        ]);

        $this->seedProducts($branchB->id, $bakeryCategory->id, [
            ['sku' => 'RTA-018', 'name' => 'Roti Abon', 'description' => 'Roti lembut isi abon sapi gurih dengan taburan mayones.', 'price' => 12000, 'cost_price' => 7000, 'image' => 'roti-abon.webp'],
            ['sku' => 'RTC-019', 'name' => 'Roti Coklat Lumer', 'description' => 'Roti empuk dengan isian coklat lumer yang meleleh di setiap gigitan.', 'price' => 13000, 'cost_price' => 7500, 'image' => 'roti-coklat-lumer.webp'],
            ['sku' => 'RTK-020', 'name' => 'Roti Keju Manis', 'description' => 'Roti manis dengan taburan keju parut premium di atasnya.', 'price' => 13000, 'cost_price' => 7500, 'image' => 'roti-keju-manis.webp'],
            ['sku' => 'RTS-021', 'name' => 'Roti Kismis', 'description' => 'Roti klasik dengan kismis pilihan, lembut dan tidak terlalu manis.', 'price' => 11000, 'cost_price' => 6500, 'image' => 'roti-kismis.webp'],
            ['sku' => 'KNS-022', 'name' => 'Kue Nastar', 'description' => 'Kue kering isi selai nanas homemade, renyah dan lumer di mulut.', 'price' => 45000, 'cost_price' => 28000, 'image' => 'kue-nastar.webp'],
        ]);
    }

    private function seedProducts(int $branchId, int $categoryId, array $products): void
    {
        foreach ($products as $data) {
            Product::withoutGlobalScopes()->updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $categoryId,
                    'branch_id' => $branchId,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'cost_price' => $data['cost_price'],
                    'image' => 'products/'.$data['image'],
                    'is_unlimited' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
