<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->update(['name' => DB::raw('UPPER(name)')]);
    }

    public function down(): void
    {
        // Tidak bisa revert karena data sudah di-uppercase
    }
};
