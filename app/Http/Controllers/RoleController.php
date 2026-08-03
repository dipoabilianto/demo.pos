<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('settings.general', ['tab' => 'roles']);
    }

    private function validPermissionKeys(): array
    {
        return collect(config('permissions.modules'))
            ->flatMap(fn ($module) => collect($module['permissions'])->pluck('key'))
            ->all();
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => Rule::in($this->validPermissionKeys()),
        ]);

        $validated['permissions'] = $validated['permissions'] ?? [];

        Role::create($validated);

        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === 'superadmin') {
            return back()->withErrors('Role superadmin tidak dapat diedit.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => Rule::in($this->validPermissionKeys()),
        ]);

        $validated['permissions'] = $validated['permissions'] ?? [];

        $role->update($validated);

        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (in_array($role->name, ['admin', 'superadmin', 'kasir', 'produksi', 'gudang', 'owner'])) {
            return back()->withErrors('Role sistem tidak dapat dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->withErrors('Role masih memiliki pengguna. Pindahkan pengguna terlebih dahulu.');
        }

        $role->delete();

        return redirect()->route('settings.roles.index')->with('success', 'Role berhasil dihapus.');
    }
}