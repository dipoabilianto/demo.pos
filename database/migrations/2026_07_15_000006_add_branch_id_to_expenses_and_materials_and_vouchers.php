<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
