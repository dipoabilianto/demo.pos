<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'HEMAT10',
                'type' => 'percentage',
                'value' => 10,
                'min_order' => 50000,
                'max_discount' => 20000,
                'max_uses' => 100,
                'max_uses_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'BARU20',
                'type' => 'percentage',
                'value' => 20,
                'min_order' => 0,
                'max_discount' => 30000,
                'max_uses' => 50,
                'max_uses_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addDays(7),
                'is_active' => true,
            ],
            [
                'code' => 'FLAT10K',
                'type' => 'nominal',
                'value' => 10000,
                'min_order' => 30000,
                'max_discount' => null,
                'max_uses' => 0,
                'max_uses_per_user' => 2,
                'starts_at' => null,
                'expires_at' => null,
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::firstOrCreate(['code' => $voucher['code']], $voucher);
        }
    }
}
