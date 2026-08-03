<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $additions = [
            'admin' => ['attendances.check-in', 'attendances.report'],
            'owner' => ['attendances.check-in', 'attendances.report'],
            'kasir' => ['attendances.check-in'],
            'produksi' => ['attendances.check-in'],
            'gudang' => ['attendances.check-in'],
        ];

        foreach ($additions as $roleName => $newPermissions) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $role->permissions = array_values(array_unique(array_merge(
                $role->permissions ?? [],
                $newPermissions
            )));
            $role->save();
        }
    }

    public function down(): void
    {
        $removals = ['attendances.check-in', 'attendances.report'];

        foreach (['admin', 'owner', 'kasir', 'produksi', 'gudang'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $role->permissions = array_values(array_diff($role->permissions ?? [], $removals));
            $role->save();
        }
    }
};
