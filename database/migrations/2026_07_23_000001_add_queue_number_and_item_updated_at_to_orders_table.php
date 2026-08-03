<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('queue_number')->nullable()->after('order_number');
            $table->timestamp('item_updated_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['queue_number', 'item_updated_at']);
        });
    }
};
