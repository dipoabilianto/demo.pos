<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if ($user->isSuperadmin()) {
            return $next($request);
        }

        $permissionChecks = [];
        $roleChecks = [];

        foreach ($roles as $role) {
            if (str_starts_with($role, 'permission:')) {
                $permissionChecks[] = substr($role, 11);
            } else {
                $roleChecks[] = $role;
            }
        }

        if (!empty($permissionChecks)) {
            foreach ($permissionChecks as $permission) {
                if ($user->hasPermission($permission)) {
                    return $next($request);
                }
            }
        }

        if (!empty($roleChecks)) {
            $userRoleNames = $user->roles->pluck('name')->toArray();

            $superadminOnly = !array_diff($roleChecks, ['superadmin']);

            if (in_array('admin', $userRoleNames) && !$superadminOnly) {
                return $next($request);
            }

            foreach ($roleChecks as $role) {
                if (in_array($role, $userRoleNames)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}
