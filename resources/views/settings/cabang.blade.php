@extends('layouts.app')
@section('title', 'Cabang & Tipe Bisnis')
@section('subtitle', 'Kelola cabang, tipe bisnis, dan kategorinya')
@section('content')
<div x-data="{ activeTab: 'cabang' }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex gap-1 rounded-xl bg-warm-100 p-1">
            <button @click="activeTab = 'cabang'" :class="activeTab === 'cabang' ? 'bg-white shadow-sm ring-1 ring-warm-200' : 'hover:bg-white/50'" class="rounded-lg px-4 py-2 text-sm font-medium text-warm-700 transition-all">
                Cabang
            </button>
            <button @click="activeTab = 'bisnis'" :class="activeTab === 'bisnis' ? 'bg-white shadow-sm ring-1 ring-warm-200' : 'hover:bg-white/50'" class="rounded-lg px-4 py-2 text-sm font-medium text-warm-700 transition-all">
                Tipe Bisnis
            </button>
        </div>
        <button x-data @click="$dispatch('open-modal', activeTab === 'cabang' ? 'add-branch' : 'add-business-type')"
            class="group inline-flex items-center gap-2 rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl active:scale-[0.97] transition-all duration-200">
            <svg class="h-4 w-4 transition-transform duration-200 group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            <span x-text="activeTab === 'cabang' ? 'Tambah Cabang' : 'Tambah Tipe Bisnis'"></span>
        </button>
    </div>

    <div x-show="activeTab === 'cabang'" x-cloak>
        <div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-warm-50/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Cabang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tipe Bisnis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Online</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @forelse ($branches as $branch)
                            <tr class="hover:bg-warm-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-theme-gradient text-xs font-bold text-white shadow-sm shrink-0">
                                            {{ strtoupper(substr($branch->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-theme-primary">{{ $branch->name }}</p>
                                            @if ($branch->phone)
                                                <p class="text-xs text-warm-400">{{ $branch->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($branch->businessTypes as $bt)
                                            <span class="inline-flex items-center rounded-full bg-theme-primary/10 px-2.5 py-1 text-xs font-medium text-theme-primary ring-1 ring-theme-primary/20">
                                                {{ $bt->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-warm-400">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-warm-500 max-w-[200px] truncate">{{ $branch->address ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    @if ($branch->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-500 ring-1 ring-warm-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-warm-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($branch->is_online)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-sky-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            Buka
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-500 ring-1 ring-warm-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-warm-400"></span>
                                            Tutup
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button x-data @click="$dispatch('open-modal', 'edit-branch-{{ $branch->id }}')"
                                            class="rounded-lg p-2 text-warm-400 hover:text-theme-primary hover:bg-theme-primary/10 transition-all" title="Edit cabang">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155l10.79-10.79z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('settings.cabang.branch.destroy', $branch) }}" onsubmit="return confirm('Yakin ingin menghapus cabang {{ $branch->name }}?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg p-2 text-warm-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Hapus cabang">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <svg class="h-12 w-12 text-warm-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                        <p class="text-sm font-medium text-warm-500">Belum ada cabang</p>
                                        <p class="text-xs text-warm-400 mt-1">Klik "Tambah Cabang" untuk menambahkan cabang baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div x-show="activeTab === 'bisnis'" x-cloak>
        <div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-warm-50/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Digunakan di</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @forelse ($businessTypes as $bt)
                            <tr class="hover:bg-warm-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-theme-primary">{{ $bt->name }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-warm-500">{{ $bt->slug }}</td>
                                <td class="px-6 py-4 text-sm text-warm-500 max-w-[250px] truncate">{{ $bt->description ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($bt->branches as $b)
                                            <span class="inline-flex items-center rounded-full bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-600 ring-1 ring-warm-200">
                                                {{ $b->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-warm-400">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button x-data @click="$dispatch('open-modal', 'edit-business-type-{{ $bt->id }}')"
                                            class="rounded-lg p-2 text-warm-400 hover:text-theme-primary hover:bg-theme-primary/10 transition-all" title="Edit tipe bisnis">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155l10.79-10.79z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('settings.cabang.business-type.destroy', $bt) }}" onsubmit="return confirm('Yakin ingin menghapus tipe bisnis {{ $bt->name }}?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg p-2 text-warm-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Hapus tipe bisnis">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <svg class="h-12 w-12 text-warm-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                        <p class="text-sm font-medium text-warm-500">Belum ada tipe bisnis</p>
                                        <p class="text-xs text-warm-400 mt-1">Klik "Tambah Tipe Bisnis" untuk menambahkan tipe bisnis baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Cabang --}}
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'add-branch') open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Tambah Cabang</h3>
                        <p class="text-xs text-warm-500">Buat cabang baru</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.cabang.branch.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Cabang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" inputmode="text" required class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Cabang Utama">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Kosongkan untuk otomatis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Alamat</label>
                    <textarea name="address" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Jl. Contoh No. 123"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" inputmode="tel" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="081234567890">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Tipe Bisnis</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($allBusinessTypes as $bt)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-warm-200 cursor-pointer hover:border-theme-primary/50 transition-colors">
                                <input type="checkbox" name="business_types[]" value="{{ $bt->id }}" class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                <span class="text-sm text-warm-700">{{ $bt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-warm-100 pt-4 mt-2">
                    <p class="text-xs font-semibold text-warm-500 uppercase tracking-wider mb-3">Lokasi Absensi</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Latitude</label>
                            <input type="text" name="latitude" placeholder="-6.2088" inputmode="decimal" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Longitude</label>
                            <input type="text" name="longitude" placeholder="106.8456" inputmode="decimal" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-medium text-warm-700 mb-1">Radius (meter)</label>
                        <input type="number" name="radius_meters" value="100" min="10" max="10000" inputmode="numeric" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_online" value="1" class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                    <span class="text-sm font-medium text-warm-700">Pemesanan Online</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                    <span class="text-sm font-medium text-warm-700">Aktif</span>
                </label>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false" class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">Batal</button>
                    <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl active:scale-[0.97] transition-all duration-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Cabang --}}
@foreach ($branches as $branch)
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'edit-branch-{{ $branch->id }}') open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155l10.79-10.79z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Edit Cabang</h3>
                        <p class="text-xs text-warm-500">{{ $branch->name }}</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.cabang.branch.update', $branch) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Cabang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ $branch->name }}" inputmode="text" required class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" value="{{ $branch->slug }}" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Alamat</label>
                    <textarea name="address" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">{{ $branch->address }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ $branch->phone }}" inputmode="tel" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Tipe Bisnis</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($allBusinessTypes as $bt)
                            <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-warm-200 cursor-pointer hover:border-theme-primary/50 transition-colors">
                                <input type="checkbox" name="business_types[]" value="{{ $bt->id }}" {{ $branch->businessTypes->contains($bt->id) ? 'checked' : '' }} class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                                <span class="text-sm text-warm-700">{{ $bt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-warm-100 pt-4 mt-2">
                    <p class="text-xs font-semibold text-warm-500 uppercase tracking-wider mb-3">Lokasi Absensi</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Latitude</label>
                            <input type="text" name="latitude" value="{{ $branch->latitude }}" placeholder="-6.2088" inputmode="decimal" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Longitude</label>
                            <input type="text" name="longitude" value="{{ $branch->longitude }}" placeholder="106.8456" inputmode="decimal" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-medium text-warm-700 mb-1">Radius (meter)</label>
                        <input type="number" name="radius_meters" value="{{ $branch->radius_meters ?? 100 }}" min="10" max="10000" inputmode="numeric" class="block w-full rounded-xl border-warm-300 px-3 py-2 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_online" value="1" {{ $branch->is_online ? 'checked' : '' }} class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                    <span class="text-sm font-medium text-warm-700">Pemesanan Online</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $branch->is_active ? 'checked' : '' }} class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                    <span class="text-sm font-medium text-warm-700">Aktif</span>
                </label>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false" class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">Batal</button>
                    <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl active:scale-[0.97] transition-all duration-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Modal Tambah Tipe Bisnis --}}
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'add-business-type') open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Tambah Tipe Bisnis</h3>
                        <p class="text-xs text-warm-500">Tentukan jenis bisnis (bakery, coffee shop, dll)</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.cabang.business-type.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" inputmode="text" required class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Bakery">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Kosongkan untuk otomatis">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all" placeholder="Toko roti dan kue"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false" class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">Batal</button>
                    <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl active:scale-[0.97] transition-all duration-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Tipe Bisnis --}}
@foreach ($businessTypes as $bt)
<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'edit-business-type-{{ $bt->id }}') open = true" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.155.66l-.083.042-2.207.735.735-2.207.042-.083a4.5 4.5 0 01.66-1.155l10.79-10.79z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-warm-900">Edit Tipe Bisnis</h3>
                        <p class="text-xs text-warm-500">{{ $bt->slug }}</p>
                    </div>
                </div>
                <button @click="open = false" class="rounded-lg p-1.5 text-warm-400 hover:text-warm-700 hover:bg-warm-100 transition-all">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('settings.cabang.business-type.update', $bt) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ $bt->name }}" inputmode="text" required class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" value="{{ $bt->slug }}" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="2" inputmode="text" class="block w-full rounded-xl border-warm-300 px-4 py-2.5 text-sm focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">{{ $bt->description }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-warm-200/50">
                    <button type="button" @click="open = false" class="rounded-xl px-5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-100 transition-all">Batal</button>
                    <button type="submit" class="rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl active:scale-[0.97] transition-all duration-200">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
