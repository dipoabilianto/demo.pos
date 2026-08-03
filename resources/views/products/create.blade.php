@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('subtitle', 'Masukkan data produk baru.')
@section('content')
<form method="POST" action="{{ route('products.store') }}" class="max-w-2xl space-y-6" enctype="multipart/form-data">
    @csrf
    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Informasi Produk</h3>
        </div>

        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Foto Produk</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-dashed border-warm-300 px-4 py-3 text-sm text-warm-500 hover:border-theme-primary hover:text-theme-primary transition-colors" id="product-image-label">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                <span id="product-image-text">Unggah Foto (PNG/JPG, maks 2MB)</span>
                <input type="file" id="product-image-input" accept="image/*" class="hidden">
            </label>
            <div id="product-image-preview" class="hidden mt-3"></div>
            <input type="hidden" name="image_data" id="product-image-data">
            <p id="product-image-status" class="hidden mt-2 text-xs font-medium"></p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Kategori</label>
            <select name="category_id" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                <option value="">Pilih Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('description') }}</textarea>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Harga & Stok</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Harga Jual <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-warm-400 font-medium">Rp</span>
                    <input type="number" name="price" value="{{ old('price') }}" required step="0.01" min="0" class="block w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Harga Sale</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-warm-400 font-medium">Rp</span>
                    <input type="number" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" class="block w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>
                <p class="text-[11px] text-warm-400 mt-1">Harga promo (harus lebih kecil dari harga jual)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Harga Modal</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-warm-400 font-medium">Rp</span>
                    <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0" class="block w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5" id="stock-label">Stok <span class="text-rose-500">*</span></label>
                <input type="number" name="stock" id="stock-input" value="{{ old('stock', 0) }}" required min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5" id="min-stock-label">Min. Stok</label>
                <input type="number" name="min_stock" id="min-stock-input" value="{{ old('min_stock', 1) }}" min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer pt-2">
            <input type="checkbox" name="is_unlimited" value="1" {{ old('is_unlimited') ? 'checked' : '' }}
                class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20"
                onchange="document.getElementById('stock-input').disabled = this.checked; document.getElementById('min-stock-input').disabled = this.checked; document.getElementById('stock-label').classList.toggle('text-warm-400', this.checked); document.getElementById('min-stock-label').classList.toggle('text-warm-400', this.checked);">
            <span class="text-sm font-medium text-warm-700">Stok Tidak Terbatas (∞)</span>
        </label>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('products.index') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Batal</a>
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Simpan
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cb = document.querySelector('[name="is_unlimited"]');
    if (cb && cb.checked) {
        document.getElementById('stock-input').disabled = true;
        document.getElementById('min-stock-input').disabled = true;
        document.getElementById('stock-label').classList.add('text-warm-400');
        document.getElementById('min-stock-label').classList.add('text-warm-400');
    }
});
document.addEventListener('change', function(e) {
    if (e.target.id !== 'product-image-input') return;
    const file = e.target.files[0];
    if (!file) return;
    const preview = document.getElementById('product-image-preview');
    const text = document.getElementById('product-image-text');
    const status = document.getElementById('product-image-status');
    const hidden = document.getElementById('product-image-data');
    status.className = 'mt-2 text-xs font-medium text-theme-primary';
    status.textContent = 'Mengompres...';
    status.classList.remove('hidden');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.onload = function() {
        let w = img.width, h = img.height;
        const maxDim = 600;
        if (w > maxDim || h > maxDim) {
            const ratio = Math.min(maxDim / w, maxDim / h);
            w = Math.round(w * ratio);
            h = Math.round(h * ratio);
        }
        canvas.width = w;
        canvas.height = h;
        ctx.drawImage(img, 0, 0, w, h);
        canvas.toBlob(function(blob) {
            const reader = new FileReader();
            reader.onloadend = function() {
                hidden.value = reader.result;
                preview.innerHTML = '<img src="' + reader.result + '" class="h-32 w-auto object-contain rounded-xl border border-warm-200">';
                preview.classList.remove('hidden');
                text.textContent = file.name + ' (' + (blob.size / 1024).toFixed(1) + ' KB)';
                status.className = 'mt-2 text-xs font-medium text-emerald-600';
                status.textContent = 'Siap diunggah';
            };
            reader.readAsDataURL(blob);
        }, 'image/webp', 0.8);
    };
    img.onerror = function() {
        status.className = 'mt-2 text-xs font-medium text-rose-600';
        status.textContent = 'Gagal membaca gambar.';
    };
    img.src = URL.createObjectURL(file);
});
</script>
@endpush
