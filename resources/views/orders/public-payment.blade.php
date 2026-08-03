@extends('layouts.public')
@section('title', 'Pembayaran - ' . substr($order->order_number ?? '', -4))
@php
    $catalogUrl = $order->branch ? route('orders.public-catalog', $order->branch) : route('orders.public-catalog.default');
@endphp
@section('content')
<style>
    @keyframes fadeUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
    @keyframes scaleIn{from{opacity:0;transform:scale(.92)}to{opacity:1;transform:scale(1)}}
    @keyframes checkmark{0%{stroke-dashoffset:100;opacity:0}60%{opacity:1}100%{stroke-dashoffset:0;opacity:1}}
    @keyframes pulseRing{0%{box-shadow:0 0 0 0 rgba(5,150,105,.3)}70%{box-shadow:0 0 0 20px rgba(5,150,105,0)}100%{box-shadow:0 0 0 0 rgba(5,150,105,0)}}
    @keyframes spin{to{transform:rotate(360deg)}}
    @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
    .anim-fadeUp{animation:fadeUp .5s ease-out both}
    .anim-scaleIn{animation:scaleIn .4s cubic-bezier(.22,1,.36,1) both}
    .animate-pulseRing{animation:pulseRing 2s infinite}
    .animate-checkmark{stroke-dasharray:100;animation:checkmark .6s cubic-bezier(.22,1,.36,1) forwards .2s}
    .animate-float{animation:float 3s ease-in-out infinite}
    .pay-option{transition:all .2s ease;cursor:pointer}
    .pay-option.selected{border-color:#d97706;background:linear-gradient(135deg,#fffbeb,#fef3c7);box-shadow:0 4px 14px rgba(217,119,6,.15)}
    .pay-option:not(.selected):hover{border-color:#d97706;background:#fefce8;transform:translateY(-1px)}
</style>

<div class="min-h-[80vh] flex items-start justify-center py-6 sm:py-10 px-4">
    <div class="w-full max-w-lg bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl anim-fadeUp">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200/60">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10">
                    <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Pembayaran</h3>
                    <p class="text-[11px] text-stone-400 font-mono">{{ substr($order->order_number, -4) }}</p>
                    <p class="flex items-center gap-1 text-[10px] text-amber-700 font-medium">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $order->branch->name }}
                    </p>
                </div>
            </div>
            <a href="{{ $catalogUrl }}" class="flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>

        @if ($paid ?? false)
        <div class="p-8 text-center">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 animate-pulseRing mb-4">
                <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path class="animate-checkmark" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="text-lg font-bold text-emerald-800 mb-1">Pembayaran Berhasil!</h2>
            <p class="text-sm text-stone-500">Pesanan {{ substr($order->order_number, -4) }} telah lunas.</p>
            <a href="{{ $catalogUrl }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-theme-gradient-r px-6 py-3 text-sm font-bold text-white shadow-lg hover:opacity-90 transition-all">Pesan Lagi</a>
        </div>
        @else

        <div class="px-5 py-4 space-y-4">

            {{-- Order Summary --}}
            <div>
                <label class="text-xs font-medium text-stone-500 mb-2 block">Pesanan Anda</label>
                <div class="space-y-2">
                    @foreach ($order->items as $item)
                    <div class="flex items-center justify-between py-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-sm text-stone-700 truncate">{{ $item->product_name }}</span>
                            <span class="shrink-0 text-[11px] text-stone-400 bg-stone-100 rounded-full px-2 py-0.5 font-medium">&times;{{ $item->quantity }}</span>
                        </div>
                        <span class="shrink-0 text-sm font-semibold text-stone-800 ml-3">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-stone-100 mt-3 pt-3 space-y-1.5 text-sm">
                    <div class="flex justify-between text-stone-500">
                        <span>Subtotal</span>
                        <span class="font-medium text-stone-800">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount > 0)
                    <div class="flex justify-between text-stone-500">
                        <span>Diskon</span>
                        <span class="font-medium text-emerald-600">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if ($order->voucher)
                    <div class="flex justify-between text-[11px] text-stone-400">
                        <span>Voucher</span>
                        <span class="font-mono font-medium text-stone-500">{{ $order->voucher->code }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-stone-100">
                        <span class="text-stone-900">Total</span>
                        <span class="text-theme-primary">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Customer --}}
            <div class="flex items-center gap-2 text-sm text-stone-500 bg-stone-50/70 rounded-xl px-4 py-3">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <span class="font-medium text-stone-700">{{ $order->customer_name }}</span>
                @if ($order->customer_phone)
                <span class="text-stone-400">&middot;</span>
                <span>{{ $order->customer_phone }}</span>
                @endif
            </div>

            {{-- Error --}}
            <div id="pay-error" class="hidden rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700 ring-1 ring-rose-200 flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <span id="pay-error-text"></span>
            </div>

            {{-- Payment Methods --}}
            <div>
                <label class="text-xs font-medium text-stone-500 mb-2 block">Pilih Pembayaran</label>
                <div class="space-y-2">
                    @if ($cashMethod && $cashMethod->is_active)
                    <button type="button" class="pay-option w-full flex items-center gap-3 rounded-xl border-2 border-stone-200 px-4 py-3.5 text-left" data-method="cash">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-emerald-100 shrink-0">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-stone-800">Bayar Di Kasir</span>
                            <p class="text-xs text-stone-400">Bayar langsung di toko</p>
                        </div>
                        <svg class="h-5 w-5 text-stone-300 pay-check shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    @endif
                    @if ($transferMethod && $transferMethod->is_active)
                    <button type="button" class="pay-option w-full flex items-center gap-3 rounded-xl border-2 border-stone-200 px-4 py-3.5 text-left" data-method="transfer">
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-sky-100 shrink-0">
                            <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        </div>
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-stone-800">Transfer Bank</span>
                            <p class="text-xs text-stone-400">BCA, Mandiri, BRI, BNI — Virtual Account</p>
                        </div>
                        <svg class="h-5 w-5 text-stone-300 pay-check shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    @endif

                    @forelse ($paymentMethods as $method)
                    <button type="button" class="pay-option w-full flex items-center gap-3 rounded-xl border-2 border-stone-200 px-4 py-3.5 text-left" data-method="{{ $method->code }}">
                        @if (file_exists(public_path('images/payment/' . $method->code . '.svg')))
                        <img src="{{ asset('images/payment/' . $method->code . '.svg') }}" alt="{{ $method->name }}" class="h-8 w-8 object-contain shrink-0">
                        @else
                        <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-stone-100 shrink-0">
                            <svg class="h-5 w-5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        </div>
                        @endif
                        <div class="flex-1">
                            <span class="text-sm font-semibold text-stone-800">{{ $method->name }}</span>
                            <p class="text-xs text-stone-400">{{ $method->description }}</p>
                        </div>
                        <svg class="h-5 w-5 text-stone-300 pay-check shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    @empty
                    @if (!($transferMethod && $transferMethod->is_active))
                    <p class="text-center text-sm text-stone-400 py-4">Tidak ada metode pembayaran tersedia.</p>
                    @endif
                    @endforelse
                </div>

                <div id="pay-cash-info" class="hidden mt-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm flex items-start gap-2">
                    <svg class="h-5 w-5 shrink-0 mt-0.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <div>
                        <p class="font-medium text-amber-800">Bayar Di Kasir</p>
                        <p class="text-amber-700 mt-0.5 leading-relaxed">Silakan datang ke kasir toko untuk melakukan pembayaran. Pesanan akan diproses setelah pembayaran dikonfirmasi oleh kasir.</p>
                    </div>
                </div>

                <button id="pay-btn" disabled class="w-full mt-3 inline-flex items-center justify-center gap-2 rounded-xl bg-theme-gradient-r px-5 py-3.5 text-sm font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <span id="pay-btn-label">Bayar Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                </button>

                <div class="mt-3 flex items-center justify-center gap-4 text-[10px] text-stone-400">
                    <span class="flex items-center gap-1"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg> Data aman &amp; terenkripsi</span>
                    <span class="flex items-center gap-1"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Konfirmasi otomatis</span>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ $catalogUrl }}" class="text-xs font-medium text-rose-500 hover:text-rose-700 hover:underline transition-colors">Kembali ke Menu</a>
                </div>
            </div>
        </div>

        {{-- Payment Result --}}
        @endif
    </div>

    <div id="pay-result" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="w-full max-w-sm bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl anim-scaleIn p-6 text-center">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 animate-pulseRing mb-4">
                <svg class="h-7 w-7 text-emerald-600 animate-float" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-stone-800">Menunggu Pembayaran</h3>
            <p class="text-xs text-stone-400 mt-1">Silakan selesaikan pembayaran di jendela popup</p>
            <div id="pay-poll-area" class="hidden mt-5">
                <div class="flex items-center justify-center gap-2 text-xs text-stone-400">
                    <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Memeriksa status pembayaran...
                </div>
            </div>
            <div id="pay-expired" class="hidden mt-3 text-xs text-rose-500">Waktu pembayaran habis. Silakan pesan ulang.</div>
        </div>
    </div>

    <div id="pay-direct-success" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl anim-scaleIn overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/20 mb-2">
                    <svg class="h-6 w-6 text-white animate-float" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white">Menunggu Pembayaran</h3>
                <p class="text-sm text-amber-100 mt-0.5">Pesanan {{ substr($order->order_number, -4) }}</p>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div id="pay-direct-message" class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm flex items-start gap-2">
                    <svg class="h-5 w-5 shrink-0 mt-0.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    <div>
                        <p id="pay-direct-info-text" class="text-amber-800 leading-relaxed"></p>
                    </div>
                </div>
                <div class="bg-stone-50 rounded-xl px-4 py-3 space-y-2 text-sm divide-y divide-stone-100">
                    @foreach ($order->items as $item)
                    <div class="flex items-center justify-between {{ $loop->first ? '' : 'pt-2' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0 text-xs font-medium text-stone-400 w-5">{{ $item->quantity }}x</span>
                            <span class="truncate text-stone-700">{{ $item->product_name }}</span>
                        </div>
                        <span class="shrink-0 font-medium text-stone-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between text-stone-500">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount > 0)
                    <div class="flex justify-between text-stone-500">
                        <span>Diskon</span>
                        <span class="text-rose-500">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-stone-200 pt-2 text-base font-bold">
                        <span>Total Pesanan</span>
                        <span class="text-amber-700">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-xs text-stone-400 text-center pt-1">Pesanan sedang menunggu pembayaran. Setelah dibayar, kasir akan memproses pesanan Anda.</p>
            </div>
            <div class="px-6 pb-5">
                <a href="{{ $catalogUrl }}" class="block w-full text-center rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-90 transition-all">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>

    <div id="pay-success" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl anim-scaleIn overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/20 mb-2">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-white">Pembayaran Berhasil!</h3>
                <p class="text-sm text-emerald-100 mt-0.5">Pesanan {{ substr($order->order_number, -4) }}</p>
            </div>
            <div class="px-6 py-4 space-y-3">
                <div class="bg-stone-50 rounded-xl px-4 py-3 space-y-2 text-sm divide-y divide-stone-100">
                    @foreach ($order->items as $item)
                    <div class="flex items-center justify-between {{ $loop->first ? '' : 'pt-2' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0 text-xs font-medium text-stone-400 w-5">{{ $item->quantity }}x</span>
                            <span class="truncate text-stone-700">{{ $item->product_name }}</span>
                        </div>
                        <span class="shrink-0 font-medium text-stone-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between text-stone-500">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($order->discount > 0)
                    <div class="flex justify-between text-stone-500">
                        <span>Diskon</span>
                        <span class="text-rose-500">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-stone-500">
                        <span>Pembayaran</span>
                        <span id="pay-success-method" class="font-medium text-stone-600">-</span>
                    </div>
                    <div class="flex justify-between border-t border-stone-200 pt-2 text-base font-bold">
                        <span>Total Dibayar</span>
                        <span class="text-emerald-700">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-xs text-stone-400 text-center pt-1">Pesanan Anda akan segera diproses. Terima kasih!</p>
            </div>
            <div class="px-6 pb-5 space-y-2">
                <button onclick="printReceipt()" class="w-full text-center rounded-xl bg-stone-800 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-stone-700 transition-all flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z"/></svg>
                    Cetak Bukti Pembayaran
                </button>
                <a href="{{ $catalogUrl }}" class="block w-full text-center rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-90 transition-all">
                    Tutup
                </a>
            </div>
        </div>
    </div>
    <div id="pay-error-popup" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl anim-scaleIn overflow-hidden">
            <div class="bg-gradient-to-r from-rose-500 to-rose-600 px-6 py-5 text-center">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-white/20 mb-2">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <h3 id="pay-error-popup-title" class="text-lg font-bold text-white">Pembayaran Gagal</h3>
                <p class="text-sm text-rose-100 mt-0.5">Pesanan {{ substr($order->order_number, -4) }}</p>
            </div>
            <div class="px-6 py-4">
                <p id="pay-error-popup-text" class="text-sm text-stone-600 leading-relaxed"></p>
            </div>
            <div class="px-6 pb-5">
                <a href="{{ $catalogUrl }}" class="block w-full text-center rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-90 transition-all">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>


</div>

<div class="text-center py-4 text-xs text-stone-400 border-t border-stone-100 bg-white/50 max-w-lg mx-auto rounded-b-2xl">
    <a href="{{ $catalogUrl }}" class="hover:text-stone-600 transition-colors">&larr; Kembali ke Menu</a>
</div>
@endsection

@push('scripts')
<script>document.addEventListener('alpine:init', function () {
    initPayment({
        invoiceUrl: '{{ route("orders.invoice", $order) }}',
        csrf: document.querySelector('meta[name="csrf-token"]').content,
        isPublic: true,
        backPrevention: false,
        onPayMethodChange(method) {
            const cashInfo = document.getElementById('pay-cash-info');
            const btnLabel = document.getElementById('pay-btn-label');
            if (method === 'cash' || method === 'transfer') {
                cashInfo.classList.toggle('hidden', method !== 'cash');
                btnLabel.textContent = 'Konfirmasi Pesanan';
            } else {
                cashInfo.classList.add('hidden');
                btnLabel.textContent = 'Bayar Rp{{ number_format($order->total, 0, ',', '.') }}';
            }
        },
        onShowPayLink() {
            document.getElementById('pay-result').classList.remove('hidden');
            document.getElementById('pay-poll-area').classList.remove('hidden');
        },
        onShowDirectSuccess(method) {
            document.getElementById('pay-result')?.classList.add('hidden');
            document.getElementById('pay-direct-success').classList.remove('hidden');
            var infoText = document.getElementById('pay-direct-info-text');
            if (infoText) {
                if (method === 'cash') {
                    infoText.innerHTML = 'Pilih metode <strong>Bayar Di Kasir</strong>. Silakan datang ke kasir toko untuk melakukan pembayaran. Pesanan akan diproses setelah pembayaran dikonfirmasi oleh kasir.';
                } else {
                    infoText.innerHTML = 'Pilih metode <strong>Transfer Bank</strong>. Silakan lakukan pembayaran ke rekening yang tersedia. Pesanan akan diproses setelah pembayaran dikonfirmasi oleh kasir.';
                }
            }
        },
        onShowSuccess(method) {
            document.getElementById('pay-result')?.classList.add('hidden');
            document.getElementById('pay-success').classList.remove('hidden');
            var el = document.getElementById('pay-success-method');
            if (el && method) el.textContent = method.toUpperCase();
        },
        onShowError(msg) {
            var title = 'Pembayaran Gagal';
            if (msg.includes('sudah dibayar')) title = 'Sudah Dibayar';
            else if (msg.includes('kadaluwarsa') || msg.includes('tidak dapat diproses')) title = 'Pesanan Kadaluwarsa';
            document.getElementById('pay-error-popup-title').textContent = title;
            document.getElementById('pay-error-popup-text').textContent = msg;
            document.getElementById('pay-error-popup').classList.remove('hidden');
        },
        onExpired() {
            document.getElementById('pay-expired')?.classList.remove('hidden');
        },
    });
    window.printReceipt = function() { window.open('{{ route("orders.public-receipt", $order) }}', 'print_receipt', 'width=400,height=600,scrollbars=yes'); };
});</script>
@endpush