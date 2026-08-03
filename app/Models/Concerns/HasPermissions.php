<?php

namespace App\Models\Concerns;

trait HasPermissions
{
    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canAttendance(): bool
    {
        return $this->hasPermission('attendances.check-in');
    }

    public function getEffectivePermissions(): array
    {
        if ($this->isSuperadmin()) {
            $all = [];
            foreach (config('permissions.modules') as $module) {
                foreach ($module['permissions'] as $perm) {
                    $all[] = $perm['key'];
                }
            }

            return $all;
        }

        $rolePermissions = [];
        foreach ($this->roles as $role) {
            $rolePermissions = array_merge($rolePermissions, $role->permissions ?? []);
        }

        $userOverride = $this->permissions ?? [];

        return array_unique(array_merge($rolePermissions, $userOverride));
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        $effective = $this->getEffectivePermissions();

        foreach ($effective as $p) {
            if (str_ends_with($p, '.*')) {
                $modulePrefix = substr($p, 0, -2);
                if (str_starts_with($permission, $modulePrefix . '.')) {
                    return true;
                }
            }
            if ($p === $permission) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }
}
