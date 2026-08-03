<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_methods')->insertOrIgnore([
            [
                'code' => 'cash',
                'name' => 'Tunai',
                'description' => 'Bayar langsung',
                'group' => 'offline',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'transfer',
                'name' => 'Transfer',
                'description' => 'Bayar langsung',
                'group' => 'offline',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('payment_methods')->whereIn('code', ['cash', 'transfer'])->delete();
    }
};
