<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('xendit_id')->nullable()->unique();
            $table->string('type'); // 'sale' or 'expense'
            $table->morphs('transactionable');
            $table->string('payment_channel')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('external_id')->nullable();
            $table->json('xendit_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};