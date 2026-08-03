<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $newPermissions = ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'];

    public function up(): void
    {
        $role = Role::where('name', 'admin')->first();

        if (! $role) {
            return;
        }

        $role->permissions = array_values(array_unique(array_merge(
            $role->permissions ?? [],
            $this->newPermissions
        )));
        $role->save();
    }

    public function down(): void
    {
        $role = Role::where('name', 'admin')->first();

        if (! $role) {
            return;
        }

        $role->permissions = array_values(array_diff($role->permissions ?? [], $this->newPermissions));
        $role->save();
    }
};
