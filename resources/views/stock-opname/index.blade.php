@extends('layouts.app')
@section('title', 'Stok Opname')
@section('subtitle', 'Lihat dan sesuaikan stok bahan baku.')
@section('content')
<div class="flex items-center justify-between mb-6">
    <form method="GET" class="flex-1 max-w-sm">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari bahan baku..." class="block w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </form>
    <a href="{{ route('stock-opname.history') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 transition-all duration-200">
        Riwayat Transaksi
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($materials as $material)
        <div class="rounded-2xl bg-white p-5 shadow-md shadow-warm-900/5 border border-warm-200/50 hover:shadow-lg transition-all duration-200">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-warm-900">{{ $material->name }}</h3>
                    <p class="text-xs text-warm-400 mt-0.5">Satuan: {{ $material->unit }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $material->isLowStock() ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5 mb-4">
                <span class="text-2xl font-bold {{ $material->isLowStock() ? 'text-rose-600' : 'text-warm-900' }}">
                    {{ number_format($material->current_stock, 0) }}
                </span>
                <span class="text-sm text-warm-400">{{ $material->unit }}</span>
            </div>
            <div class="space-y-1.5 mb-4">
                <div class="flex justify-between text-xs">
                    <span class="text-warm-400">Min. Stok</span>
                    <span class="text-warm-600 font-medium">{{ number_format($material->min_stock, 0) }}</span>
                </div>
                @if ($material->min_stock > 0)
                    <div class="w-full bg-warm-100 rounded-full h-1.5">
                        @php $pct = min(100, ($material->current_stock / $material->min_stock) * 100); @endphp
                        <div class="h-1.5 rounded-full transition-all {{ $material->isLowStock() ? 'bg-rose-400' : 'bg-emerald-400' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('stock-opname.adjust-form', $material) }}" class="flex-1 text-center rounded-xl bg-theme-gradient-r px-3 py-2 text-xs font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all duration-200">
                    <svg class="h-3.5 w-3.5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" /></svg>
                    Stok Opname
                </a>
                <a href="{{ route('raw-materials.edit', $material) }}" class="rounded-xl bg-warm-100 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 transition-all">
                    Edit
                </a>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16 text-warm-400">
            <svg class="h-16 w-16 mx-auto mb-4 text-warm-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
            <p class="text-sm">Belum ada bahan baku. Tambahkan dari menu <strong>Bahan Baku</strong> terlebih dahulu.</p>
        </div>
    @endforelse
</div>
@if ($materials->hasPages())
    <div class="mt-6">{{ $materials->links() }}</div>
@endif
@endsection
