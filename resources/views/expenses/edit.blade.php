@extends('layouts.app')
@section('title', 'Edit Pengeluaran')
@section('subtitle', 'Ubah data pengeluaran.')
@section('content')
<form method="POST" action="{{ route('expenses.update', $expense) }}" class="max-w-xl space-y-6">
    @csrf @method('PUT')
    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-rose-100 to-rose-50 text-rose-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Data Pengeluaran</h3>
        </div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Judul <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $expense->title) }}" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Jumlah <span class="text-rose-500">*</span></label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-warm-400 font-medium">Rp</span>
                <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" required step="0.01" min="0" class="block w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Kategori</label>
                <select name="category" id="expense-category" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                    <option value="">Pilih Kategori</option>
                    @foreach (['Operasional', 'Stok', 'Stok Bahan Baku', 'Utilities', 'Gaji', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $expense->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Tanggal <span class="text-rose-500">*</span></label>
                <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
        </div>

        <div id="stock-fields" class="{{ $expense->category === 'Stok Bahan Baku' ? '' : 'hidden' }} space-y-4 p-4 rounded-xl bg-theme-primary/5 border border-theme-primary/20">
            <p class="text-xs font-medium text-theme-primary flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                Data Pembelian Bahan Baku
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Bahan Baku <span class="text-rose-500">*</span></label>
                    @php
                        $stockTrans = $expense->stockTransactions->first();
                        $matName = old('raw_material_name', $stockTrans?->rawMaterial?->name ?? '');
                    @endphp
                    <input type="text" name="raw_material_name" id="raw-material-name" value="{{ $matName }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="Mis: Tepung Terigu">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Satuan <span class="text-rose-500">*</span></label>
                    <select name="raw_material_unit" id="raw-material-unit" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        <option value="">Pilih</option>
                        @php $currUnit = old('raw_material_unit', $stockTrans?->rawMaterial?->unit ?? ''); @endphp
                        @foreach (['Kg', 'Gram', 'Liter', 'ml', 'Pcs', 'Bungkus', 'Pack', 'Butir', 'Kaleng'] as $u)
                            <option value="{{ $u }}" {{ $currUnit === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Jumlah Dibeli <span class="text-rose-500">*</span></label>
                <input type="number" name="stock_quantity" id="stock-quantity" value="{{ old('stock_quantity', $stockTrans?->quantity) }}" step="0.01" min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="0">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('description', $expense->description) }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('expenses.index') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Batal</a>
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Simpan Perubahan
        </button>
    </div>
</form>

@push('scripts')
<script>
const category = document.getElementById('expense-category');
const stockFields = document.getElementById('stock-fields');
const rawMaterialName = document.getElementById('raw-material-name');
const rawMaterialUnit = document.getElementById('raw-material-unit');
const stockQuantity = document.getElementById('stock-quantity');

function toggleStockFields() {
    if (category.value === 'Stok Bahan Baku') {
        stockFields.classList.remove('hidden');
        rawMaterialName.required = true;
        rawMaterialUnit.required = true;
        stockQuantity.required = true;
    } else {
        stockFields.classList.add('hidden');
        rawMaterialName.required = false;
        rawMaterialUnit.required = false;
        stockQuantity.required = false;
    }
}

category.addEventListener('change', toggleStockFields);
toggleStockFields();
</script>
@endpush
@endsection
