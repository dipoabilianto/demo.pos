<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::firstOrCreate(
            ['key' => 'attendance_radius_mode'],
            ['value' => 'warning']
        );
    }

    public function down(): void
    {
        Setting::where('key', 'attendance_radius_mode')->delete();
    }
};
