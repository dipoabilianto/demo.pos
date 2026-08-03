@extends('layouts.app')
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $settings = $settings::getSettings(); @endphp
@section('title', 'Riwayat Pesanan')
@section('content')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
    @keyframes pulseRing { 0% { box-shadow: 0 0 0 0 rgba(217,119,6,.5); } 70% { box-shadow: 0 0 0 12px rgba(217,119,6,0); } 100% { box-shadow: 0 0 0 0 rgba(217,119,6,0); } }
    .animate-fadeUp { animation: fadeInUp .6s ease-out both; }
    .animate-fadeIn { animation: fadeIn .5s ease-out both; }
    .animate-pulseRing { animation: pulseRing 2s infinite; }
    .card-hover { transition: all .3s cubic-bezier(.22,1,.36,1); }
    .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,.06); }
    .animate-stagger { animation: fadeInUp .5s ease-out both; }
    .scrollbar-thin { scrollbar-width: thin; scrollbar-color: #d6d3d1 transparent; }
    @media (max-width: 640px) { .scrollbar-thin { scrollbar-width: none; } }
</style>

<div class="max-w-3xl mx-auto py-6" x-data="{ filtersOpen: false }">
    @if ($unseenCount > 0)
        <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 flex items-center gap-2 text-sm text-blue-800 animate-fadeIn">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-medium">{{ $unseenCount }} pesanan baru</span>
            <a href="{{ route('orders.history', ['order_status' => 'pending', 'payment_status' => 'pending', 'mark_seen' => 1]) }}" class="ml-auto text-xs font-semibold text-blue-700 underline hover:no-underline">Lihat</a>
        </div>
    @endif
    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('orders.history') }}" class="mb-5 space-y-3">
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan atau nama pelanggan..." class="block w-full rounded-xl border-stone-200 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            </div>
            <button type="button" @click="filtersOpen = !filtersOpen" :class="filtersOpen ? 'bg-theme-primary/10 text-theme-primary border-theme-primary/30' : 'bg-white text-stone-600 border-stone-200'" class="rounded-xl border px-3.5 py-2.5 text-sm font-medium transition-all hover:bg-stone-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
            </button>
        </div>

        <div x-show="filtersOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="rounded-2xl bg-white p-4 shadow-sm border border-stone-200 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                        <option value="">Semua</option>
                        @foreach ($paymentMethods as $pm)
                            <option value="{{ $pm->code }}" {{ request('payment_method') === $pm->code ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Status Pembayaran</label>
                    <select name="payment_status" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Belum Dibayar</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Lunas (Tunai/Transfer)</option>
                        <option value="success" {{ request('payment_status') === 'success' ? 'selected' : '' }}>Lunas (Online)</option>
                        <option value="expired" {{ request('payment_status') === 'expired' ? 'selected' : '' }}>Kadaluwarsa</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Status Pesanan</label>
                    <select name="order_status" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('order_status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="confirmed" {{ request('order_status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="processing" {{ request('order_status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="completed" {{ request('order_status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('order_status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Proses Kasir</label>
                    <select name="process_status" class="block w-full rounded-lg border-stone-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                        <option value="">Semua</option>
                        <option value="unprocessed" {{ request('process_status') === 'unprocessed' ? 'selected' : '' }}>Belum Diproses</option>
                        <option value="processing" {{ request('process_status') === 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="completed" {{ request('process_status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1">
                <button type="submit" class="rounded-lg bg-theme-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 transition-colors">Terapkan Filter</button>
                @if (request()->anyFilled(['search', 'date_from', 'date_to', 'payment_method', 'payment_status', 'order_status', 'process_status']))
                    <a href="{{ route('orders.history') }}" class="rounded-lg border border-stone-200 px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Hapus Filter</a>
                @endif
            </div>
        </div>
    </form>


    {{-- Results count --}}
    @if ($orders->total() > 0 || request()->anyFilled(['search', 'date_from', 'date_to', 'payment_method', 'payment_status', 'order_status']))
        <div class="text-xs text-stone-400 mb-3">
            @if ($orders->total() > 0)
                Menampilkan {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
            @else
                Tidak ada pesanan yang cocok dengan filter
            @endif
        </div>
    @endif

    @forelse ($orders as $order)
        <div class="animate-stagger bg-white rounded-2xl shadow-sm ring-1 ring-stone-100 mb-3 sm:mb-4 overflow-hidden" style="animation-delay: {{ $loop->index * 0.04 }}s;">
            <button class="w-full px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-stone-50/50 transition-colors text-left toggle-trigger">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="shrink-0 flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10 text-theme-primary">
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-90': open}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-stone-900 truncate">
                            {{ $order->order_number }}
                            @if (str_starts_with($order->order_number, 'ORDON-') && !$order->seen_at)
                                <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 ml-1 align-middle">Baru</span>
                            @endif
                        </div>
                        <div class="text-xs font-bold italic text-stone-700">{{ $order->customer_name }}</div>
                        <div class="text-xs text-stone-400">{{ $order->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-sm font-bold text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    @php
                        $ps = $order->payment_status;
                        $os = $order->order_status;
                        $overdue = $ps === 'pending' && $order->created_at->diffInMinutes(now()) > 5;
                        $pColors = ['pending' => ($overdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'), 'success' => 'bg-emerald-100 text-emerald-800', 'expired' => 'bg-red-100 text-red-800', 'failed' => 'bg-red-100 text-red-800', 'paid' => 'bg-emerald-100 text-emerald-800'];
                        $pLabels = ['pending' => ($overdue ? 'Belum Dibayar (>5 mnt)' : 'Belum Dibayar'), 'success' => 'Lunas', 'expired' => 'Kadaluwarsa', 'failed' => 'Gagal', 'paid' => 'Lunas'];
                    @endphp
                    @if (in_array($ps, ['success', 'paid']))
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold italic {{ $pColors[$ps] ?? 'bg-stone-100 text-stone-600' }}">{{ $pLabels[$ps] ?? $ps }} · {{ $order->payment_method === 'cash' ? 'Tunai' : ($order->payment_method === 'transfer' ? 'Transfer' : 'Online') }}</span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $pColors[$ps] ?? 'bg-stone-100 text-stone-600' }}">{{ $pLabels[$ps] ?? $ps }}</span>
                    @endif
                    @php
                        $procBadge = '';
                        if ($order->order_status === 'completed') {
                            $procBadge = '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-800">Selesai</span>';
                        } elseif ($order->processed_by) {
                            $name = e($order->processedBy->name ?? 'Kasir');
                            $procBadge = '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-blue-100 text-blue-800">Diproses ' . $name . '</span>';
                        } elseif ($order->order_status !== 'cancelled') {
                            $procBadge = '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-stone-100 text-stone-600">Belum Diproses</span>';
                        }
                    @endphp
                    {!! $procBadge !!}
                </div>
            </button>

            <div class="border-t border-stone-100 overflow-hidden transition-all duration-200" style="max-height: 0;">
                <div class="px-5 py-4 space-y-3">
                    <div class="flex items-center gap-2 text-xs">
                        @php
                            $oColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'processing' => 'bg-blue-100 text-blue-800', 'shipped' => 'bg-indigo-100 text-indigo-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-red-100 text-red-800'];
                            $oLabels = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'confirmed' => 'Dikonfirmasi'];
                        @endphp
                        <span class="font-medium text-stone-500">Status:</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 font-semibold {{ $oColors[$os] ?? 'bg-stone-100 text-stone-600' }}">{{ $oLabels[$os] ?? $os }}</span>
                        @if (!$order->processed_by && $os !== 'cancelled' && $os !== 'completed')
                            <form action="{{ route('orders.process', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg bg-theme-primary px-3.5 py-1.5 text-xs font-bold text-white hover:opacity-90 active:scale-95 shadow-sm transition-all">Proses</button>
                            </form>
                        @endif
                        @if ($order->processed_by && $os !== 'completed' && $os !== 'cancelled')
                            <form action="{{ route('orders.complete', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="rounded-lg ring-1 ring-inset ring-theme-primary text-theme-primary bg-white px-3.5 py-1.5 text-xs font-bold hover:bg-theme-primary/10 active:scale-95 transition-all">Selesai</button>
                            </form>
                        @endif
                    </div>

                    <div class="space-y-1">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-600">{{ $item->product_name }} <span class="text-stone-400">×{{ $item->quantity }}</span></span>
                                <span class="font-medium text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="border-stone-100">

                    <div class="space-y-0.5 text-sm">
                        <div class="flex justify-between text-stone-500">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                                <div class="flex justify-between text-stone-500">
                                    <span>Diskon</span>
                                    <span>{{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : 'Rp 0' }}</span>
                                </div>
                                @php $vc = $order->voucher->code ?? $order->voucher_code ?? null; @endphp
                                @if ($vc)
                                    <div class="flex justify-between text-stone-400 text-[11px]">
                                        <span>Voucher</span>
                                        <span class="font-mono">{{ $vc }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between font-bold text-stone-900 pt-1 border-t border-stone-200">
                            <span>Total</span>
                            <span class="text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="text-xs text-stone-400">
                        {{ $order->customer_name }}@if($order->customer_phone) · {{ $order->customer_phone }}@endif
                    </div>

                    <div class="flex items-center justify-between pt-1 gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="text-sm font-medium text-theme-primary hover:text-theme-primary transition-colors">Detail &rarr;</a>
                        <div class="flex items-center gap-2">
                            @if (!in_array($ps, ['success', 'paid', 'failed', 'expired']) && $os !== 'cancelled')
                                <a href="{{ route('orders.payment', $order) }}" class="rounded-lg bg-theme-primary px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90 transition-colors">Bayar</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-20 animate-fadeUp">
            <div class="inline-flex h-24 w-24 items-center justify-center rounded-full bg-theme-primary/10 mb-6">
                <svg class="h-12 w-12 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.801 0a2.25 2.25 0 01-1.545-1.578A2.25 2.25 0 0012 2.25h-1.5a2.25 2.25 0 00-2.18 2.622 2.25 2.25 0 01-1.545 2.578 2.25 2.25 0 000 1.5A2.25 2.25 0 007.5 10.5h1.5a2.25 2.25 0 002.18-2.622 2.25 2.25 0 011.545-1.28M12 12.75h3.75m-3.75 3h3.75m-4.473-7.5H8.25" /></svg>
            </div>
            <h2 class="text-xl font-bold text-stone-900 mb-2">Belum Ada Pesanan</h2>
            <p class="text-sm text-stone-500 max-w-xs mx-auto mb-6">Kamu belum melakukan pemesanan apapun. Yuk, pesan roti favoritmu sekarang!</p>
            <a href="{{ route('orders.catalog') }}" class="inline-flex items-center gap-2 rounded-xl bg-theme-gradient-r px-6 py-3 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow hover:opacity-90 active:scale-95 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.25v2.5c0 .777.845 1.74 1.976 1.834a30.633 30.633 0 00-1.614 4.481m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M6.362 17.25l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                Mulai Pesan
            </a>
        </div>
    @endforelse

    @if ($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.is-open .border-t').forEach(el => {
    el.style.maxHeight = el.scrollHeight + 'px';
});
document.querySelectorAll('.toggle-trigger').forEach(btn => {
    btn.addEventListener('click', function() {
        const card = this.closest('div');
        const details = card.querySelector('.border-t');
        card.classList.toggle('is-open');
        if (card.classList.contains('is-open')) {
            details.style.maxHeight = details.scrollHeight + 'px';
        } else {
            details.style.maxHeight = '0';
        }
    });
});
</script>
@endpush