<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Role::where('name', 'superadmin')->update(['label' => 'Superuser']);
    }

    public function down(): void
    {
        Role::where('name', 'superadmin')->update(['label' => 'Superadmin']);
    }
};
