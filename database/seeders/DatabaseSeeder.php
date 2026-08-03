<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $businessType = BusinessType::firstOrCreate(
            ['slug' => 'kedai-kopi'],
            ['name' => 'Kedai Kopi', 'description' => 'Kopi, minuman, dan makanan ringan', 'is_active' => true]
        );

        $branch = Branch::firstOrCreate(
            ['slug' => 'cabang-utama'],
            ['name' => 'Cabang Utama', 'is_active' => true, 'is_online' => true]
        );
        $branch->businessTypes()->syncWithoutDetaching([$businessType->id]);

        $roles = Role::all()->keyBy('name');

        $superadmin = User::firstOrCreate(['email' => 'superadmin@oribun.app'], [
            'name' => 'Superadmin',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);
        if ($superadmin->wasRecentlyCreated && $roles->has('superadmin')) {
            $superadmin->roles()->attach($roles['superadmin']);
        }

        $admin = User::firstOrCreate(['email' => 'admin@oribun.app'], [
            'name' => 'Admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        if ($admin->wasRecentlyCreated && $roles->has('admin')) {
            $admin->roles()->attach($roles['admin']);
        }

        $userKasir = User::firstOrCreate(['email' => 'kasir@oribun.app'], [
            'name' => 'Kasir',
            'password' => bcrypt('kasir123'),
            'role' => 'kasir',
        ]);
        if ($userKasir->wasRecentlyCreated && $roles->has('kasir')) {
            $userKasir->roles()->attach($roles['kasir']);
        }

        $userProduksi = User::firstOrCreate(['email' => 'produksi@oribun.app'], [
            'name' => 'Produksi',
            'password' => bcrypt('produksi123'),
            'role' => 'produksi',
        ]);
        if ($userProduksi->wasRecentlyCreated && $roles->has('produksi')) {
            $userProduksi->roles()->attach($roles['produksi']);
        }

        $userGudang = User::firstOrCreate(['email' => 'gudang@oribun.app'], [
            'name' => 'Gudang',
            'password' => bcrypt('gudang123'),
            'role' => 'gudang',
        ]);
        if ($userGudang->wasRecentlyCreated && $roles->has('gudang')) {
            $userGudang->roles()->attach($roles['gudang']);
        }

        // Test users with multi-role
        $multiKasirGudang = User::firstOrCreate(['email' => 'kasirgudang@oribun.app'], [
            'name' => 'Kasir Gudang',
            'password' => bcrypt('kasirgudang'),
            'role' => 'kasir',
        ]);
        if ($multiKasirGudang->wasRecentlyCreated) {
            if ($roles->has('kasir')) $multiKasirGudang->roles()->attach($roles['kasir']);
            if ($roles->has('gudang')) $multiKasirGudang->roles()->attach($roles['gudang']);
        }

        $multiKasirProduksi = User::firstOrCreate(['email' => 'kasirproduksi@oribun.app'], [
            'name' => 'Kasir Produksi',
            'password' => bcrypt('kasirproduksi'),
            'role' => 'kasir',
        ]);
        if ($multiKasirProduksi->wasRecentlyCreated) {
            if ($roles->has('kasir')) $multiKasirProduksi->roles()->attach($roles['kasir']);
            if ($roles->has('produksi')) $multiKasirProduksi->roles()->attach($roles['produksi']);
        }

        // Migrate existing users that might not have role_user pivot yet
        $this->migrateExistingUsers();

        $paymentMethods = [
            ['code' => 'BCA', 'name' => 'BCA Virtual Account', 'description' => 'Virtual Account', 'group' => 'virtual_account'],
            ['code' => 'BNI', 'name' => 'BNI Virtual Account', 'description' => 'Virtual Account', 'group' => 'virtual_account'],
            ['code' => 'BRI', 'name' => 'BRI Virtual Account', 'description' => 'Virtual Account', 'group' => 'virtual_account'],
            ['code' => 'MANDIRI', 'name' => 'Mandiri Bill Payment', 'description' => 'Virtual Account', 'group' => 'virtual_account'],
            ['code' => 'OVO', 'name' => 'OVO', 'description' => 'E-Wallet', 'group' => 'ewallet'],
            ['code' => 'DANA', 'name' => 'DANA', 'description' => 'E-Wallet', 'group' => 'ewallet'],
            ['code' => 'SHOPEEPAY', 'name' => 'ShopeePay', 'description' => 'E-Wallet', 'group' => 'ewallet'],
            ['code' => 'QRIS', 'name' => 'QRIS', 'description' => 'QRIS', 'group' => 'qris'],
            ['code' => 'qris_manual', 'name' => 'QRIS Manual', 'description' => 'Scan QRIS', 'group' => 'qris_manual'],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(['code' => $method['code']], $method);
        }

        $kopi = Category::firstOrCreate(['name' => 'Kopi'], ['description' => 'Kopi spesial dari biji pilihan']);
        $nonKopi = Category::firstOrCreate(['name' => 'Non-Kopi'], ['description' => 'Minuman non-kopi yang tak kalah nikmat']);
        $makananRingan = Category::firstOrCreate(['name' => 'Makanan Ringan'], ['description' => 'Teman ngopi favorit']);
        $minumanSegar = Category::firstOrCreate(['name' => 'Minuman Segar'], ['description' => 'Minuman segar pelepas dahaga']);

        $products = [
            ['category_id' => $kopi->id, 'name' => 'Espresso', 'sku' => 'ESP-001', 'description' => 'Shot kopi pekat dengan crema sempurna, dibuat dari biji arabika pilihan.', 'price' => 20000, 'cost_price' => 12000, 'image' => 'products/ESP-001.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Americano', 'sku' => 'AMR-002', 'description' => 'Espresso dengan air panas, menghasilkan rasa kopi yang bold dan halus.', 'price' => 22000, 'cost_price' => 13000, 'image' => 'products/AMR-002.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Cappuccino', 'sku' => 'CAP-003', 'description' => 'Perpaduan espresso, steamed milk, dan foam susu yang tebal dan lembut.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'products/CAP-003.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Cafe Latte', 'sku' => 'LAT-004', 'description' => 'Espresso dicampur steamed milk creamy dengan lapisan foam tipis di atasnya.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'products/LAT-004.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Caramel Macchiato', 'sku' => 'MCH-005', 'description' => 'Layered vanilla-steamed milk dengan espresso dan saus caramel manis.', 'price' => 32000, 'cost_price' => 19000, 'image' => 'products/MCH-005.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Mocha', 'sku' => 'MOC-006', 'description' => 'Perpaduan sempurna antara espresso, coklat Belgian, dan steamed milk.', 'price' => 30000, 'cost_price' => 18000, 'image' => 'products/MOC-006.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Cold Brew', 'sku' => 'CLD-007', 'description' => 'Kopi cold brew yang diseduh 12 jam, menghasilkan rasa smooth dan rendah asam.', 'price' => 25000, 'cost_price' => 15000, 'image' => 'products/CLD-007.jpg', 'is_unlimited' => true],
            ['category_id' => $kopi->id, 'name' => 'Vanilla Latte', 'sku' => 'VNL-008', 'description' => 'Cafe Latte dengan sentuhan sirup vanilla pilihan yang harum dan manis.', 'price' => 30000, 'cost_price' => 18000, 'image' => 'products/VNL-008.jpg', 'is_unlimited' => true],
            ['category_id' => $nonKopi->id, 'name' => 'Matcha Latte', 'sku' => 'MCH-009', 'description' => 'Green tea bubuk premium dari Jepang dicampur steamed milk yang creamy.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'products/MCH-009.jpg', 'is_unlimited' => true],
            ['category_id' => $nonKopi->id, 'name' => 'Taro Latte', 'sku' => 'TRO-010', 'description' => 'Minuman taro bubuk premium dengan steamed milk, rasa manis dan legit.', 'price' => 26000, 'cost_price' => 16000, 'image' => 'products/TRO-010.jpg', 'is_unlimited' => true],
            ['category_id' => $nonKopi->id, 'name' => 'Red Velvet Latte', 'sku' => 'RVL-011', 'description' => 'Minuman red velvet creamy dengan topping whipped cream dan crumble.', 'price' => 28000, 'cost_price' => 17000, 'image' => 'products/RVL-011.jpg', 'is_unlimited' => true],
            ['category_id' => $nonKopi->id, 'name' => 'Coklat Panas', 'sku' => 'CHC-012', 'description' => 'Coklat bubuk Belgian asli dengan steamed milk, hangat dan memanjakan.', 'price' => 22000, 'cost_price' => 14000, 'image' => 'products/CHC-012.jpg', 'is_unlimited' => true],
            ['category_id' => $makananRingan->id, 'name' => 'Croissant', 'sku' => 'CRS-013', 'description' => 'Croissant panggang renyah dengan lapisan mentega asli Prancis.', 'price' => 18000, 'cost_price' => 11000, 'min_stock' => 5, 'image' => 'products/CRS-013.jpg', 'is_unlimited' => true],
            ['category_id' => $makananRingan->id, 'name' => 'Banana Cake', 'sku' => 'BNC-014', 'description' => 'Banana cake homemade lembut dengan potongan pisang asli dan walnut.', 'price' => 15000, 'cost_price' => 9000, 'min_stock' => 5, 'image' => 'products/BNC-014.jpg', 'is_unlimited' => true],
            ['category_id' => $minumanSegar->id, 'name' => 'Air Mineral', 'sku' => 'AIR-015', 'description' => 'Air mineral kemasan 330ml dari sumber mata air pegunungan.', 'price' => 5000, 'cost_price' => 3000, 'image' => 'products/AIR-015.jpg', 'is_unlimited' => true],
            ['category_id' => $minumanSegar->id, 'name' => 'Lemon Tea', 'sku' => 'LMT-016', 'description' => 'Teh hitam segar dengan perasan lemon asli dan sentuhan madu.', 'price' => 8000, 'cost_price' => 4000, 'image' => 'products/LMT-016.jpg', 'is_unlimited' => true],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['sku' => $product['sku']], $product);
        }

        $this->call(RawMaterialSeeder::class);
        $this->call(VoucherSeeder::class);
    }

    private function migrateExistingUsers(): void
    {
        $roles = Role::all()->keyBy('name');
        $users = User::whereDoesntHave('roles')->get();

        foreach ($users as $user) {
            if ($roles->has($user->role)) {
                $user->roles()->attach($roles[$user->role]);
            }
        }
    }
}
