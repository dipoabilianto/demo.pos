<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('customer_identifier'); // email or phone
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
    }
};
