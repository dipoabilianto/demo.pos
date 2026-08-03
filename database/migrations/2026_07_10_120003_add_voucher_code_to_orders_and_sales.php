<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('voucher_code', 50)->nullable()->after('voucher_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('discount')->constrained()->nullOnDelete();
            $table->string('voucher_code', 50)->nullable()->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('voucher_code');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('voucher_code');
        });
    }
};
