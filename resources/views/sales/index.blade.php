@extends('layouts.app')
@section('title', 'Penjualan')
@section('subtitle', 'Total: Rp ' . number_format($totalRevenue, 0, ',', '.'))
@section('content')


<form class="mb-6 flex flex-wrap gap-3" method="GET">
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Cari</label>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice atau nama produk..." class="rounded-xl border-warm-200 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white w-56">
        </div>
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Dari Tanggal</label>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Sampai Tanggal</label>
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
    </div>
    <div>
        <label class="block text-xs font-medium text-warm-500 mb-1">Metode</label>
        <select name="payment_method" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            <option value="">Semua Metode</option>
            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tunai</option>
            <option value="transfer" {{ request('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="xendit" {{ request('payment_method') === 'xendit' ? 'selected' : '' }}>Xendit</option>
        </select>
    </div>
    <div class="self-end">
        <button type="submit" class="rounded-xl bg-warm-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-warm-800 transition-all duration-200 shadow-sm">Filter</button>
    </div>
</form>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-warm-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($sales as $sale)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">{{ $sale->invoice_number }}</td>
                        <td class="px-6 py-4 text-sm text-warm-500">{{ $sale->items->count() }} item</td>
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium {{ $sale->payment_method === 'cash' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($sale->payment_method === 'transfer' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-theme-primary/10 text-theme-primary border border-theme-primary/20') }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $sale->payment_method === 'cash' ? 'bg-emerald-500' : ($sale->payment_method === 'transfer' ? 'bg-sky-500' : 'bg-theme-primary') }}"></span>
                                {{ ucfirst($sale->payment_method) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sale->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-theme-primary/10 text-theme-primary border border-theme-primary/20' }}">
                                {{ $sale->payment_status === 'paid' ? 'Lunas' : 'Sebagian' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-warm-400">{{ $sale->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center gap-1 rounded-lg bg-theme-primary/10 px-3 py-1.5 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
                            <p class="text-sm text-warm-400">Belum ada penjualan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($sales->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $sales->links() }}</div>
    @endif
</div>
@endsection
