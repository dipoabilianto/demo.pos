@extends('layouts.app')
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $settings = $settings::getSettings(); @endphp
@section('title', 'Pembayaran - ' . ($order->order_number ?? ''))
@section('content')
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
    @keyframes shimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
    @keyframes checkmark { 0% { stroke-dashoffset: 100; opacity: 0; } 60% { opacity: 1; } 100% { stroke-dashoffset: 0; opacity: 1; } }
    @keyframes confettiFall { 0% { transform: translateY(-10px) rotate(0deg); opacity: 0; } 20% { opacity: 1; } 100% { transform: translateY(100px) rotate(720deg); opacity: 0; } }
    @keyframes pulseRing { 0% { box-shadow: 0 0 0 0 rgba(5,150,105,.4); } 70% { box-shadow: 0 0 0 16px rgba(5,150,105,0); } 100% { box-shadow: 0 0 0 0 rgba(5,150,105,0); } }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes toastIn { from { opacity:0;transform:translateY(12px) scale(0.95); } to { opacity:1;transform:translateY(0) scale(1); } }
    .animate-toastIn { animation:toastIn .35s cubic-bezier(.22,1,.36,1) both; }
    .animate-fadeUp { animation: fadeInUp .6s ease-out both; }
    .animate-scaleIn { animation: scaleIn .5s cubic-bezier(.22,1,.36,1) both; }
    .animate-float { animation: float 4s ease-in-out infinite; }
    .animate-checkmark { stroke-dasharray: 100; animation: checkmark .6s cubic-bezier(.22,1,.36,1) forwards; animation-delay: .2s; }
    .animate-pulseRing { animation: pulseRing 2s infinite; }
    .pay-option { transition: all .2s ease; }
    .pay-option.selected { border-color: #d97706; background: linear-gradient(135deg, #fffbeb, #fef3c7); box-shadow: 0 4px 14px rgba(217,119,6,.15); }
    .pay-option:not(.selected):hover { border-color: #d97706; background: #fefce8; transform: translateY(-1px); }
    .confetti-dot { position: absolute; width: 6px; height: 6px; border-radius: 50%; animation: confettiFall 1.2s ease-out forwards; }
    .scrollbar-thin { scrollbar-width: thin; scrollbar-color: #d6d3d1 transparent; }
</style>

<div class="max-w-2xl mx-auto py-8 sm:py-12">
    {{-- Success Animation --}}
    <div class="text-center mb-8 animate-fadeUp">
        <div class="relative inline-flex mb-5">
            <div class="h-20 w-20 rounded-full bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center animate-pulseRing">
                <svg class="h-10 w-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path class="animate-checkmark" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" />
                </svg>
            </div>
            <span class="confetti-dot" style="left:calc(50%-24px);top:4px;background:#f59e0b;animation-delay:.3s"></span>
            <span class="confetti-dot" style="left:calc(50%+20px);top:2px;background:#10b981;animation-delay:.5s"></span>
            <span class="confetti-dot" style="left:calc(50%+36px);top:20px;background:#f43f5e;animation-delay:.4s"></span>
            <span class="confetti-dot" style="left:calc(50%-34px);top:24px;background:#8b5cf6;animation-delay:.6s"></span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900">Pesanan Berhasil Dibuat!</h1>
        <p class="mt-2 text-stone-500">Nomor Pesanan: <strong class="text-theme-primary font-bold text-base">{{ $order->order_number }}</strong></p>
        <p class="mt-1 text-sm text-stone-400">Silakan pilih metode pembayaran di bawah.</p>
    </div>

    {{-- Order Summary --}}
    <div class="animate-scaleIn rounded-2xl bg-white/90 backdrop-blur-sm p-5 sm:p-7 ring-1 ring-stone-200/70 shadow-sm hover:shadow-md transition-all duration-300 mb-6" style="animation-delay: .15s;">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10">
                <svg class="h-5 w-5 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-8.25 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
            </div>
            <h2 class="text-base sm:text-lg font-bold text-stone-900">Ringkasan Pesanan</h2>
        </div>
        <div class="space-y-3 divide-y divide-stone-100">
            @foreach ($order->items as $item)
            <div class="flex justify-between items-center py-2 first:pt-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-stone-600">{{ $item->product_name }}</span>
                    <span class="text-xs text-stone-400 bg-stone-100 rounded-full px-2 py-0.5 font-medium">×{{ $item->quantity }}</span>
                </div>
                <span class="text-sm font-semibold text-stone-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        <div class="border-t border-stone-200/70 mt-4 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-stone-500">
                <span>Subtotal</span>
                <span class="font-medium text-stone-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-stone-500">
                <span>Diskon</span>
                <span class="font-medium text-stone-900">
                    {{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : 'Rp 0' }}
                </span>
            </div>
            <div class="flex justify-between text-base font-bold text-stone-900 pt-3 border-t border-stone-200">
                <span>Total Pembayaran</span>
                <span class="text-theme-primary text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Method Selection --}}
    <div id="pay-section" class="animate-scaleIn rounded-2xl bg-white/90 backdrop-blur-sm p-5 sm:p-7 ring-1 ring-stone-200/70 shadow-sm hover:shadow-md transition-all duration-300 mb-6" style="animation-delay: .25s;">
        <div class="flex items-center gap-3 mb-5">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
            <h2 class="text-base sm:text-lg font-bold text-stone-900">Pilih Metode Pembayaran</h2>
        </div>

        <div id="pay-error" class="hidden rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200 mb-4 flex items-center gap-2">
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <span id="pay-error-text"></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            @if ($cashMethod && $cashMethod->is_active)
            <button type="button" class="pay-option flex flex-col items-center gap-1.5 rounded-xl border-2 border-stone-200 px-3 py-3.5 text-center" data-method="cash">
                <div class="h-7 w-7 flex items-center justify-center rounded-lg bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
                </div>
                <span class="text-sm font-semibold text-stone-800">Tunai</span>
                <span class="text-[10px] text-stone-400 font-medium">Bayar langsung</span>
            </button>
            @endif
            @if ($transferMethod && $transferMethod->is_active)
            <button type="button" class="pay-option flex flex-col items-center gap-1.5 rounded-xl border-2 border-stone-200 px-3 py-3.5 text-center" data-method="transfer">
                <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-sky-100">
                    <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                </div>
                <span class="text-sm font-semibold text-stone-800">Transfer</span>
                <span class="text-[10px] text-stone-400 font-medium">Bayar langsung</span>
            </button>
            @endif
            @forelse ($paymentMethods as $method)
                <button type="button" class="pay-option flex flex-col items-center gap-1.5 rounded-xl border-2 border-stone-200 px-3 py-3.5 text-center" data-method="{{ $method->code }}">
                    <img src="{{ asset('images/payment/' . $method->code . '.svg') }}" alt="{{ $method->name }}" class="h-7 object-contain">
                    <span class="text-sm font-semibold text-stone-800">{{ $method->name }}</span>
                    <span class="text-[10px] text-stone-400 font-medium">{{ $method->description }}</span>
                </button>
            @empty
                <p class="col-span-full text-sm text-stone-400 text-center py-8">Tidak ada metode pembayaran yang tersedia.</p>
            @endforelse
        </div>

        <button id="pay-btn" disabled class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-theme-gradient-r px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow hover:opacity-90 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 transition-all">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0v3.75a2.25 2.25 0 004.5 0V12a6.75 6.75 0 10-13.5 0v7.5" /></svg>
            Bayar Sekarang
        </button>
    </div>

    {{-- Cash Modal --}}
    <div id="cash-modal" class="fixed inset-0 z-[100] bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this)closeCashModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-scaleIn">
            <div class="text-center mb-5">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 mb-3">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
                </div>
                <h3 class="text-lg font-bold text-stone-900">Pembayaran Tunai</h3>
                <p class="text-sm text-stone-500 mt-1">Total: <strong class="text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5">Jumlah Uang</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-stone-400">Rp</span>
                        <input type="text" id="cash-amount" inputmode="numeric"
                            class="block w-full rounded-xl border-2 border-stone-200 pl-10 pr-11 py-3 text-lg font-bold text-stone-900 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all"
                            placeholder="0" oninput="formatCashInput(this)">
                        <button type="button" onclick="cashBackspace()" class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded-lg text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-all active:scale-90" title="Hapus angka terakhir">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9.75L14.25 12m0 0l2.25 2.25M14.25 12l2.25-2.25M14.25 12L12 14.25m-2.58 4.92l-6.375-6.375a1.125 1.125 0 010-1.59L9.42 4.83c.211-.211.498-.33.796-.33H19.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25h-9.284c-.298 0-.585-.119-.796-.33z"/></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-3">
                        <button type="button" onclick="setCashAmount(2000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp2.000</button>
                        <button type="button" onclick="setCashAmount(5000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp5.000</button>
                        <button type="button" onclick="setCashAmount(10000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp10.000</button>
                        <button type="button" onclick="setCashAmount(20000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp20.000</button>
                        <button type="button" onclick="setCashAmount(50000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp50.000</button>
                        <button type="button" onclick="setCashAmount(100000)" class="rounded-lg border border-stone-200 py-2 text-xs font-semibold text-stone-600 hover:bg-theme-primary/10 hover:border-theme-primary hover:text-theme-primary transition-all active:scale-95">Rp100.000</button>
                    </div>
                    <button type="button" onclick="setCashAmount({{ $order->total }})" class="w-full mt-2 rounded-lg border border-emerald-300 bg-emerald-50 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 hover:border-emerald-400 transition-all active:scale-[0.98]">Uang Pas — Rp {{ number_format($order->total, 0, ',', '.') }}</button>
                </div>
                <div id="cash-change" class="hidden rounded-xl bg-theme-primary/10 border border-theme-primary/20 px-4 py-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-600">Total</span>
                        <span class="font-semibold text-stone-900" id="cash-total-display">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-stone-600">Uang</span>
                        <span class="font-semibold text-stone-900" id="cash-paid-display">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-base font-bold mt-2 pt-2 border-t border-theme-primary/20">
                        <span class="text-emerald-700">Kembalian</span>
                        <span class="text-emerald-700" id="cash-change-display">Rp 0</span>
                    </div>
                </div>
                <div id="cash-insufficient" class="hidden rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700 text-center font-medium">
                    Uang tidak mencukupi!
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button onclick="closeCashModal()" class="flex-1 rounded-xl border-2 border-stone-200 px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-all">
                    Batal
                </button>
                <button id="cash-confirm-btn" disabled onclick="confirmCash()" class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    {{-- Success Section (shown after payment) --}}
    <div id="pay-success" class="hidden animate-scaleIn rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 p-6 sm:p-7 ring-1 ring-emerald-200/60 shadow-sm mb-6" style="animation-delay: .15s;">
        <div class="text-center mb-5">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 animate-pulseRing mb-3">
                <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-xl font-extrabold text-emerald-900">Pembayaran Berhasil!</h3>
            <p class="text-sm text-emerald-600/80 mt-1">Pesanan {{ $order->order_number }} telah lunas</p>
        </div>

        <div class="border-t border-emerald-200/60 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-stone-500">
                <span>Subtotal</span>
                <span class="font-medium text-stone-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-stone-500">
                <span>Diskon</span>
                <span class="font-medium text-stone-900">
                    {{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : 'Rp 0' }}
                </span>
            </div>
            <div class="flex justify-between text-stone-500">
                <span>Pembayaran</span>
                <span id="pay-success-method" class="font-medium text-stone-600">-</span>
            </div>
            <div class="flex justify-between text-base font-bold text-stone-900 pt-2 border-t border-emerald-200/60">
                <span>Total</span>
                <span class="text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-5 flex flex-col sm:flex-row gap-3">
            <button onclick="printReceipt('{{ route('orders.receipt.consumer', $order) }}')" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-emerald-600 active:scale-[0.98] transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                Cetak Struk Konsumen
            </button>
            <button onclick="printReceipt('{{ route('orders.receipt.kitchen', $order) }}')" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-stone-700 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-stone-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                Cetak Struk Dapur
            </button>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('orders.catalog') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-600 transition-colors">Pesan Lagi &rarr;</a>
        </div>
    </div>

    {{-- QRIS Manual Modal --}}
    <div id="qris-modal" class="fixed inset-0 z-[100] bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center p-4" onclick="if(event.target===this)closeQRISModal()">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 animate-scaleIn">
            <div class="text-center mb-5">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-theme-primary/10 mb-3">
                    <svg class="h-6 w-6 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-stone-900">QRIS Manual</h3>
                <p class="text-sm text-stone-500 mt-1">Scan QRIS berikut untuk melakukan pembayaran</p>
            </div>
            @if ($settings['qris_manual_image'] ?? null)
            <div class="flex justify-center mb-4">
                <img src="{{ asset('storage/' . $settings['qris_manual_image']) }}" alt="QRIS" class="w-56 h-56 rounded-xl border-2 border-stone-200 object-contain bg-white">
            </div>
            @else
            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700 text-center mb-4">
                Gambar QRIS belum diupload. Admin bisa upload di Pengaturan > Pembayaran.
            </div>
            @endif
            <div class="text-center mb-4">
                <p class="text-sm text-stone-600">Total Pembayaran</p>
                <p class="text-xl font-extrabold text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
            </div>
            <div class="flex gap-3">
                <button onclick="closeQRISModal()" class="flex-1 rounded-xl border-2 border-stone-200 px-4 py-2.5 text-sm font-semibold text-stone-600 hover:bg-stone-50 transition-all">
                Tutup
                </button>
                <button id="qris-paid-btn" onclick="confirmQRISPaid()" class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:bg-emerald-500 transition-all">
                Saya sudah bayar
                </button>
            </div>
        </div>
    </div>

    {{-- Payment Result (for Xendit pending) --}}
    <div id="pay-result" class="hidden animate-scaleIn rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 p-6 sm:p-7 ring-1 ring-emerald-200/60 shadow-sm" style="animation-delay: .15s;">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-emerald-900">Menunggu Pembayaran</h3>
                <p class="text-xs text-emerald-600/80">Setelah pembayaran dikonfirmasi, pesanan akan segera diproses</p>
            </div>
        </div>

        <div id="pay-result-timer" class="flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50/80 rounded-xl px-4 py-2.5 mb-4">
            <svg class="animate-float h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Halaman ini akan memantau status pembayaran secara otomatis.</span>
        </div>

        <div id="pay-link-area" class="hidden">
            <a id="pay-link" href="#" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-200/40 hover:from-emerald-500 hover:to-emerald-600 hover:shadow-emerald-300/50 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
                Buka Halaman Pembayaran
            </a>
            <p class="mt-2 text-xs text-emerald-600/70">Atau copy link berikut: <span id="pay-link-url" class="font-mono text-emerald-700 underline underline-offset-2 cursor-pointer"></span></p>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-medium text-emerald-700">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75" /></svg>
                Segera setelah pembayaran masuk
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-theme-primary/10 px-3 py-1 text-[11px] font-medium text-theme-primary">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Masa berlaku 24 jam
            </span>
        </div>

        <div id="pay-poll-area" class="mt-4 hidden">
            <div class="flex items-center gap-2 text-xs text-emerald-600">
                <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Memeriksa status pembayaran...
            </div>
        </div>
    </div>

    {{-- Xendit Iframe Modal --}}
    <div id="pay-iframe-modal" class="hidden fixed inset-0 z-[80] flex items-center justify-center p-4 bg-black/50" onclick="if (event.target===this) closeIframeModal()">
        <div class="relative w-full max-w-2xl h-[70vh] bg-white rounded-xl shadow-lg overflow-hidden anim-scaleIn">
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-stone-200/50">
                <span class="text-sm font-medium text-stone-600">Pembayaran Online</span>
                <button onclick="closeIframeModal()" class="flex h-6 w-6 items-center justify-center rounded text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-all">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="pay-iframe-loading" class="absolute inset-0 flex items-center justify-center bg-white">
                <svg class="animate-spin h-7 w-7 text-stone-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </div>
            <iframe id="pay-iframe" src="" class="w-full h-[calc(100%-45px)] mt-[45px]" frameborder="0" allow="payment" onload="document.getElementById('pay-iframe-loading').classList.add('hidden')"></iframe>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
history.pushState(null, '', location.href);
window.addEventListener('popstate', function(e) {
    history.pushState(null, '', location.href);
    alert('Selesaikan transaksi terlebih dahulu!');
});

let selectedMethod = null;
let pollInterval = null;
let payResultShown = false;

document.querySelectorAll('.pay-option').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pay-option').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        selectedMethod = this.dataset.method;
        document.getElementById('pay-btn').disabled = false;
        document.getElementById('pay-error').classList.add('hidden');
    });
});

document.getElementById('pay-btn').addEventListener('click', function() {
    if (!selectedMethod) return;
    if (selectedMethod === 'cash') openCashModal();
    else if (selectedMethod === 'qris_manual') openQRISManual();
    else processPayment();
});

function setCashAmount(val) {
    var el = document.getElementById('cash-amount');
    el.value = val.toLocaleString('id-ID');
    calcChange();
}

function cashBackspace() {
    var el = document.getElementById('cash-amount');
    var raw = el.value.replace(/[^0-9]/g, '');
    el.value = raw.length > 1 ? parseInt(raw.slice(0, -1)).toLocaleString('id-ID') : '';
    calcChange();
}

function formatCashInput(el) {
    var raw = el.value.replace(/[^0-9]/g, '');
    el.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
    calcChange();
}

function openCashModal() {
    document.getElementById('cash-modal').classList.remove('hidden');
    document.getElementById('cash-modal').classList.add('flex');
    document.getElementById('cash-amount').value = '';
    document.getElementById('cash-change').classList.add('hidden');
    document.getElementById('cash-insufficient').classList.add('hidden');
    document.getElementById('cash-confirm-btn').disabled = true;
    document.getElementById('cash-amount').focus();
}

function closeCashModal() {
    document.getElementById('cash-modal').classList.add('hidden');
    document.getElementById('cash-modal').classList.remove('flex');
}

function openQRISManual() {
    document.getElementById('qris-modal').classList.remove('hidden');
    document.getElementById('qris-modal').classList.add('flex');
}

function closeQRISModal() {
    document.getElementById('qris-modal').classList.add('hidden');
    document.getElementById('qris-modal').classList.remove('flex');
}

async function confirmQRISPaid() {
    document.getElementById('qris-paid-btn').disabled = true;
    document.getElementById('qris-paid-btn').innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
    selectedMethod = 'qris_manual';
    await processPayment();
}

function calcChange() {
    var total = {{ $order->total }};
    var raw = document.getElementById('cash-amount').value.replace(/[^0-9]/g, '');
    var paid = parseInt(raw) || 0;
    var changeEl = document.getElementById('cash-change');
    var insufficientEl = document.getElementById('cash-insufficient');
    var confirmBtn = document.getElementById('cash-confirm-btn');

    if (paid > 0) {
        changeEl.classList.remove('hidden');
        document.getElementById('cash-total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('cash-paid-display').textContent = 'Rp ' + paid.toLocaleString('id-ID');
        if (paid >= total) {
            document.getElementById('cash-change-display').textContent = 'Rp ' + (paid - total).toLocaleString('id-ID');
            insufficientEl.classList.add('hidden');
            confirmBtn.disabled = false;
        } else {
            document.getElementById('cash-change-display').textContent = 'Rp 0';
            insufficientEl.classList.remove('hidden');
            confirmBtn.disabled = true;
        }
    } else {
        changeEl.classList.add('hidden');
        insufficientEl.classList.add('hidden');
        confirmBtn.disabled = true;
    }
}

async function confirmCash() {
    document.getElementById('cash-confirm-btn').disabled = true;
    document.getElementById('cash-confirm-btn').innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';
    closeCashModal();
    await processPayment();
}

async function processPayment() {
    var btn = document.getElementById('pay-btn');
    btn.disabled = true;
    var origText = btn.innerHTML;
    btn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';

    try {
        const res = await fetch('{{ route("orders.invoice", $order) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ payment_method: selectedMethod }),
        });
        const data = await res.json();
        if (data.success && data.paid_directly) {
            showSuccess(selectedMethod);
        } else if (data.is_direct) {
            closeQRISModal();
            showSuccess(selectedMethod);
        } else if (data.invoice_url) {
            openIframeModal(data.invoice_url);
            startPolling(data.xendit_id);
        } else if (data.error) {
            document.getElementById('pay-error-text').textContent = data.error;
            document.getElementById('pay-error').classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('pay-error-text').textContent = 'Gagal memproses pembayaran. Silakan coba lagi.';
        document.getElementById('pay-error').classList.remove('hidden');
    }
    btn.disabled = false;
    btn.innerHTML = origText;
}

function openIframeModal(url) {
    document.getElementById('pay-iframe').src = url;
    document.getElementById('pay-iframe-modal').classList.remove('hidden');
    document.getElementById('pay-iframe-modal').classList.add('flex');
}

function closeIframeModal() {
    document.getElementById('pay-iframe-modal').classList.add('hidden');
    document.getElementById('pay-iframe-modal').classList.remove('flex');
    document.getElementById('pay-iframe').src = '';
}

function showSuccess(paymentMethod) {
    closeIframeModal();
    document.getElementById('pay-section').classList.add('hidden');
    document.getElementById('pay-result')?.classList.add('hidden');
    document.getElementById('pay-success').classList.remove('hidden');
    document.getElementById('pay-success').scrollIntoView({ behavior: 'smooth', block: 'center' });
    const methodEl = document.getElementById('pay-success-method');
    if (methodEl && paymentMethod) {
        methodEl.textContent = paymentMethod.toUpperCase();
    }
}

function printReceipt(url) {
    window.open(url, 'print_receipt', 'width=400,height=600,scrollbars=yes');
}

function startPolling(xenditId) {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        try {
            const res = await fetch('/orders/public/status/' + xenditId);
            const data = await res.json();
            if (data.status === 'paid' || data.status === 'success') {
                clearInterval(pollInterval);
                showSuccess(data.payment_method);
            } else if (data.status === 'expired') {
                clearInterval(pollInterval);
            }
        } catch (e) {}
    }, 2000);
}
</script>
@endpush