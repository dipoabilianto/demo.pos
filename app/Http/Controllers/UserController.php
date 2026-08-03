<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        $allRoles = Role::all();
        $permissionModules = config('permissions.modules');

        return view('settings.users', compact('users', 'allRoles', 'permissionModules'));
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
        ]);

        $authUser = $request->user();
        $selectedRoles = Role::whereIn('id', $validated['roles'])->get();
        $hasSuperadminRole = $selectedRoles->contains('name', 'superadmin');

        if ($hasSuperadminRole && ! $authUser->isSuperadmin()) {
            return back()->withErrors(['roles' => 'Hanya superadmin yang dapat membuat akun superadmin.'])->withInput();
        }

        $firstRole = $selectedRoles->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $firstRole?->name ?? 'kasir',
            'permissions' => !empty($validated['permissions']) ? $validated['permissions'] : null,
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
        ]);

        $authUser = $request->user();
        $selectedRoles = Role::whereIn('id', $validated['roles'])->get();
        $hasSuperadminRole = $selectedRoles->contains('name', 'superadmin');

        if ($hasSuperadminRole && ! $authUser->isSuperadmin()) {
            return back()->withErrors(['roles' => 'Hanya superadmin yang dapat memberikan role superadmin.'])->withInput();
        }

        $firstRole = $selectedRoles->first();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $firstRole?->name ?? 'kasir',
            'permissions' => !empty($validated['permissions']) ? $validated['permissions'] : null,
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
