<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('payment_method')->default('xendit');
            $table->string('payment_status')->default('pending');
            $table->string('order_status')->default('pending');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};