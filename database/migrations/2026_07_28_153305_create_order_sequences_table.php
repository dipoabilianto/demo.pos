<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_sequences', function (Blueprint $table) {
            $table->string('prefix', 10);
            $table->string('date_prefix', 8);
            $table->integer('last_sequence')->default(0);
            $table->primary(['prefix', 'date_prefix']);
        });

        $prefixes = ['ORDOF', 'ORDON'];
        foreach ($prefixes as $prefix) {
            $rows = DB::table('orders')
                ->where('order_number', 'like', $prefix . '-%')
                ->get(['order_number']);

            $maxByDate = [];
            foreach ($rows as $row) {
                $parts = explode('-', $row->order_number);
                if (count($parts) < 3) {
                    continue;
                }
                $datePrefix = $parts[1];
                $seq = (int) $parts[2];
                $key = $prefix . '-' . $datePrefix;
                if (!isset($maxByDate[$key]) || $seq > $maxByDate[$key]) {
                    $maxByDate[$key] = $seq;
                }
            }

            foreach ($maxByDate as $key => $seq) {
                $datePrefix = explode('-', $key)[1];
                DB::table('order_sequences')->insert([
                    'prefix' => $prefix,
                    'date_prefix' => $datePrefix,
                    'last_sequence' => $seq,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_sequences');
    }
};
