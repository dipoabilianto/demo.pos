@extends('layouts.app')
@section('title', 'Laporan')
@section('subtitle', 'Filter dan cetak laporan')
@section('content')
<style>
    @keyframes fadeInUp { from { opacity:0;transform:translateY(16px); } to { opacity:1;transform:translateY(0); } }
    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
    .anim-fadeInUp { animation: fadeInUp .4s ease-out both; }
    .anim-fadeIn { animation: fadeIn .3s ease-out both; }
    .stagger-1 { animation-delay: calc(var(--i,0) * 0.05s); }
</style>

@php
    $tab = $tab ?? 'sales';
    $stockSub = request('stock_sub', 'current');
    $allowedTabs = [];
    $tabIcons = [
        'sales' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />',
        'expenses' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />',
        'stock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />',
        'raw-materials' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />',
        'stock-opname' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" />',
        'financial' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />',
    ];
    $tabLabels = [
        'sales' => 'Penjualan',
        'expenses' => 'Pengeluaran',
        'stock' => 'Stok',
        'raw-materials' => 'Bahan Baku',
        'stock-opname' => 'Stok Opname',
        'financial' => 'Keuangan',
    ];
    if (auth()->user()?->hasPermission('reports.sales')) $allowedTabs[] = 'sales';
    if (auth()->user()?->hasPermission('reports.expenses')) $allowedTabs[] = 'expenses';
    if (auth()->user()?->hasPermission('reports.stock')) $allowedTabs[] = 'stock';
    if (auth()->user()?->hasPermission('reports.raw-materials')) $allowedTabs[] = 'raw-materials';
    if (auth()->user()?->hasPermission('reports.stock-opname')) $allowedTabs[] = 'stock-opname';
    if (auth()->user()?->hasPermission('reports.financial')) $allowedTabs[] = 'financial';
    $defaultTab = in_array($tab, $allowedTabs) ? $tab : ($allowedTabs[0] ?? 'sales');
@endphp

<div class="space-y-6">

    {{-- Tab Navigation --}}
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-warm-200/50 p-1.5 overflow-x-auto">
        <div class="flex gap-1 min-w-max">
            @foreach($allowedTabs as $t)
            <a href="{{ request()->fullUrlWithQuery(['tab' => $t]) }}"
                class="rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200 {{ $tab === $t ? 'bg-theme-primary text-white shadow-sm shadow-theme-primary/20' : 'text-warm-600 hover:bg-warm-100 hover:text-warm-800' }}">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $tabIcons[$t] !!}</svg>
                    <span>{{ $tabLabels[$t] }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    @php
        $reportBranchId = session('branch_id');
        $reportBranch = $reportBranchId ? \App\Models\Branch::find($reportBranchId) : null;
    @endphp
    <div class="flex items-center gap-2 px-1 pb-1">
        <svg class="h-4 w-4 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
        <span class="text-sm font-medium text-warm-600">
            {{ $reportBranch ? 'Cabang: ' . $reportBranch->name : 'Semua Cabang' }}
        </span>
    </div>

    {{-- SALES TAB --}}
    @if($tab === 'sales')
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="sales">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                @include('reports.partials.date-presets')
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Metode</label>
                    <select name="payment_method"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="">Semua</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="xendit" {{ request('payment_method') === 'xendit' ? 'selected' : '' }}>Xendit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            Filter
                        </span>
                    </button>
                    <a href="{{ route('reports.sales.print', request()->except(['tab','stock_sub','material_id'])) }}" target="_blank"
                        class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                            Cetak / PDF
                        </span>
                    </a>
                </div>
            </div>
        </form>

        @if($sales->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @php $cards = [
                ['label' => 'Total Penjualan', 'value' => 'Rp ' . number_format($salesTotal->total ?? 0, 0, ',', '.'), 'color' => 'text-warm-900', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'],
                ['label' => 'Total Transaksi', 'value' => $salesTotal->count ?? 0, 'color' => 'text-warm-900', 'icon' => 'M3 3h1.5v3h15V3M3 21h1.5v-6h15v6M3 12h1.5v3h15v-3'],
                ['label' => 'Total Diskon', 'value' => 'Rp ' . number_format($salesTotal->discount ?? 0, 0, ',', '.'), 'color' => 'text-rose-600', 'icon' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Total Pajak', 'value' => 'Rp ' . number_format($salesTotal->tax ?? 0, 0, ',', '.'), 'color' => 'text-warm-900', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.801 0a2.25 2.25 0 01-1.545-1.578A2.25 2.25 0 0012 2.25h-1.5a2.25 2.25 0 00-2.18 2.622 2.25 2.25 0 01-1.545 2.578 2.25 2.25 0 000 1.5A2.25 2.25 0 007.5 10.5h1.5a2.25 2.25 0 002.18-2.622 2.25 2.25 0 011.545-1.28M12 12.75h3.75m-3.75 3h3.75m-4.473-7.5H8.25'],
            ]; @endphp
            @foreach($cards as $i => $card)
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-warm-200/50 anim-fadeInUp" style="animation-delay:{{ $i * 0.06 }}s">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-theme-primary/10 shrink-0">
                        <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <p class="text-xs text-warm-500 font-medium">{{ $card['label'] }}</p>
                </div>
                <p class="text-lg font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden anim-fadeInUp" style="animation-delay:0.2s">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Item</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Total</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Metode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $sale->invoice_number }}</td>
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-warm-700">{{ $sale->items->sum('quantity') }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right text-warm-900">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-0.5 text-xs font-medium text-warm-700 capitalize">{{ $sale->payment_method }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($sales->hasPages())
                <div class="px-5 py-4 border-t border-warm-100 bg-gradient-to-r from-warm-50/50 to-white">{{ $sales->links() }}</div>
            @endif
        </div>
        @else
        <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-warm-200/50">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-primary/10 mb-4">
                <svg class="h-8 w-8 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            </div>
            <p class="text-warm-500 font-medium">Belum ada data penjualan</p>
            <p class="text-warm-400 text-sm mt-1">Pilih periode dan klik Filter untuk menampilkan laporan penjualan.</p>
        </div>
        @endif
    @endif

    {{-- EXPENSES TAB --}}
    @if($tab === 'expenses')
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="expenses">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                @include('reports.partials.date-presets')
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Kategori</label>
                    <select name="category"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="">Semua</option>
                        @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            Filter
                        </span>
                    </button>
                    <a href="{{ route('reports.expenses.print', request()->except(['tab','stock_sub','material_id'])) }}" target="_blank"
                        class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                            Cetak / PDF
                        </span>
                    </a>
                </div>
            </div>
        </form>

        @if($expenses->count() > 0)
        @php $expTotal = $expensesTotal->total ?? 0; $expCount = $expensesTotal->count ?? 0; @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php $ecards = [
                ['label' => 'Total Pengeluaran', 'value' => 'Rp ' . number_format($expTotal, 0, ',', '.'), 'color' => 'text-rose-600', 'icon' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'],
                ['label' => 'Jumlah Transaksi', 'value' => $expCount, 'color' => 'text-warm-900', 'icon' => 'M3 3h1.5v3h15V3M3 21h1.5v-6h15v6M3 12h1.5v3h15v-3'],
                ['label' => 'Rata-rata', 'value' => 'Rp ' . ($expCount ? number_format($expTotal / $expCount, 0, ',', '.') : '0'), 'color' => 'text-warm-900', 'icon' => 'M3 3h1.5v3h15V3M3 21h1.5v-6h15v6M3 12h1.5v3h15v-3'],
            ]; @endphp
            @foreach($ecards as $i => $card)
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-warm-200/50 anim-fadeInUp" style="animation-delay:{{ $i * 0.06 }}s">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 shrink-0">
                        <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <p class="text-xs text-warm-500 font-medium">{{ $card['label'] }}</p>
                </div>
                <p class="text-lg font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden anim-fadeInUp" style="animation-delay:0.15s">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Judul</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @foreach($expenses as $expense)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $expense->expense_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $expense->title }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                <span class="inline-flex items-center rounded-lg bg-warm-100 px-2.5 py-0.5 text-xs font-medium text-warm-700">{{ $expense->category ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right text-rose-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($expenses->hasPages())
                <div class="px-5 py-4 border-t border-warm-100 bg-gradient-to-r from-warm-50/50 to-white">{{ $expenses->links() }}</div>
            @endif
        </div>
        @else
        <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-warm-200/50">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 mb-4">
                <svg class="h-8 w-8 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </div>
            <p class="text-warm-500 font-medium">Belum ada data pengeluaran</p>
            <p class="text-warm-400 text-sm mt-1">Pilih periode dan klik Filter untuk menampilkan laporan pengeluaran.</p>
        </div>
        @endif
    @endif

    {{-- STOCK TAB --}}
    @if($tab === 'stock')
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="stock">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Tipe Stok</label>
                    <select name="stock_sub"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="current" {{ $stockSub === 'current' ? 'selected' : '' }}>Stok Saat Ini</option>
                        <option value="mutasi" {{ $stockSub === 'mutasi' ? 'selected' : '' }}>Kartu Stok</option>
                        <option value="minimum" {{ $stockSub === 'minimum' ? 'selected' : '' }}>Minimum Stok</option>
                    </select>
                </div>
                @if($stockSub === 'mutasi')
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Bahan</label>
                    <select name="material_id"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="">Pilih Bahan</option>
                        @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Dari</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Sampai</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                @include('reports.partials.date-presets')
                <button type="submit"
                    class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                        Filter
                    </span>
                </button>
                @else
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <button type="submit"
                    class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.5V3m0 18v-.75m-7.5-7.5H3m18 0h-.75"/></svg>
                        Tampilkan
                    </span>
                </button>
                @endif
                <a href="{{ route('reports.stock.print', array_merge(request()->except(['tab', 'stock_sub']), ['tab' => request('stock_sub', 'current')])) }}" target="_blank"
                    class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                        Cetak / PDF
                    </span>
                </a>
            </div>
        </form>

        @if($stockSub === 'current' || $stockSub === 'minimum')
        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nama Bahan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Stok</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Min. Stok</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @forelse($materials->filter(fn($m) => $stockSub !== 'minimum' || $m->isLowStock()) as $material)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $material->name }}</td>
                            <td class="px-5 py-3.5 text-sm text-warm-600">{{ $material->unit }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right {{ $material->isLowStock() ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($material->current_stock, 0) }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-warm-500">{{ number_format($material->min_stock, 0) }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                @if($material->isLowStock())
                                <span class="inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">Stok Menipis</span>
                                @else
                                <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-warm-400">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @elseif($stockSub === 'mutasi')
        @php $selectedMaterial = $materials->firstWhere('id', (int)request('material_id')); @endphp
        @if(request('material_id') && $selectedMaterial)
        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-warm-100 bg-gradient-to-r from-warm-50/70 to-white">
                <span class="text-sm font-semibold text-warm-900">Kartu Stok: {{ $selectedMaterial->name ?? '' }}</span>
                <span class="text-xs text-warm-500 ml-2">({{ $selectedMaterial->unit ?? '' }})</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Qty</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @forelse($stockTransactions as $t)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-medium {{ $t->type === 'in' ? 'bg-emerald-100 text-emerald-700' : ($t->type === 'out' ? 'bg-rose-100 text-rose-700' : 'bg-warm-100 text-warm-700') }}">
                                    {{ $t->type === 'in' ? 'Masuk' : ($t->type === 'out' ? 'Keluar' : 'Opname') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right {{ $t->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $t->type === 'in' ? '+' : '-' }}{{ number_format($t->quantity, 0) }}
                            </td>
                            <td class="px-5 py-3.5 text-sm text-warm-600">{{ $t->note ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-warm-400">Belum ada transaksi untuk bahan ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-warm-200/50">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-primary/10 mb-4">
                <svg class="h-8 w-8 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
            </div>
            <p class="text-warm-500 font-medium">Pilih bahan baku</p>
            <p class="text-warm-400 text-sm mt-1">Pilih bahan baku dan klik Filter untuk melihat kartu stok.</p>
        </div>
        @endif
        @endif
    @endif

    {{-- RAW MATERIALS TAB --}}
    @if($tab === 'raw-materials')
        @php
            $totalMaterials = $materials->count();
            $lowStockCount = $materials->filter(fn($m) => $m->isLowStock())->count();
            $totalStock = $materials->sum('current_stock');
        @endphp
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="raw-materials">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <a href="{{ route('reports.raw-materials.print', request()->except(['tab','stock_sub','material_id'])) }}" target="_blank"
                    class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                        Cetak / PDF
                    </span>
                </a>
            </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @php $rcards = [
                ['label' => 'Total Bahan Baku', 'value' => $totalMaterials, 'color' => 'text-warm-900', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.801 0a2.25 2.25 0 01-1.545-1.578A2.25 2.25 0 0012 2.25h-1.5a2.25 2.25 0 00-2.18 2.622 2.25 2.25 0 01-1.545 2.578 2.25 2.25 0 000 1.5A2.25 2.25 0 007.5 10.5h1.5a2.25 2.25 0 002.18-2.622 2.25 2.25 0 011.545-1.28M12 12.75h3.75m-3.75 3h3.75m-4.473-7.5H8.25'],
                ['label' => 'Total Stok', 'value' => number_format($totalStock, 0), 'color' => 'text-warm-900', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
                ['label' => 'Bahan Stok Menipis', 'value' => $lowStockCount, 'color' => $lowStockCount > 0 ? 'text-rose-600' : 'text-emerald-600', 'icon' => 'M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z'],
            ]; @endphp
            @foreach($rcards as $i => $card)
            <div class="rounded-2xl bg-white p-5 shadow-sm border border-warm-200/50 anim-fadeInUp" style="animation-delay:{{ $i * 0.06 }}s">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl {{ $i === 2 && $lowStockCount > 0 ? 'bg-rose-50' : 'bg-theme-primary/10' }} shrink-0">
                        <svg class="h-4 w-4 {{ $i === 2 && $lowStockCount > 0 ? 'text-rose-500' : 'text-theme-primary' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <p class="text-xs text-warm-500 font-medium">{{ $card['label'] }}</p>
                </div>
                <p class="text-lg font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
            </div>
            @endforeach
        </div>

        @if($materials->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider" style="width:50px">No</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Nama Bahan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Satuan</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Stok</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Min. Stok</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @forelse($materials as $material)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $material->name }}</td>
                            <td class="px-5 py-3.5 text-sm text-warm-600">{{ $material->unit }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right {{ $material->isLowStock() ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($material->current_stock, 0) }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-warm-500">{{ number_format($material->min_stock, 0) }}</td>
                            <td class="px-5 py-3.5 text-sm">
                                @if($material->isLowStock())
                                <span class="inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">Stok Menipis</span>
                                @else
                                <span class="inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-warm-400">Belum ada bahan baku</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endif

    {{-- STOCK OPNAME TAB --}}
    @if($tab === 'stock-opname')
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="stock-opname">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                @include('reports.partials.date-presets')
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Bahan</label>
                    <select name="material_id"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="">Semua</option>
                        @foreach($materials as $material)
                        <option value="{{ $material->id }}" {{ request('material_id') == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            Filter
                        </span>
                    </button>
                    <a href="{{ route('reports.stock-opname.print', request()->except(['tab','stock_sub'])) }}" target="_blank"
                        class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                            Cetak / PDF
                        </span>
                    </a>
                </div>
            </div>
        </form>

        @if($opnameTransactions->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Bahan</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Selisih</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @foreach($opnameTransactions as $t)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $t->rawMaterial?->name ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-sm font-semibold text-right text-theme-primary">{{ number_format($t->quantity, 0) }}</td>
                            <td class="px-5 py-3.5 text-sm text-warm-600">{{ $t->note ?: '-' }}</td>
                            <td class="px-5 py-3.5 text-sm text-warm-500">{{ $t->user?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-warm-200/50">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-primary/10 mb-4">
                <svg class="h-8 w-8 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" /></svg>
            </div>
            <p class="text-warm-500 font-medium">Belum ada data stok opname</p>
            <p class="text-warm-400 text-sm mt-1">Pilih periode dan klik Filter untuk menampilkan data stok opname.</p>
        </div>
        @endif
    @endif

    {{-- FINANCIAL TAB --}}
    @if($tab === 'financial')
        @php
            $totalRevenue = $sales->sum('total');
            $totalExpensesAmount = $expenses->sum('amount');
            $hpp = $sales->sum(function($s) { return $s->items->sum(function($item) { return ($item->product ? $item->product->cost_price : 0) * $item->quantity; }); });
            $labaKotor = $totalRevenue - $hpp;
            $labaBersih = $labaKotor - $totalExpensesAmount;
            $marginKotor = $totalRevenue > 0 ? round(($labaKotor / $totalRevenue) * 100, 1) : 0;
            $marginBersih = $totalRevenue > 0 ? round(($labaBersih / $totalRevenue) * 100, 1) : 0;
            $ratioBiaya = $totalRevenue > 0 ? round(($totalExpensesAmount / $totalRevenue) * 100, 1) : 0;
        @endphp
        <form class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-warm-200/50" method="GET">
            <input type="hidden" name="tab" value="financial">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                </div>
                @include('reports.partials.date-presets')
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Grup Per Periode</label>
                    <select name="group"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="">Tanpa Grup</option>
                        <option value="day" {{ request('group') === 'day' ? 'selected' : '' }}>Harian</option>
                        <option value="week" {{ request('group') === 'week' ? 'selected' : '' }}>Mingguan</option>
                        <option value="month" {{ request('group') === 'month' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-warm-500 mb-1.5">Orientasi Cetak</label>
                    <select name="orientation"
                        class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white transition-all">
                        <option value="portrait" {{ request('orientation', 'portrait') === 'portrait' ? 'selected' : '' }}>Potrait</option>
                        <option value="landscape" {{ request('orientation') === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-theme-primary px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                            Filter
                        </span>
                    </button>
                    <a href="{{ route('reports.financial.print', request()->except(['tab','stock_sub','material_id'])) }}" target="_blank"
                        class="rounded-xl bg-white border border-warm-200 px-5 py-2.5 text-sm font-semibold text-warm-700 shadow-sm hover:bg-warm-50 hover:border-theme-primary/30 active:scale-95 transition-all duration-200">
                        <span class="flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                            Cetak / PDF
                        </span>
                    </a>
                </div>
            </div>
        </form>

        @if($sales->count() > 0 || $expenses->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-warm-200/50">
                <h3 class="text-sm font-bold text-warm-900 mb-4 pb-3 border-b border-warm-100 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                    </span>
                    LAPORAN LABA RUGI
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-1.5">
                        <span class="text-warm-700 font-semibold">PENDAPATAN</span>
                        <span class="font-bold text-warm-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 pl-4">
                        <span class="text-warm-500">Total Penjualan</span>
                        <span class="text-warm-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-warm-100 pt-3 flex justify-between items-center">
                        <span class="text-warm-700 font-semibold">HPP</span>
                        <span class="font-semibold text-rose-600">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t-2 border-warm-200 pt-3 flex justify-between items-center">
                        <span class="text-warm-900 font-bold">LABA KOTOR</span>
                        <span class="font-bold text-warm-900">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-warm-100 pt-3 flex justify-between items-center">
                        <span class="text-warm-700 font-semibold">BIAYA OPERASIONAL</span>
                        <span class="font-semibold text-rose-600">Rp {{ number_format($totalExpensesAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 pl-4">
                        <span class="text-warm-500">Total Pengeluaran</span>
                        <span class="text-rose-600">Rp {{ number_format($totalExpensesAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t-2 border-warm-900 pt-3 flex justify-between items-center">
                        <span class="text-warm-900 font-bold text-base">LABA BERSIH</span>
                        <span class="text-lg font-extrabold {{ $labaBersih >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-warm-200/50">
                <h3 class="text-sm font-bold text-warm-900 mb-4 pb-3 border-b border-warm-100 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                    </span>
                    RASIO KEUANGAN
                </h3>
                <div class="space-y-4">
                    @php $ratios = [
                        ['label' => 'Margin Laba Kotor', 'value' => number_format($marginKotor, 1) . '%', 'color' => $marginKotor >= 0 ? 'text-emerald-600' : 'text-rose-600', 'desc' => 'Persentase laba kotor terhadap pendapatan'],
                        ['label' => 'Margin Laba Bersih', 'value' => number_format($marginBersih, 1) . '%', 'color' => $marginBersih >= 0 ? 'text-emerald-600' : 'text-rose-600', 'desc' => 'Persentase laba bersih terhadap pendapatan'],
                        ['label' => 'Rasio Biaya terhadap Pendapatan', 'value' => number_format($ratioBiaya, 1) . '%', 'color' => 'text-warm-900', 'desc' => 'Persentase biaya operasional terhadap pendapatan'],
                    ]; @endphp
                    @foreach($ratios as $ratio)
                    <div class="rounded-xl bg-gradient-to-r from-warm-50 to-white p-4">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-medium text-warm-700">{{ $ratio['label'] }}</span>
                            <span class="text-lg font-bold {{ $ratio['color'] }}">{{ $ratio['value'] }}</span>
                        </div>
                        <p class="text-xs text-warm-400">{{ $ratio['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($periods->count() > 0)
        <div class="rounded-2xl bg-white shadow-sm border border-warm-200/50 overflow-hidden">
            <div class="px-5 py-4 border-b border-warm-100 bg-gradient-to-r from-warm-50/70 to-white flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-theme-primary/10">
                    <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m0 0V4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v13.5c0 .621-.504 1.125-1.125 1.125"/></svg>
                </span>
                <span class="text-sm font-semibold text-warm-900">TREN PERIODE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-gradient-to-r from-warm-50 to-white">
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Periode</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Pendapatan</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">HPP</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Laba Kotor</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Biaya</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Laba Bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @foreach($periods as $p)
                        <tr class="hover:bg-warm-50/70 transition-colors">
                            <td class="px-5 py-3.5 text-sm font-medium text-warm-900">{{ $p['label'] }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-warm-700">Rp {{ number_format($p['revenue'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-rose-600">Rp {{ number_format($p['hpp'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-sm text-right font-medium {{ $p['laba_kotor'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($p['laba_kotor'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-sm text-right text-rose-600">Rp {{ number_format($p['expenses'], 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-sm text-right font-bold {{ $p['laba_bersih'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">Rp {{ number_format($p['laba_bersih'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @else
        <div class="rounded-2xl bg-white p-16 text-center shadow-sm border border-warm-200/50">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-primary/10 mb-4">
                <svg class="h-8 w-8 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
            </div>
            <p class="text-warm-500 font-medium">Belum ada data keuangan</p>
            <p class="text-warm-400 text-sm mt-1">Pilih periode dan klik Filter untuk menampilkan laporan keuangan.</p>
        </div>
        @endif
    @endif
</div>
@endsection