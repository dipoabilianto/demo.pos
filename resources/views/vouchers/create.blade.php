@extends('layouts.app')
@section('title', 'Buat Voucher')
@section('subtitle', 'Buat kode voucher diskon baru.')
@section('content')
<form method="POST" action="{{ route('vouchers.store') }}" class="max-w-2xl space-y-6">
    @csrf
    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Informasi Voucher</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Kode Voucher <span class="text-rose-500">*</span></label>
                <div class="flex gap-2">
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required class="flex-1 rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white uppercase">
                    <button type="button" id="generateCode" class="rounded-xl bg-warm-100 px-3 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-200 transition-all">Generate</button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Tipe <span class="text-rose-500">*</span></label>
                <select name="type" id="type" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Persen (%)</option>
                    <option value="nominal" {{ old('type') === 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Nilai Diskon <span class="text-rose-500">*</span></label>
                <input type="number" name="value" id="value" value="{{ old('value') }}" required min="0" step="0.01" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
            <div id="maxDiscountWrapper">
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Maksimal Potongan</label>
                <input type="number" name="max_discount" value="{{ old('max_discount') }}" min="0" step="0.01" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                <p class="text-[11px] text-warm-400 mt-1">Kosongkan jika tanpa batas</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Minimal Belanja</label>
                <input type="number" name="min_order" value="{{ old('min_order', 0) }}" min="0" step="0.01" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                <p class="text-[11px] text-warm-400 mt-1">0 = tanpa minimal</p>
            </div>
            <div></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Batas Pemakaian Global</label>
                <input type="number" name="max_uses" value="{{ old('max_uses', 0) }}" min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                <p class="text-[11px] text-warm-400 mt-1">0 = unlimited</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Batas Per Orang</label>
                <input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user', 0) }}" min="0" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                <p class="text-[11px] text-warm-400 mt-1">0 = unlimited</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Mulai Berlaku</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Berakhir Pada</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') === '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-warm-300 text-theme-primary focus:ring-theme-primary/50">
            <label for="is_active" class="text-sm font-medium text-warm-700">Aktif</label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Simpan Voucher
        </button>
        <a href="{{ route('vouchers.index') }}" class="rounded-xl bg-white px-6 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 transition-all">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('generateCode')?.addEventListener('click', function () {
    const prefix = 'VCH-';
    const random = Array.from({ length: 8 }, () =>
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'[Math.floor(Math.random() * 36)]
    ).join('');
    document.getElementById('code').value = prefix + random;
});

document.getElementById('type')?.addEventListener('change', function () {
    const wrapper = document.getElementById('maxDiscountWrapper');
    wrapper.style.display = this.value === 'percentage' ? '' : 'none';
});
document.getElementById('type')?.dispatchEvent(new Event('change'));
</script>
@endpush
