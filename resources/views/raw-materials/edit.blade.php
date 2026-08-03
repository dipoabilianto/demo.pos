@extends('layouts.app')
@section('title', 'Edit Bahan Baku')
@section('subtitle', 'Ubah data bahan baku.')
@section('content')
<form method="POST" action="{{ route('raw-materials.update', $rawMaterial) }}" class="max-w-xl space-y-6">
    @csrf @method('PUT')
    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Data Bahan Baku</h3>
        </div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Bahan Baku <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $rawMaterial->name) }}" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="Mis: Tepung Terigu">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                <select name="unit" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                    <option value="">Pilih</option>
                    @foreach (['Kg', 'Gram', 'Liter', 'ml', 'Pcs', 'Bungkus', 'Pack', 'Butir'] as $u)
                        <option value="{{ $u }}" {{ old('unit', $rawMaterial->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Stok Saat Ini</label>
                <input type="number" value="{{ number_format($rawMaterial->current_stock, 0) }}" disabled class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm bg-warm-50 text-warm-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Min. Stok</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', $rawMaterial->min_stock) }}" step="0.01" min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="0">
            </div>
        </div>
        <p class="text-xs text-warm-400 italic">Stok saat ini tidak bisa diubah dari sini. Gunakan menu <strong>Stok Opname</strong> untuk penyesuaian stok.</p>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('raw-materials.index') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Batal</a>
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection
