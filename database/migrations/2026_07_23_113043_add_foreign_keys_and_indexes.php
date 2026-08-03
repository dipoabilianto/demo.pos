<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('payment_status');
            $table->index('order_status');
            $table->index(['payment_status', 'order_status']);
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('payment_status');
            $table->index('created_at');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->index('sale_id');
            $table->index('product_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('type');
            $table->index('status');
            $table->index('transactionable_type');
            $table->index('transactionable_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('branch_id');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->index('code');
            $table->index('branch_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date');
            $table->index('category');
            $table->index('branch_id');
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index('type');
            $table->index('raw_material_id');
            $table->index('created_at');
        });

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->index('branch_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('branch_id');
        });


        Schema::table('branch_product', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['payment_status', 'order_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['sale_id']);
            $table->dropIndex(['product_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['transactionable_type']);
            $table->dropIndex(['transactionable_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['branch_id']);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['branch_id']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
            $table->dropIndex(['category']);
            $table->dropIndex(['branch_id']);
        });

        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['raw_material_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['branch_id']);
        });


        Schema::table('branch_product', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
        });
    }
};
