<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit', 20);
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
