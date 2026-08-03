<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(BranchProductSeeder::class);

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
