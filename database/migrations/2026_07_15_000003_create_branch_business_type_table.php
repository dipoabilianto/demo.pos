<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_business_type', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_type_id')->constrained()->cascadeOnDelete();
            $table->primary(['branch_id', 'business_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_business_type');
    }
};
