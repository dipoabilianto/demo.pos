@extends('layouts.app')
@section('title', 'Owner Dashboard')
@php
    $label = isset($hasFilter) && $hasFilter ? 'Ringkasan periode' : 'Ringkasan hari ini';
@endphp
@section('subtitle', $tab === 'voucher' ? 'Kelola voucher diskon.' : 'Consolidated ' . $label . ' — semua cabang')
@section('content')

{{-- Tabs --}}
<div class="mb-6 flex gap-1 rounded-xl bg-white p-1.5 shadow-sm border border-warm-200/50 w-fit">
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'chart']) }}"
       class="rounded-lg px-5 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'chart' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            Grafik
        </div>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'voucher']) }}"
       class="rounded-lg px-5 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'voucher' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
            Voucher
        </div>
    </a>
</div>

@if ($tab === 'voucher')

<div class="flex items-center justify-between mb-6">
    <div></div>
    <div>
        <a href="{{ route('vouchers.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            + Voucher Baru
        </a>
    </div>
</div>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nilai</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Min. Belanja</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($vouchers as $v)
                <tr class="hover:bg-warm-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-mono font-medium text-warm-900">{{ $v->code }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-1 text-xs font-medium text-warm-700">
                            {{ $v->type === 'percentage' ? 'Persen' : 'Nominal' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-warm-900">
                        {{ $v->type === 'percentage' ? $v->value . '%' : 'Rp ' . number_format($v->value, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-warm-500">Rp {{ number_format($v->min_order ?? 0, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $isExpired = $v->expires_at && $v->expires_at->isPast();
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $isExpired ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            {{ $isExpired ? 'Kadaluarsa' : 'Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('vouchers.edit', $v) }}" class="inline-flex items-center gap-1 rounded-lg bg-theme-primary/10 px-3 py-1.5 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                        <p class="text-sm text-warm-400">Belum ada voucher.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vouchers->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $vouchers->links() }}</div>
    @endif
</div>

@else

<div class="animate-fade-in-up mb-5 rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50">
    <form method="GET" action="{{ route('owner.dashboard') }}" id="filter-form">
        <div class="flex flex-wrap items-end gap-3 p-4">
            <div>
                <label class="block text-xs font-medium text-warm-500 mb-1">Dari</label>
                <input type="date" name="from" id="filter-from" value="{{ $from ?? '' }}"
                    class="block rounded-lg border-warm-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-warm-500 mb-1">Sampai</label>
                <input type="date" name="to" id="filter-to" value="{{ $to ?? '' }}"
                    class="block rounded-lg border-warm-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-x-2 gap-y-2 border-t border-warm-100 px-4 py-3">
            <button type="submit"
                class="rounded-lg bg-theme-gradient-r px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">
                Filter
            </button>

            <span class="hidden sm:inline text-warm-200 select-none mx-1">|</span>

            <span class="text-xs text-warm-400 font-medium mr-0.5">Preset:</span>

            <div class="flex gap-1.5">
                <button type="button" onclick="setPreset(1)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    1 Hari
                </button>
                <button type="button" onclick="setPreset(7)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    1 Minggu
                </button>
                <button type="button" onclick="setPreset(30)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    1 Bulan
                </button>
                <button type="button" onclick="setPreset(365)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    1 Tahun
                </button>
            </div>

            @if ($hasFilter)
            <span class="hidden sm:inline text-warm-200 select-none mx-1">|</span>
            <a href="{{ route('owner.dashboard') }}"
                class="rounded-lg bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition-all">
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Totals --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.05s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shadow-sm shadow-emerald-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a9.75 9.75 0 0019.5 0M12 6v2.25m0 0a1.5 1.5 0 01-1.5 1.5H9m3-1.5a1.5 1.5 0 011.5 1.5h1.5M12 6a1.5 1.5 0 00-1.5-1.5H9m3 3V6m0 0V4.5" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Total Penjualan</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">Rp {{ number_format($totals['sales'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.1s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 shadow-sm shadow-rose-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">Rp {{ number_format($totals['expenses'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.15s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-50 text-violet-600 shadow-sm shadow-violet-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Laba Bersih</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">Rp {{ number_format($totals['profit'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.2s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 shadow-sm shadow-amber-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25-2.25M12 13.875V7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Stok Menipis</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">{{ $lowStockProducts }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Chart 1: Laba per Cabang --}}
<div class="animate-fade-in-up rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 mb-6" style="animation-delay: 0.3s">
    <div class="flex items-center justify-between border-b border-warm-100 px-5 py-4">
        <div>
            <h3 class="text-sm font-semibold text-warm-800">Laba per Cabang</h3>
            <p class="text-xs text-warm-400 mt-0.5">
                {{ \Carbon\Carbon::parse($profitBranchChartData['labels'][0] ?? today())->format('d M') }}
                &mdash;
                {{ \Carbon\Carbon::parse($profitBranchChartData['labels'][count($profitBranchChartData['labels'])-1] ?? today())->format('d M Y') }}
            </p>
        </div>
        <span class="text-xs text-warm-400">Klik legenda untuk sembunyikan/tampilkan</span>
    </div>
    <div class="p-5" x-data='profitBranchChart(@json($profitBranchChartData))'>
        <canvas x-ref="canvas" height="280"></canvas>
    </div>
</div>

{{-- Chart 2 + Produk Favorit per Cabang --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="animate-fade-in-up rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.35s">
        <div class="border-b border-warm-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-warm-800">Penjualan per Cabang</h3>
        </div>
        <div class="p-5" x-data='branchChart(@json($branchChartData))'>
            <canvas x-ref="canvas" height="260"></canvas>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.4s">
        <div class="border-b border-warm-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-warm-800">Produk Favorit per Cabang</h3>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
            @forelse ($branchTopProducts as $bp)
            <div>
                <p class="text-xs font-semibold text-warm-500 mb-2 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-theme-primary"></span>
                    {{ $bp['branch'] }}
                </p>
                @php $maxQty = max(array_column($bp['products'], 'total_qty') ?: [1]); @endphp
                <div class="space-y-2">
                    @foreach ($bp['products'] as $p)
                    @php $pct = ($p['total_qty'] / $maxQty) * 100; @endphp
                    <div class="group cursor-default">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-warm-700 truncate pr-2">{{ $p['product_name'] }}</span>
                            <span class="shrink-0 font-semibold text-warm-900">{{ $p['total_qty'] }}x</span>
                        </div>
                        <div class="relative h-5 rounded-full bg-warm-100 overflow-hidden">
                            <div class="h-full rounded-full bg-theme-primary/70 transition-all duration-700 ease-out group-hover:bg-theme-primary"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-[10px] text-warm-400 mt-0.5">Rp {{ number_format($p['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="text-sm text-warm-400 col-span-2 text-center py-6">Belum ada data produk.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Per-branch breakdown --}}
<div class="animate-fade-in-up rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.45s">
    <div class="border-b border-warm-100 px-5 py-4">
        <h3 class="text-sm font-semibold text-warm-800">Rincian per Cabang</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-warm-100 bg-warm-50/50">
                    <th class="text-left px-5 py-3 font-semibold text-warm-600">Cabang</th>
                    <th class="text-right px-5 py-3 font-semibold text-warm-600">Penjualan</th>
                    <th class="text-right px-5 py-3 font-semibold text-warm-600">Pengeluaran</th>
                    <th class="text-right px-5 py-3 font-semibold text-warm-600">Laba</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($branches as $b)
                <tr class="border-b border-warm-100 hover:bg-warm-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <span class="font-medium text-warm-800">{{ $b['branch']->name }}</span>
                        @if ($b['branch']->businessTypes?->isNotEmpty())
                        <div class="flex gap-1 mt-0.5">
                            @foreach ($b['branch']->businessTypes as $bt)
                            <x-ui.badge variant="theme" size="xs">{{ $bt->name }}</x-ui.badge>
                            @endforeach
                        </div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-medium text-emerald-600">Rp {{ number_format($b['sales'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right font-medium text-rose-600">Rp {{ number_format($b['expenses'], 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-warm-900">Rp {{ number_format($b['profit'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
<script>
function setPreset(val) {
    const from = document.getElementById('filter-from');
    const to = document.getElementById('filter-to');
    const today = new Date();
    const y = today.getFullYear();
    const m = String(today.getMonth() + 1).padStart(2, '0');
    const d = String(today.getDate()).padStart(2, '0');
    to.value = `${y}-${m}-${d}`;
    const past = new Date(today);
    past.setDate(past.getDate() - (val - 1));
    const py = past.getFullYear();
    const pm = String(past.getMonth() + 1).padStart(2, '0');
    const pd = String(past.getDate()).padStart(2, '0');
    from.value = `${py}-${pm}-${pd}`;
    document.getElementById('filter-form').requestSubmit();
}
</script>
@endpush
@endsection
