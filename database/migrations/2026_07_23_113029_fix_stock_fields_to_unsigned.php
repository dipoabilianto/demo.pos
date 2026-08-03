<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0)->change();
            $table->unsignedInteger('min_stock')->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock')->default(0)->change();
            $table->integer('min_stock')->default(1)->change();
        });
    }
};
