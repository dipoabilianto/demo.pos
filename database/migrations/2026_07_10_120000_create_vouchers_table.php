<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('type', 20); // percentage / nominal
            $table->decimal('value', 15, 2);
            $table->decimal('min_order', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->integer('max_uses')->default(0); // 0 = unlimited
            $table->integer('max_uses_per_user')->default(0); // 0 = unlimited
            $table->integer('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
