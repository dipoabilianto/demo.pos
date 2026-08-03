@php
    $roleColors = [
        'superadmin' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200', 'dot' => 'bg-rose-400'],
        'admin' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'ring' => 'ring-purple-200', 'dot' => 'bg-purple-400'],
        'kasir' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'dot' => 'bg-emerald-400'],
        'produksi' => ['bg' => 'bg-theme-primary/10', 'text' => 'text-theme-primary', 'ring' => 'ring-theme-primary/20', 'dot' => 'bg-theme-primary'],
        'gudang' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700', 'ring' => 'ring-sky-200', 'dot' => 'bg-sky-400'],
    ];
@endphp

@extends('layouts.app')

@section('title', 'Pengguna')
@section('subtitle', 'Kelola akses pengguna & divisi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-warm-900">Daftar Pengguna</h2>
            <p class="text-sm text-warm-500 mt-0.5">{{ $users->total() }} pengguna terdaftar</p>
        </div>
        <button x-data @click="$dispatch('open-modal', 'add-user')"
            class="group inline-flex items-center gap-2.5 rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow active:scale-[0.97] transition-all duration-200">
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Pengguna
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-md shadow-warm-900/5 ring-1 ring-warm-200/50 overflow-hidden">
        <div class="p-4 border-b border-warm-100">
            <form method="GET" class="max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau peran..." class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-warm-200/50">
                <thead class="bg-warm-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Divisi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Bergabung</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-warm-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br bg-theme-gradient text-xs font-bold text-white shadow-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-warm-900">{{ $user->name }}</p>
                                        @if ($user->id === auth()->id())
                                            <span class="text-[10px] text-theme-primary font-medium">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-warm-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($user->roles as $role)
                                        @php $c = $roleColors[$role->name] ?? ['bg' => 'bg-stone-100', 'text' => 'text-stone-600', 'ring' => 'ring-stone-200', 'dot' => 'bg-stone-400']; @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 {{ $c['bg'] }} {{ $c['text'] }} {{ $c['ring'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $c['dot'] }}"></span>
                                            {{ $role->label }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-warm-400">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-warm-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button x-data @click="$dispatch('open-modal', 'edit-user-{{ $user->id }}')"
                                        class="rounded-lg p-2 text-warm-400 hover:text-theme-primary hover:bg-theme-primary/10 transition-all"
                                        title="Edit pengguna">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155l10.79-10.79z" /></svg>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $user->name }}?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg p-2 text-warm-400 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                                title="Hapus pengguna">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="max-w-sm mx-auto">
                                    <svg class="h-12 w-12 text-warm-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="text-sm font-medium text-warm-500">Belum ada pengguna</p>
                                    <p class="text-xs text-warm-400 mt-1">Klik "Tambah Pengguna" untuk menambahkan pengguna baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-warm-200/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@php
    $rolePermMap = [];
    foreach ($allRoles as $role) {
        $rolePermMap[$role->id] = $role->permissions ?? [];
    }
@endphp

{{-- Add User Modal --}}
<div
    x-data="userForm({{ json_encode($rolePermMap) }}, {{ json_encode($permissionModules) }}, null)"
    x-on:open-modal.window="if ($event.detail === 'add-user') { open = true; initForm(); }"
    x-show="open"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Tambah Pengguna</h3>
                        <p class="text-xs text-warm-500">Buat akun baru untuk karyawan</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.users.store') }}" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama</label>
                        <input type="text" name="name" required
                            class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                            placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Email</label>
                        <input type="email" name="email" required
                            class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                            placeholder="email@example.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                        placeholder="Minimal 6 karakter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Job/Posisi <span class="text-warm-400 font-normal">(bisa pilih lebih dari 1)</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach ($allRoles as $role)
                            @if ($role->name === 'superadmin')
                                @continue
                            @endif
                            @php $c = $roleColors[$role->name] ?? []; @endphp
                            <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-150"
                                :class="selectedRoles.includes({{ $role->id }})
                                    ? 'border-theme-primary bg-theme-primary/5 shadow-sm shadow-theme-primary/10'
                                    : 'border-warm-200 hover:border-warm-300 hover:bg-warm-50/50'">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    x-model="selectedRoles"
                                    @change="updatePermissions()"
                                    class="h-4 w-4 rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $c['dot'] ?? 'bg-stone-400' }}"></span>
                                    <span class="text-sm font-medium text-warm-700 truncate">{{ $role->label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-warm-400 mt-2">Hak akses akan digabung otomatis dari semua posisi yang dipilih.</p>
                </div>

                <div x-data="{ showDetails: false }" class="border-t border-warm-100 pt-4">
                    <button type="button" @click="showDetails = !showDetails"
                        class="group flex items-center gap-2 text-sm font-medium text-warm-500 hover:text-theme-primary transition-colors">
                        <svg class="h-4 w-4 transition-transform duration-200" :class="showDetails ? 'rotate-45' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span x-text="showDetails ? 'Tutup izin detail' : 'Atur izin detail (opsional)'"></span>
                    </button>

                    <div x-show="showDetails" x-collapse class="mt-4 space-y-3">
                        @foreach ($permissionModules as $module)
                            <div class="rounded-xl border border-warm-200 overflow-hidden">
                                <div class="px-4 py-2.5 bg-warm-50/70 border-b border-warm-200/50">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-warm-700">{{ $module['label'] }}</p>
                                                <span class="text-[10px] text-warm-400 font-medium uppercase tracking-wider"
                                                    x-text="countSelected('{{ $module['key'] }}') + '/' + {{ count($module['permissions']) }}"></span>
                                    </div>
                                </div>
                                <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    @foreach ($module['permissions'] as $perm)
                                        <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-warm-50 transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['key'] }}"
                                                x-model="selectedPermissions"
                                                class="h-4 w-4 rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                            <span class="text-sm text-warm-600">{{ $perm['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow active:scale-[0.97] transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Simpan
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit User Modals --}}
@foreach ($users as $user)
<div
    x-data="userForm({{ json_encode($rolePermMap) }}, {{ json_encode($permissionModules) }}, {{ $user->id }})"
    x-on:open-modal.window="if ($event.detail === 'edit-user-{{ $user->id }}') { open = true; initForm({{ json_encode($user->roles->pluck('id')->toArray()) }}, {{ json_encode($user->permissions ?? []) }}); }"
    x-show="open"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155L18.745 6.257z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Edit Pengguna</h3>
                        <p class="text-xs text-warm-500">{{ $user->name }}</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.users.update', $user) }}" class="space-y-5">
                @csrf @method('PATCH')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama</label>
                        <input type="text" name="name" value="{{ $user->name }}" required
                            class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" required
                            class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Password</label>
                    <input type="password" name="password"
                        class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm placeholder-warm-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                        placeholder="Kosongkan jika tidak diubah">
                    <p class="text-xs text-warm-400 mt-1">Biarkan kosong jika tidak ingin mengganti password.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Job/Posisi <span class="text-warm-400 font-normal">(bisa pilih lebih dari 1)</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        @foreach ($allRoles as $role)
                            @if ($role->name === 'superadmin')
                                @continue
                            @endif
                            @php $c = $roleColors[$role->name] ?? []; @endphp
                            <label class="relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all duration-150"
                                :class="selectedRoles.includes({{ $role->id }})
                                    ? 'border-theme-primary bg-theme-primary/5 shadow-sm shadow-theme-primary/10'
                                    : 'border-warm-200 hover:border-warm-300 hover:bg-warm-50/50'">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                    x-model="selectedRoles"
                                    @change="updatePermissions()"
                                    class="h-4 w-4 rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $c['dot'] ?? 'bg-stone-400' }}"></span>
                                    <span class="text-sm font-medium text-warm-700 truncate">{{ $role->label }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div x-data="{ showDetails: false }" class="border-t border-warm-100 pt-4">
                    <button type="button" @click="showDetails = !showDetails"
                        class="group flex items-center gap-2 text-sm font-medium text-warm-500 hover:text-theme-primary transition-colors">
                        <svg class="h-4 w-4 transition-transform duration-200" :class="showDetails ? 'rotate-45' : ''"
                            fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span x-text="showDetails ? 'Tutup izin detail' : 'Atur izin detail (opsional)'"></span>
                    </button>

                    <div x-show="showDetails" x-collapse class="mt-4 space-y-3">
                        @foreach ($permissionModules as $module)
                            <div class="rounded-xl border border-warm-200 overflow-hidden">
                                <div class="px-4 py-2.5 bg-warm-50/70 border-b border-warm-200/50">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-warm-700">{{ $module['label'] }}</p>
                                                <span class="text-[10px] text-warm-400 font-medium uppercase tracking-wider"
                                                    x-text="countSelected('{{ $module['key'] }}') + '/' + {{ count($module['permissions']) }}"></span>
                                    </div>
                                </div>
                                <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                    @foreach ($module['permissions'] as $perm)
                                        <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg cursor-pointer hover:bg-warm-50 transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['key'] }}"
                                                x-model="selectedPermissions"
                                                class="h-4 w-4 rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                            <span class="text-sm text-warm-600">{{ $perm['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false"
                        class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow active:scale-[0.97] transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Simpan
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userForm', (rolePermMap, permissionModules, userId) => ({
        open: false,
        selectedRoles: [],
        selectedPermissions: [],
        rolePermMap: rolePermMap,
        permissionModules: permissionModules,

        initForm(initialRoles = [], initialPermissions = []) {
            this.selectedRoles = initialRoles;
            this.selectedPermissions = initialPermissions;
            if (initialPermissions.length === 0) {
                this.updatePermissions();
            }
        },

        updatePermissions() {
            const union = new Set();
            this.selectedRoles.forEach(roleId => {
                const perms = this.rolePermMap[roleId] || [];
                perms.forEach(p => union.add(p));
            });
            this.selectedPermissions = Array.from(union);
        },

        countSelected(moduleKey) {
            if (!this.selectedPermissions) return 0;
            const module = this.permissionModules.find(m => m.key === moduleKey);
            if (!module) return 0;
            return module.permissions.filter(p => this.selectedPermissions.includes(p.key)).length;
        }
    }));
});
</script>
@endpush
