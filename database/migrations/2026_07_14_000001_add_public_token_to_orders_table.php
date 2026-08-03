<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 36)->nullable()->unique()->after('id');
        });

        $orders = DB::table('orders')->whereNull('public_token')->get();
        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update([
                'public_token' => (string) Str::uuid(),
            ]);
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('public_token', 36)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('public_token');
        });
    }
};
