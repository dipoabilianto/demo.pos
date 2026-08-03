<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $config = config('permissions.role_defaults');

        $roleData = [
            [
                'name' => 'superadmin',
                'label' => 'Superadmin',
                'description' => 'Akses penuh ke semua fitur, termasuk keamanan dan metode pembayaran',
                'permissions' => [],
            ],
            [
                'name' => 'admin',
                'label' => 'Admin',
                'description' => 'Mengelola produk, kategori, voucher, pengaturan, dan pengguna',
                'permissions' => $this->expandPermissions($config['admin']),
            ],
            [
                'name' => 'kasir',
                'label' => 'Kasir',
                'description' => 'Melayani penjualan, pesanan, pembayaran, dan mencetak resi',
                'permissions' => $this->expandPermissions($config['kasir']),
            ],
            [
                'name' => 'produksi',
                'label' => 'Dapur/Produksi',
                'description' => 'Mengelola produk dan kategori untuk produksi',
                'permissions' => $this->expandPermissions($config['produksi']),
            ],
            [
                'name' => 'gudang',
                'label' => 'Gudang',
                'description' => 'Mengelola pengeluaran, bahan baku, dan stok opname',
                'permissions' => $this->expandPermissions($config['gudang']),
            ],
            [
                'name' => 'owner',
                'label' => 'Owner',
                'description' => 'Melihat laporan, dashboard owner, dan pengaturan toko',
                'permissions' => $this->expandPermissions($config['owner']),
            ],
        ];

        foreach ($roleData as $data) {
            Role::firstOrCreate(['name' => $data['name']], $data);
        }
    }

    private function expandPermissions(array $patterns): array
    {
        $allPermissions = [];
        foreach (config('permissions.modules') as $module) {
            foreach ($module['permissions'] as $perm) {
                $allPermissions[] = $perm['key'];
            }
        }

        $result = [];
        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '.*')) {
                $prefix = substr($pattern, 0, -2);
                foreach ($allPermissions as $p) {
                    if (str_starts_with($p, $prefix . '.')) {
                        $result[] = $p;
                    }
                }
            } else {
                $result[] = $pattern;
            }
        }

        return array_unique($result);
    }
}
