@extends('layouts.app')
@section('title', 'Stok Opname - ' . $rawMaterial->name)
@section('subtitle', 'Sesuaikan stok fisik dengan catatan.')
@section('content')
<form method="POST" action="{{ route('stock-opname.adjust') }}" class="max-w-xl space-y-6">
    @csrf
    <input type="hidden" name="raw_material_id" value="{{ $rawMaterial->id }}">

    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" /></svg>
            </div>
            <div>
                <h3 class="text-base font-semibold text-warm-900">Stok Opname: {{ $rawMaterial->name }}</h3>
                <p class="text-sm text-warm-400">Satuan: {{ $rawMaterial->unit }}</p>
            </div>
        </div>

        <div class="flex items-center justify-center gap-6 py-4">
            <div class="text-center">
                <p class="text-xs text-warm-400 mb-1">Stok Sistem</p>
                <p class="text-3xl font-bold text-warm-900">{{ number_format($rawMaterial->current_stock, 0) }}</p>
                <p class="text-xs text-warm-400">{{ $rawMaterial->unit }}</p>
            </div>
            <div class="text-warm-300 text-2xl font-light">&rarr;</div>
            <div class="text-center">
                <p class="text-xs text-warm-400 mb-1">Stok Fisik</p>
                <input type="number" name="actual_stock" id="actual-stock" value="{{ old('actual_stock', $rawMaterial->current_stock) }}" required step="0.01" min="0" class="block w-32 text-center text-2xl font-bold rounded-xl border-theme-primary/30 px-3 py-2 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white mx-auto">
                <p class="text-xs text-warm-400 mt-1">{{ $rawMaterial->unit }}</p>
            </div>
        </div>

        <div id="stock-diff" class="hidden text-center text-sm font-medium p-3 rounded-xl"></div>

        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Catatan</label>
            <textarea name="note" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="Mis: Hasil stok opname fisik...">{{ old('note') }}</textarea>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('raw-materials.index', ['tab' => 'opname']) }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Batal</a>
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Simpan Opname
        </button>
    </div>
</form>

@push('scripts')
<script>
const systemStock = {{ $rawMaterial->current_stock }};
const actualInput = document.getElementById('actual-stock');
const diffEl = document.getElementById('stock-diff');

function updateDiff() {
    const actual = parseFloat(actualInput.value) || 0;
    const diff = actual - systemStock;
    if (diff === 0) {
        diffEl.classList.add('hidden');
    } else {
        diffEl.classList.remove('hidden');
        if (diff > 0) {
            diffEl.className = 'text-center text-sm font-medium p-3 rounded-xl bg-emerald-50 text-emerald-700';
            diffEl.textContent = '+ ' + diff.toFixed(0) + ' ' + '{{ $rawMaterial->unit }}' + ' (kelebihan stok)';
        } else {
            diffEl.className = 'text-center text-sm font-medium p-3 rounded-xl bg-rose-50 text-rose-700';
            diffEl.textContent = diff.toFixed(0) + ' ' + '{{ $rawMaterial->unit }}' + ' (kekurangan stok)';
        }
    }
}

actualInput.addEventListener('input', updateDiff);
updateDiff();
</script>
@endpush
@endsection
