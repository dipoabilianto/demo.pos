<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        if (Storage::disk('local')->exists('settings.json')) {
            $data = json_decode(Storage::disk('local')->get('settings.json'), true) ?: [];
            $insert = [];
            foreach ($data as $key => $value) {
                $insert[] = [
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($insert)) {
                DB::table('settings')->insert($insert);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
