<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $newPermissions = ['shifts.view', 'shifts.create', 'shifts.edit'];

    public function up(): void
    {
        foreach (['admin', 'owner'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $role->permissions = array_values(array_unique(array_merge(
                $role->permissions ?? [],
                $this->newPermissions
            )));
            $role->save();
        }
    }

    public function down(): void
    {
        foreach (['admin', 'owner'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $role->permissions = array_values(array_diff($role->permissions ?? [], $this->newPermissions));
            $role->save();
        }
    }
};
