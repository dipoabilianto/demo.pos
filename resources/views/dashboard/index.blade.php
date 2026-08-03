@extends('layouts.app')
@section('title', 'Dashboard')
@php $sub = $hasFilter ? 'Ringkasan periode ' . ($from ?? '...') . ' — ' . ($to ?? '...') : 'Ringkasan bisnis Anda hari ini.'; @endphp
@section('subtitle', $sub)
@section('content')

{{-- Filter --}}
<div class="animate-fade-in-up mb-5 rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0s">
    <form method="GET" action="{{ route('dashboard') }}" id="filter-form">
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
        <div class="flex flex-wrap items-center gap-2 border-t border-warm-100 px-4 py-3">
            <button type="submit"
                class="rounded-lg bg-theme-gradient-r px-4 py-2 text-xs font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">
                Filter
            </button>
            <span class="hidden sm:inline text-warm-200 mx-0.5">|</span>
            <span class="text-xs text-warm-400 font-medium mr-1">Preset:</span>
            <div class="flex gap-1.5">
                <button type="button" onclick="setPreset(1)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    1 Hari
                </button>
                <button type="button" onclick="setPreset(7)"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    7 Hari
                </button>
                <button type="button" onclick="setPreset('month')"
                    class="rounded-lg bg-warm-100/80 px-3 py-2 text-xs font-medium text-warm-600 hover:bg-warm-200 hover:text-warm-800 transition-all active:scale-95">
                    Bulan Ini
                </button>
            </div>
            @if ($hasFilter)
            <span class="text-warm-200 mx-0.5">|</span>
            <a href="{{ route('dashboard') }}"
                class="rounded-lg bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-100 transition-all">
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

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
    if (val === 'month') {
        from.value = `${y}-${m}-01`;
    } else {
        const past = new Date(today);
        past.setDate(past.getDate() - (val - 1));
        const py = past.getFullYear();
        const pm = String(past.getMonth() + 1).padStart(2, '0');
        const pd = String(past.getDate()).padStart(2, '0');
        from.value = `${py}-${pm}-${pd}`;
    }
    document.getElementById('filter-form').requestSubmit();
}
</script>
@endpush

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.05s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 shadow-sm shadow-emerald-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a9.75 9.75 0 0019.5 0M12 6v2.25m0 0a1.5 1.5 0 01-1.5 1.5H9m3-1.5a1.5 1.5 0 011.5 1.5h1.5M12 6a1.5 1.5 0 00-1.5-1.5H9m3 3V6m0 0V4.5" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">{{ $hasFilter ? 'Penjualan' : 'Penjualan Hari Ini' }}</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">Rp {{ number_format($periodSales, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.1s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 shadow-sm shadow-rose-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">{{ $hasFilter ? 'Pengeluaran' : 'Pengeluaran Hari Ini' }}</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">Rp {{ number_format($periodExpenses, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.15s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 shadow-sm shadow-sky-200/50">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M4 7.5h16" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Total Produk</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">{{ $totalProducts }}</p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.2s">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary shadow-sm shadow-theme-shadow">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
            </div>
            <div>
                <p class="text-sm font-medium text-warm-500">Stok Menipis</p>
                <p class="text-2xl font-bold text-warm-900 tracking-tight">{{ $lowStockProducts }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.25s">
        <h3 class="text-base font-semibold text-warm-900 mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
            {{ $hasFilter ? 'Ringkasan Periode' : 'Ringkasan Bulan Ini' }}
        </h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-50/50 p-4 border border-emerald-100">
                <dt class="text-xs text-emerald-700 font-medium">Total Penjualan</dt>
                <dd class="mt-1 text-xl font-bold text-emerald-800">Rp {{ number_format($periodSales, 0, ',', '.') }}</dd>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-rose-50 to-rose-50/50 p-4 border border-rose-100">
                <dt class="text-xs text-rose-700 font-medium">Total Pengeluaran</dt>
                <dd class="mt-1 text-xl font-bold text-rose-800">Rp {{ number_format($periodExpenses, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>

    <div class="animate-fade-in-up rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50" style="animation-delay: 0.3s">
        <h3 class="text-base font-semibold text-warm-900 mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
            Pengeluaran Terbaru
        </h3>
        @if ($recentExpenses->count() > 0)
            <ul class="divide-y divide-warm-100">
                @foreach ($recentExpenses as $expense)
                    <li class="py-3 flex justify-between items-center group hover:bg-warm-50 -mx-2 px-2 rounded-lg transition-colors">
                        <div>
                            <p class="text-sm font-medium text-warm-900">{{ $expense->title }}</p>
                            <p class="text-xs text-warm-400">{{ $expense->expense_date->format('d M Y') }}</p>
                        </div>
                        <span class="text-sm font-semibold text-rose-600">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8">
                <svg class="h-10 w-10 text-warm-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
                <p class="text-sm text-warm-400">Belum ada pengeluaran.</p>
            </div>
        @endif
    </div>
</div>

<div class="animate-fade-in-up rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden" style="animation-delay: 0.35s">
    <div class="flex items-center justify-between p-6 pb-4">
        <h3 class="text-base font-semibold text-warm-900 flex items-center gap-2">
            <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
            Penjualan Terbaru
        </h3>
        <a href="{{ route('sales.create') }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md hover:shadow-theme-shadow transition-all duration-200">
            + Transaksi Baru
        </a>
    </div>
    @if ($recentSales->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-warm-100">
                <thead>
                    <tr class="bg-warm-50/50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-100">
                    @foreach ($recentSales as $sale)
                        <tr class="hover:bg-warm-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-warm-900">{{ $sale->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-warm-700">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-warm-500 capitalize">{{ $sale->payment_method }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 border border-emerald-200">{{ ucfirst($sale->payment_status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-warm-400">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 px-6">
            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
            <p class="text-sm text-warm-400">Belum ada penjualan.</p>
        </div>
    @endif
</div>
@endsection
