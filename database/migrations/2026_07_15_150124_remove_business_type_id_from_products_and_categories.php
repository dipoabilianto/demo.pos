<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
            $table->dropColumn('business_type_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['business_type_id']);
            $table->dropColumn('business_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('business_type_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('business_type_id')->nullable()->constrained()->nullOnDelete();
        });
    }
};
