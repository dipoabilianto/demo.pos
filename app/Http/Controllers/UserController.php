<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * A user may only assign roles at a strictly lower tier than their own
     * (superuser > owner > admin > kasir/produksi/gudang). Superuser is exempt.
     */
    private function assertCanAssignRoles(User $authUser, $selectedRoles): ?RedirectResponse
    {
        if ($authUser->isSuperadmin()) {
            return null;
        }

        $tiers = ['superadmin' => 100, 'owner' => 90, 'admin' => 50];
        $assignedTier = $selectedRoles->reduce(fn ($max, $role) => max($max, $tiers[$role->name] ?? 10), 10);

        if ($assignedTier >= $authUser->roleTier()) {
            return back()->withErrors(['roles' => 'Anda tidak dapat memberikan role yang setingkat atau lebih tinggi dari role Anda sendiri.'])->withInput();
        }

        return null;
    }

    public function index(Request $request): View
    {
        $authUser = $request->user();
        $allowedTabs = [];
        if ($authUser->hasPermission('users.view')) $allowedTabs[] = 'users';
        if ($authUser->hasPermission('roles.view')) $allowedTabs[] = 'roles';
        $tab = in_array($request->query('tab', 'users'), $allowedTabs) ? $request->query('tab', 'users') : $allowedTabs[0];

        $query = User::with('roles', 'branch')->where('role', '!=', 'superadmin');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $allRoles = Role::all();
        $roles = Role::withCount('users')->get();
        $permissionModules = config('permissions.modules');
        $branches = Branch::active()->orderBy('name')->get();

        return view('settings.users', compact('tab', 'allowedTabs', 'users', 'allRoles', 'roles', 'permissionModules', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $authUser = $request->user();
        $selectedRoles = Role::whereIn('id', $validated['roles'])->get();

        if ($error = $this->assertCanAssignRoles($authUser, $selectedRoles)) {
            return $error;
        }

        $firstRole = $selectedRoles->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $firstRole?->name ?? 'kasir',
            'permissions' => !empty($validated['permissions']) ? $validated['permissions'] : null,
            'branch_id' => $validated['branch_id'] ?? null,
        ]);

        $user->roles()->attach($validated['roles']);

        return redirect()->route('settings.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', Password::defaults()],
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $authUser = $request->user();

        if (! $authUser->isSuperadmin() && $user->roleTier() >= $authUser->roleTier()) {
            return back()->withErrors('Anda tidak dapat mengubah pengguna yang setingkat atau lebih tinggi dari Anda.');
        }

        $selectedRoles = Role::whereIn('id', $validated['roles'])->get();

        if ($error = $this->assertCanAssignRoles($authUser, $selectedRoles)) {
            return $error;
        }

        $firstRole = $selectedRoles->first();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $firstRole?->name ?? 'kasir',
            'permissions' => !empty($validated['permissions']) ? $validated['permissions'] : null,
            'branch_id' => $validated['branch_id'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->roles()->sync($validated['roles']);

        return redirect()->route('settings.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors('Anda tidak dapat menghapus akun sendiri.');
        }

        $authUser = auth()->user();
        if (! $authUser->isSuperadmin() && $user->roleTier() >= $authUser->roleTier()) {
            return back()->withErrors('Anda tidak dapat menghapus pengguna yang setingkat atau lebih tinggi dari Anda.');
        }

        $superadminCount = User::where('role', 'superadmin')->count();
        $adminCount = User::where('role', 'admin')->count();

        if ($superadminCount <= 1 && $user->role === 'superadmin') {
            return back()->withErrors('Setidaknya harus ada satu superadmin.');
        }

        if ($adminCount <= 1 && $user->role === 'admin') {
            return back()->withErrors('Setidaknya harus ada satu admin.');
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('settings.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
