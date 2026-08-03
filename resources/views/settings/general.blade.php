@extends('layouts.app')
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $settings = $settings::getSettings(); @endphp
@section('title', 'Pengaturan')
@section('subtitle', 'Kelola pengaturan toko dan aplikasi.')
@section('content')
@php
    $user = Auth::user();
    $allowedTabs = [];
    if ($user->hasPermission('settings.general')) $allowedTabs[] = 'general';
    if ($user->hasPermission('settings.notifications')) $allowedTabs[] = 'notifications';
    if ($user->hasPermission('settings.receipt')) $allowedTabs[] = 'receipt';
    if ($user->hasPermission('settings.tax')) $allowedTabs[] = 'tax';
    if ($user->hasPermission('settings.promotions')) $allowedTabs[] = 'promotions';
    if ($user->hasPermission('settings.appearance')) $allowedTabs[] = 'appearance';
    if ($user->hasPermission('settings.payment')) $allowedTabs[] = 'payment';
    if ($user->hasPermission('security.manage')) $allowedTabs[] = 'security';
    $tab = in_array(request('tab', 'general'), $allowedTabs) ? request('tab', 'general') : $allowedTabs[0];
@endphp
<div>
    <div class="mb-6 flex gap-1 rounded-xl bg-white p-1.5 shadow-sm border border-warm-200/50 w-fit">
        @if($user->hasPermission('settings.general'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'general']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'general' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72" /></svg>
                Toko
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.payment'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'payment']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'payment' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                Pembayaran
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.tax'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'tax']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'tax' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                Pajak
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.notifications'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'notifications']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'notifications' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                Notifikasi
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.receipt'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'receipt']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'receipt' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                Struk
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.promotions'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'promotions']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'promotions' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                Promosi
            </div>
        </a>
        @endif
        @if ($user->hasPermission('security.manage'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'security']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'security' ? 'bg-emerald-500 text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                Keamanan
            </div>
        </a>
        @endif
        @if($user->hasPermission('settings.appearance'))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'appearance']) }}" class="rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'appearance' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" /></svg>
                Tampilan
            </div>
        </a>
        @endif
    </div>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        @if($user->hasPermission('settings.general') && $tab === 'general')
        <div>
            <div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50">
                {{-- Bagian 1: Info Toko --}}
                <div class="p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-warm-900">Informasi Toko</h3>
                            <p class="text-xs text-warm-400">Data yang tampil di halaman publik toko online Anda.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Toko</label>
                            <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? 'Oribun Bakery') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Mata Uang</label>
                            <select name="currency" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                                <option value="IDR" {{ ($settings['currency'] ?? 'IDR') === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
                                <option value="USD" {{ ($settings['currency'] ?? 'IDR') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Alamat Toko</label>
                        <textarea name="store_address" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Nomor Telepon</label>
                            <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Email Toko</label>
                            <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? '') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Nomor WhatsApp</label>
                        <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp'] ?? '') }}" placeholder="6281234567890" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Deskripsi Toko</label>
                        <textarea name="store_description" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('store_description', $settings['store_description'] ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Bagian 2: Tampilan Web --}}
                <div class="border-t border-warm-100 p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-warm-900">Tampilan Web</h3>
                            <p class="text-xs text-warm-400">Pengaturan judul halaman dan ikon tab browser.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Judul Halaman Pesanan</label>
                        <input type="text" name="catalog_title" value="{{ old('catalog_title', $settings['catalog_title'] ?? 'Pesan Roti') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        <p class="text-xs text-warm-400 mt-1">Judul yang tampil di tab browser dan header saat membuka halaman pesanan.</p>
                    </div>

                    <div class="border-t border-warm-100 pt-4">
                        <label class="block text-sm font-medium text-warm-700 mb-2">Favicon (Ikon Tab Browser)</label>
                        <div class="flex items-center gap-4" id="favicon-wrapper">
                            <div class="h-10 w-10 rounded-lg border border-warm-200 flex items-center justify-center bg-white overflow-hidden shrink-0" id="favicon-preview">
                                @if (!empty($settings['favicon']))
                                    <img src="{{ asset('storage/' . $settings['favicon']) }}" class="h-full w-full object-contain">
                                @else
                                    <svg class="h-5 w-5 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                                @endif
                            </div>
                            <label class="cursor-pointer rounded-lg bg-warm-100 px-4 py-2 text-sm font-medium text-warm-700 hover:bg-warm-200 transition-colors">
                                Pilih Gambar
                                <input type="file" id="favicon-input" accept=".ico,.png,.jpg,.jpeg,.svg" class="hidden">
                            </label>
                        </div>
                        <p class="text-xs text-warm-400 mt-1">Ukuran ideal: 32×32px atau 64×64px. Format: PNG, SVG, ICO.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($user->hasPermission('settings.payment') && $tab === 'payment')
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Konfigurasi Pembayaran</h3>
                        <p class="text-xs text-warm-400">API key untuk integrasi Xendit Payment Gateway.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Xendit Secret Key</label>
                    <input type="password" name="xendit_secret_key" value="{{ old('xendit_secret_key', $settings['xendit_secret_key'] ?? '') }}" placeholder="Kosongkan jika pakai .env" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Xendit Public Key</label>
                    <input type="password" name="xendit_public_key" value="{{ old('xendit_public_key', $settings['xendit_public_key'] ?? '') }}" placeholder="Kosongkan jika pakai .env" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Xendit Webhook Secret</label>
                    <input type="password" name="xendit_webhook_secret" value="{{ old('xendit_webhook_secret', $settings['xendit_webhook_secret'] ?? '') }}" placeholder="Kosongkan jika pakai .env" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white font-mono">
                    <p class="text-xs text-warm-400 mt-1">Kosongkan jika ingin tetap menggunakan nilai dari file <code>.env</code>. Nilai dari form ini akan digunakan jika <code>.env</code> tidak diset.</p>
                </div>

                <div class="rounded-xl bg-theme-primary/10 border border-theme-primary/20 p-4 text-sm text-theme-primary flex items-start gap-3">
                    <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <span>
                        Prioritas: <code>.env</code> > Database (form ini).
                        <a href="{{ route('settings.payment-methods.index') }}" class="font-medium underline underline-offset-2 hover:text-theme-primary">Atur metode pembayaran aktif/nonaktif &rarr;</a>
                    </span>
                </div>

                <hr class="border-warm-200">

                <div>
                    <h4 class="text-sm font-semibold text-warm-900 mb-3">QRIS Manual (Kasir)</h4>
                    <p class="text-xs text-warm-400 mb-3">Upload gambar QRIS untuk pembayaran manual di kasir.</p>

                    <div id="qris-manual-preview" class="{{ $settings['qris_manual_image'] ?? null ? '' : 'hidden' }} mb-3">
                        <img src="{{ $settings['qris_manual_image'] ? asset('storage/' . $settings['qris_manual_image']) : '' }}" class="w-48 h-48 rounded-xl border border-warm-200 object-contain bg-white">
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" id="qris-upload-btn" class="rounded-xl bg-theme-primary px-4 py-2 text-sm font-semibold text-white hover:opacity-90 transition-all">
                            {{ $settings['qris_manual_image'] ?? null ? 'Ganti' : 'Upload' }} QRIS
                        </button>
                        @if($settings['qris_manual_image'] ?? null)
                        <button type="button" id="qris-remove-btn" class="rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-all">
                            Hapus
                        </button>
                        @endif
                    </div>
                    <input type="file" id="qris-file-input" accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">
                    <p class="text-xs text-warm-400 mt-2">Format: PNG/JPG/WebP. Maks 1MB.</p>
                </div>
            </div>
        @endif

        @if($user->hasPermission('settings.tax') && $tab === 'tax')
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Pengaturan Pajak</h3>
                        <p class="text-xs text-warm-400">Atur pajak untuk transaksi penjualan dan pesanan.</p>
                    </div>
                </div>

                <label class="flex items-center justify-between py-2">
                    <div>
                        <span class="text-sm font-medium text-warm-900">Aktifkan Pajak</span>
                        <p class="text-xs text-warm-400">Terapkan pajak di setiap transaksi</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="tax_enabled" value="0">
                        <input type="checkbox" name="tax_enabled" value="1" {{ old('tax_enabled', $settings['tax_enabled'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                    </label>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Nama Pajak</label>
                        <input type="text" name="tax_name" value="{{ old('tax_name', $settings['tax_name'] ?? 'PPN') }}" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        <p class="text-xs text-warm-400 mt-1">Misal: PPN, PPh, dll.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Persentase (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate'] ?? 11) }}" min="0" max="100" step="0.1" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        <p class="text-xs text-warm-400 mt-1">Persentase pajak (contoh: 11 = 11%)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Tipe Pajak</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-warm-200 cursor-pointer hover:border-theme-primary/50 transition-colors">
                            <input type="radio" name="tax_type" value="exclude" {{ old('tax_type', $settings['tax_type'] ?? 'exclude') === 'exclude' ? 'checked' : '' }} class="text-theme-primary focus:ring-theme-primary/20">
                            <div>
                                <span class="text-sm font-medium text-warm-900">Di luar harga (Exclude)</span>
                                <p class="text-xs text-warm-400">Pajak ditambahkan ke total belanja. Harga produk belum termasuk pajak.</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border border-warm-200 cursor-pointer hover:border-theme-primary/50 transition-colors">
                            <input type="radio" name="tax_type" value="include" {{ old('tax_type', $settings['tax_type'] ?? 'exclude') === 'include' ? 'checked' : '' }} class="text-theme-primary focus:ring-theme-primary/20">
                            <div>
                                <span class="text-sm font-medium text-warm-900">Sudah termasuk harga (Include)</span>
                                <p class="text-xs text-warm-400">Harga produk sudah termasuk pajak. Pajak ditampilkan sebagai rincian saja.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="rounded-xl bg-warm-50 border border-warm-200 p-4 text-sm text-warm-700">
                    <p class="font-medium flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        Informasi Pajak
                    </p>
                    <ul class="mt-2 space-y-1 text-xs text-warm-500 list-disc list-inside">
                        <li>Pajak akan otomatis dikalkulasi saat transaksi penjualan dan pesanan baru.</li>
                        <li>Rincian pajak akan tampil di struk konsumen.</li>
                        <li>Untuk tipe <strong>Include</strong>, total tetap sama — pajak ditampilkan sebagai rincian.</li>
                        <li>Untuk tipe <strong>Exclude</strong>, pajak akan menambah total akhir.</li>
                    </ul>
                </div>
            </div>
        @endif

        @if($user->hasPermission('settings.notifications') && $tab === 'notifications')
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Pengaturan Notifikasi</h3>
                        <p class="text-xs text-warm-400">Kelola pengiriman notifikasi untuk pesanan dan stok.</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Email Notifikasi</label>
                    <input type="email" name="notification_email" value="{{ old('notification_email', $settings['notification_email'] ?? '') }}" placeholder="admin@oribun.app" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">

                    <label class="block text-sm font-medium text-warm-700 mb-1.5">WhatsApp Notifikasi</label>
                    <input type="text" name="notification_whatsapp" value="{{ old('notification_whatsapp', $settings['notification_whatsapp'] ?? '') }}" placeholder="6281234567890" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>

                <div class="divide-y divide-warm-100">
                    <label class="flex items-center justify-between py-3">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Konfirmasi Pesanan</span>
                            <p class="text-xs text-warm-400">Kirim email saat pelanggan melakukan pemesanan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="order_confirmation_email" value="0">
                            <input type="checkbox" name="order_confirmation_email" value="1" {{ old('order_confirmation_email', $settings['order_confirmation_email'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>

                    <label class="flex items-center justify-between py-3">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Status Pesanan</span>
                            <p class="text-xs text-warm-400">Kirim notifikasi saat status pesanan berubah</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="order_status_email" value="0">
                            <input type="checkbox" name="order_status_email" value="1" {{ old('order_status_email', $settings['order_status_email'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>

                    <label class="flex items-center justify-between py-3">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Peringatan Stok</span>
                            <p class="text-xs text-warm-400">Notifikasi saat stok produk menipis</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="low_stock_notification" value="0">
                            <input type="checkbox" name="low_stock_notification" value="1" {{ old('low_stock_notification', $settings['low_stock_notification'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>
                </div>

                {{-- Notifikasi Suara --}}
                <div class="border-t border-warm-100 pt-4 mt-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-warm-900">Notifikasi Suara</h4>
                            <p class="text-xs text-warm-400">Suara saat ada pesanan baru masuk</p>
                        </div>
                    </div>

                    <label class="flex items-center justify-between py-3">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Suara Notifikasi</span>
                            <p class="text-xs text-warm-400">Putar suara setiap ada pesanan baru</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="notification_sound_enabled" value="0">
                            <input type="checkbox" name="notification_sound_enabled" value="1" {{ old('notification_sound_enabled', $settings['notification_sound_enabled'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>

                    <div class="pt-3 space-y-3" x-data="{
                        preset: {{ Js::from(old('notification_sound_preset', $settings['notification_sound_preset'] ?? 'nada1')) }},
                        soundFile: {{ !empty($settings['notification_sound_file']) ? 'true' : 'false' }},
                        soundFileName: {{ Js::from(!empty($settings['notification_sound_file']) ? basename($settings['notification_sound_file']) : '') }},
                        soundFileUrl: {{ Js::from(!empty($settings['notification_sound_file']) ? asset('storage/' . $settings['notification_sound_file']) : '') }},
                        uploading: false,
                        uploadError: '',
                        uploadSound(event) {
                            var file = event.target.files[0];
                            if (!file) return;
                            this.uploading = true;
                            this.uploadError = '';
                            var form = new FormData();
                            form.append('file', file);
                            form.append('_token', document.querySelector('meta[name=&quot;csrf-token&quot;]').content);
                            fetch('{{ route('settings.upload-notification-sound') }}', {
                                method: 'POST', body: form
                            }).then(function(r) { return r.json(); }).then(function(d) {
                                if (d.success) {
                                    this.soundFile = true;
                                    this.soundFileName = d.filename;
                                    this.soundFileUrl = d.path;
                                    this.preset = 'custom';
                                } else {
                                    this.uploadError = 'Gagal mengunggah file.';
                                }
                                this.uploading = false;
                            }.bind(this)).catch(function() {
                                this.uploading = false;
                                this.uploadError = 'Terjadi kesalahan.';
                            }.bind(this));
                        },
                        removeSound() {
                            this.soundFile = false;
                            this.soundFileName = '';
                            this.soundFileUrl = '';
                            if (this.preset === 'custom') this.preset = 'nada1';
                        }
                    }">
                        <input type="hidden" name="notif_sound_custom_url" :value="soundFileUrl">
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Nada Notifikasi</label>
                            <div class="flex gap-2">
                                <select name="notification_sound_preset" x-model="preset" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                                    <option value="nada1">Nada 1 — Ascending</option>
                                    <option value="nada2">Nada 2 — Descending</option>
                                    <option value="nada3">Nada 3 — Chime</option>
                                    <option value="nada4">Nada 4 — Triplet</option>
                                    <option value="custom" :hidden="!soundFile">Custom</option>
                                </select>
                                <button onclick="__playNotifSound(this.form.notification_sound_preset.value, this.form.notif_sound_custom_url.value)" type="button" class="flex items-center gap-1.5 rounded-xl bg-warm-100 px-3.5 py-2.5 text-sm font-medium text-warm-600 hover:bg-warm-200 transition-all shrink-0">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    Preview
                                </button>
                            </div>
                        </div>

                        <template x-if="preset === 'custom' && !soundFile">
                            <div>
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-dashed border-warm-300 px-4 py-3 text-sm text-warm-500 hover:border-theme-primary hover:text-theme-primary transition-colors" :class="uploading ? 'opacity-50 pointer-events-none' : ''">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    <span x-text="uploading ? 'Mengunggah...' : 'Pilih file MP3 / WAV / OGG (maks 2MB)'"></span>
                                    <input type="file" accept=".mp3,.wav,.ogg,audio/mpeg,audio/wav,audio/ogg" class="hidden" @change="uploadSound($event)">
                                </label>
                                <p x-show="uploadError" x-text="uploadError" class="mt-1 text-xs text-rose-600"></p>
                            </div>
                        </template>

                        <template x-if="preset === 'custom' && soundFile">
                            <div class="flex items-center gap-3 rounded-xl bg-warm-50 border border-warm-200 px-4 py-3">
                                <svg class="h-5 w-5 shrink-0 text-warm-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z"/></svg>
                                <span class="text-sm font-medium text-warm-700 flex-1 truncate" x-text="soundFileName"></span>
                                <div class="flex gap-1">
                                    <button onclick="__playNotifSound('custom', this.closest('[x-data]').querySelector('[name=notif_sound_custom_url]').value)" type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10 text-theme-primary hover:bg-theme-primary/20 transition-all" title="Preview">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </button>
                                    <button @click.prevent="removeSound()" type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100 text-rose-500 hover:bg-rose-200 transition-all" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        @endif

        @if($user->hasPermission('settings.receipt') && $tab === 'receipt')
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Pengaturan Struk</h3>
                        <p class="text-xs text-warm-400">Atur logo, header, dan catatan yang tampil di struk.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Logo Struk</label>
                    @if (!empty($settings['receipt_logo']))
                        <div class="mb-3 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $settings['receipt_logo']) }}" class="h-16 w-auto object-contain rounded-lg border border-warm-200">
                            <label class="cursor-pointer rounded-lg bg-warm-100 px-3 py-1.5 text-xs font-medium text-warm-700 hover:bg-warm-200 transition-colors" id="logo-upload-label">
                                Ganti
                                <input type="file" id="logo-upload-input" accept="image/*" class="hidden">
                            </label>
                        </div>
                    @else
                        <label class="flex cursor-pointer items-center gap-2 rounded-xl border-2 border-dashed border-warm-300 px-4 py-3 text-sm text-warm-500 hover:border-theme-primary hover:text-theme-primary transition-colors" id="logo-upload-label">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                            Unggah Logo (PNG/JPG, maks 1MB)
                            <input type="file" id="logo-upload-input" accept="image/*" class="hidden">
                        </label>
                    @endif
                    <div id="logo-upload-status" class="hidden mt-2 text-xs font-medium"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Catatan Kaki (Struk Konsumen)</label>
                    <textarea name="receipt_footer_note" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('receipt_footer_note', $settings['receipt_footer_note'] ?? '') }}</textarea>
                    <p class="text-xs text-warm-400 mt-1">Pesan yang tampil di bagian bawah struk konsumen.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-1.5">Catatan untuk Dapur</label>
                    <textarea name="receipt_kitchen_note" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">{{ old('receipt_kitchen_note', $settings['receipt_kitchen_note'] ?? '') }}</textarea>
                    <p class="text-xs text-warm-400 mt-1">Instruksi khusus yang tampil di struk dapur (misal: suhu oven, topping).</p>
                </div>

                <div class="border-t border-warm-100 pt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Printer Thermal</label>
                            <select name="printer_model" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                                <option value="">-- Pilih Printer --</option>
                                <optgroup label="Epson">
                                    <option value="epson_tm_t82" {{ old('printer_model', $settings['printer_model'] ?? '') === 'epson_tm_t82' ? 'selected' : '' }}>Epson TM-T82</option>
                                    <option value="epson_tm_t88" {{ old('printer_model', $settings['printer_model'] ?? '') === 'epson_tm_t88' ? 'selected' : '' }}>Epson TM-T88</option>
                                    <option value="epson_tm_u220" {{ old('printer_model', $settings['printer_model'] ?? '') === 'epson_tm_u220' ? 'selected' : '' }}>Epson TM-U220</option>
                                    <option value="epson_tm_m30" {{ old('printer_model', $settings['printer_model'] ?? '') === 'epson_tm_m30' ? 'selected' : '' }}>Epson TM-m30</option>
                                </optgroup>
                                <optgroup label="Star">
                                    <option value="star_tsp100" {{ old('printer_model', $settings['printer_model'] ?? '') === 'star_tsp100' ? 'selected' : '' }}>Star TSP100</option>
                                    <option value="star_tsp700" {{ old('printer_model', $settings['printer_model'] ?? '') === 'star_tsp700' ? 'selected' : '' }}>Star TSP700</option>
                                    <option value="star_sm_s220" {{ old('printer_model', $settings['printer_model'] ?? '') === 'star_sm_s220' ? 'selected' : '' }}>Star SM-S220</option>
                                </optgroup>
                                <optgroup label="Xprinter">
                                    <option value="xprinter_xp_58" {{ old('printer_model', $settings['printer_model'] ?? '') === 'xprinter_xp_58' ? 'selected' : '' }}>Xprinter XP-58 (58mm)</option>
                                    <option value="xprinter_xp_80" {{ old('printer_model', $settings['printer_model'] ?? '') === 'xprinter_xp_80' ? 'selected' : '' }}>Xprinter XP-80 (80mm)</option>
                                </optgroup>
                                <optgroup label="Lainnya">
                                    <option value="posiflex_pp7000" {{ old('printer_model', $settings['printer_model'] ?? '') === 'posiflex_pp7000' ? 'selected' : '' }}>POSIFLEX PP-7000</option>
                                    <option value="bixolon_srp350" {{ old('printer_model', $settings['printer_model'] ?? '') === 'bixolon_srp350' ? 'selected' : '' }}>Bixolon SRP-350</option>
                                    <option value="citizen_ct_s310" {{ old('printer_model', $settings['printer_model'] ?? '') === 'citizen_ct_s310' ? 'selected' : '' }}>Citizen CT-S310</option>
                                    <option value="generic_58mm" {{ old('printer_model', $settings['printer_model'] ?? '') === 'generic_58mm' ? 'selected' : '' }}>Generic 58mm Thermal</option>
                                    <option value="generic_80mm" {{ old('printer_model', $settings['printer_model'] ?? '') === 'generic_80mm' ? 'selected' : '' }}>Generic 80mm Thermal</option>
                                </optgroup>
                            </select>
                            <p class="text-xs text-warm-400 mt-1">Pilih printer thermal yang terhubung ke kasir.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Ukuran Kertas</label>
                            <select name="printer_paper_size" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                                <option value="58" {{ old('printer_paper_size', $settings['printer_paper_size'] ?? '') === '58' ? 'selected' : '' }}>58 mm</option>
                                <option value="80" {{ old('printer_paper_size', $settings['printer_paper_size'] ?? '') === '80' ? 'selected' : '' }}>80 mm</option>
                            </select>
                            <p class="text-xs text-warm-400 mt-1">Sesuaikan dengan lebar kertas printer.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-warm-100 pt-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Jam Operasional</label>
                            <input type="text" name="store_hours" value="{{ old('store_hours', $settings['store_hours'] ?? '08:00 - 21:00') }}" placeholder="08:00 - 21:00" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                            <p class="text-xs text-warm-400 mt-1">Tampil di struk konsumen.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Instagram</label>
                            <input type="text" name="store_instagram" value="{{ old('store_instagram', $settings['store_instagram'] ?? '') }}" placeholder="@oribunbakery" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                            <p class="text-xs text-warm-400 mt-1">Tampil di struk konsumen.</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-warm-100 pt-4">
                    <label class="flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Tampilkan Harga di Struk Dapur</span>
                            <p class="text-xs text-warm-400">Jika tidak dicentang, struk dapur hanya menampilkan nama produk dan quantity</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="receipt_show_prices" value="0">
                            <input type="checkbox" name="receipt_show_prices" value="1" {{ old('receipt_show_prices', $settings['receipt_show_prices'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>
                </div>

                <div class="border-t border-warm-100 pt-4">
                    <label class="flex items-center justify-between">
                        <div>
                            <span class="text-sm font-medium text-warm-900">Tampilkan Tunai / Kembalian</span>
                            <p class="text-xs text-warm-400">Tampilkan jumlah tunai dibayar dan kembalian di struk konsumen.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="receipt_show_cash_change" value="0">
                            <input type="checkbox" name="receipt_show_cash_change" value="1" {{ old('receipt_show_cash_change', $settings['receipt_show_cash_change'] ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-theme-primary"></div>
                        </label>
                    </label>
                </div>
            </div>
        @endif

        @if($user->hasPermission('settings.promotions') && $tab === 'promotions')
        <div x-data="{ items: {!! htmlspecialchars(json_encode(old('promotions', $settings['promotions'] ?? [])), ENT_QUOTES, 'UTF-8') !!}, uploading: false, uploadPromoImage(event, item, i) { const file = event.target.files[0]; if (!file) return; this.uploading = true; const form = new FormData(); form.append('image', file); form.append('promo_id', item.id); form.append('_token', document.querySelector('meta[name=\'csrf-token\']').content); fetch('{{ route('settings.upload-promo-image') }}', { method: 'POST', body: form }).then(r => r.json()).then(d => { if (d.success) { item.image = d.path; } this.uploading = false; }).catch(() => { this.uploading = false; }); } }">
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-warm-100">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-warm-900">Slider Promosi</h3>
                            <p class="text-xs text-warm-400">
                                Kelola slide promosi yang tampil di halaman toko online.
                                @if ($currentBranch)
                                    Promo ini khusus untuk cabang <strong>{{ $currentBranch->name }}</strong> — cabang lain tidak terpengaruh.
                                @endif
                            </p>
                        </div>
                    </div>
                    <button @click="items.push({ id: Date.now(), title: '', description: '', link: '', active: true })" type="button" class="rounded-lg bg-theme-primary px-3 py-1.5 text-xs font-semibold text-white hover:opacity-90 transition-colors">
                        + Tambah Slide
                    </button>
                </div>

                <template x-for="(item, i) in items" :key="item.id">
                    <div class="rounded-xl border border-warm-200 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-warm-500 uppercase tracking-wider" x-text="'Slide #' + (i + 1)"></span>
                            <button @click="items.splice(i, 1)" type="button" class="flex h-7 w-7 items-center justify-center rounded-lg text-rose-400 hover:text-rose-600 hover:bg-rose-50 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <input type="hidden" :name="`promotions[${i}][id]`" x-model="item.id">
                        <input type="hidden" :name="`promotions[${i}][image]`" x-model="item.image">
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Gambar</label>
                            <div class="flex items-center gap-3">
                                <template x-if="item.image">
                                    <img :src="'{{ asset('storage/') }}/' + item.image" class="h-16 w-28 object-cover rounded-lg border border-warm-200">
                                </template>
                                <template x-if="!item.image">
                                    <div class="h-16 w-28 rounded-lg border-2 border-dashed border-warm-300 flex items-center justify-center text-warm-400">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                    </div>
                                </template>
                                <label class="cursor-pointer rounded-lg bg-warm-100 px-3 py-1.5 text-xs font-medium text-warm-700 hover:bg-warm-200 transition-colors">
                                    Pilih Gambar
                                    <input type="file" accept="image/*" class="hidden" @change="uploadPromoImage($event, item, i)">
                                </label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-warm-700 mb-1">Judul</label>
                                <input type="text" :name="`promotions[${i}][title]`" x-model="item.title" class="block w-full rounded-lg border-warm-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" placeholder="Promo Spesial!">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-warm-700 mb-1">Link (opsional)</label>
                                <input type="text" :name="`promotions[${i}][link]`" x-model="item.link" class="block w-full rounded-lg border-warm-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" placeholder="https://...">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-warm-700 mb-1">Deskripsi</label>
                            <textarea :name="`promotions[${i}][description]`" x-model="item.description" rows="2" class="block w-full rounded-lg border-warm-200 px-3 py-2 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" placeholder="Deskripsi promosi..."></textarea>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="hidden" :name="`promotions[${i}][active]`" value="0">
                            <input type="checkbox" :name="`promotions[${i}][active]`" value="1" x-model="item.active" class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20">
                            <span class="text-xs font-medium text-warm-700">Aktif</span>
                        </label>
                    </div>
                </template>

                <template x-if="items.length === 0">
                    <div class="text-center py-8">
                        <svg class="h-10 w-10 text-warm-300 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg>
                        <p class="text-sm text-warm-400">Belum ada promosi. Klik "Tambah Slide" untuk mulai.</p>
                    </div>
                </template>
            </div>
        </div>
        @endif

        @if ($user?->hasPermission('security.manage') && $tab === 'security')
        <div x-data="twoFactorSetup" x-init="load2fa()">
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Autentikasi Dua Langkah (2FA)</h3>
                        <p class="text-xs text-warm-400">Tingkatkan keamanan akun superadmin dengan Google Authenticator.</p>
                    </div>
                </div>

                <template x-if="loading">
                    <div class="text-center py-8 text-sm text-warm-400">Memuat...</div>
                </template>

                <template x-if="!loading && !enabled">
                    <div class="space-y-5">
                        <div class="rounded-xl bg-theme-primary/10 border border-theme-primary/20 p-4 text-sm text-theme-primary">
                            <p class="font-medium">Ikuti langkah berikut untuk mengaktifkan 2FA:</p>
                            <ol class="mt-2 ml-4 space-y-1 list-decimal">
                                <li>Instal <strong>Google Authenticator</strong> dari Play Store / App Store</li>
                                <li>Scan QR Code di bawah ini dengan aplikasi tersebut</li>
                                <li>Masukkan kode 6 digit yang muncul untuk verifikasi</li>
                            </ol>
                        </div>

                        <div class="flex justify-center" x-show="qrCode">
                            <div class="bg-white p-4 rounded-xl border border-warm-200">
                                <img :src="qrCode" alt="QR Code" class="w-48 h-48">
                            </div>
                        </div>

                        <div x-show="secret" class="text-center">
                            <p class="text-xs text-warm-400 mb-1">Atau masukkan kode ini secara manual:</p>
                            <code x-text="secret" class="text-sm font-mono bg-warm-100 px-3 py-1.5 rounded-lg select-all"></code>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Kode Verifikasi</label>
                            <input type="text" x-model="verifyCode" inputmode="numeric" maxlength="6" class="block w-40 text-center text-xl tracking-widest rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="------">
                            <p x-show="verifyError" x-text="verifyError" class="mt-1 text-sm text-rose-600"></p>
                        </div>

                        <div class="flex gap-3">
                            <button @click="enable2fa()" :disabled="!verifyCode || verifying" class="rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200" x-text="verifying ? 'Memverifikasi...' : 'Aktifkan 2FA'">
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="!loading && enabled">
                    <div class="space-y-5">
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800 flex items-center gap-3">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="font-medium">2FA sudah aktif. Setiap login sebagai superadmin akan memerlukan kode Authenticator.</span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-warm-700 mb-1.5">Masukkan kode untuk menonaktifkan</label>
                            <input type="text" x-model="disableCode" inputmode="numeric" maxlength="6" class="block w-40 text-center text-xl tracking-widest rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-rose-400 focus:ring-rose-400/20 text-sm bg-white" placeholder="------">
                            <p x-show="disableError" x-text="disableError" class="mt-1 text-sm text-rose-600"></p>
                        </div>

                        <button @click="disable2fa()" :disabled="!disableCode || disabling" class="rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:from-rose-600 hover:to-rose-700 transition-all duration-200" x-text="disabling ? 'Menonaktifkan...' : 'Nonaktifkan 2FA'">
                        </button>
                    </div>
                </template>
            </div>
        </div>
        @endif

        @if($user->hasPermission('settings.appearance') && $tab === 'appearance')
        {{-- Tab: Tampilan --}}
        <div x-data="themeEditor" x-init="initTheme({{ Js::from($settings) }})">
            <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-5">
                <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" /></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-warm-900">Tampilan Aplikasi</h3>
                        <p class="text-xs text-warm-400">Sesuaikan warna tema untuk tampilan private (dashboard) dan publik (toko online).</p>
                    </div>
                </div>

                {{-- Presets --}}
                <div>
                    <label class="block text-sm font-medium text-warm-700 mb-2">Preset Warna</label>
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="preset in presets" :key="preset.name">
                            <button @click="applyPreset(preset)" type="button"
                                class="flex flex-col items-center gap-1.5 rounded-xl border-2 px-2 py-2.5 text-xs font-medium transition-all duration-200"
                                :class="isActivePreset(preset) ? 'border-theme-primary bg-theme-primary/10 text-theme-primary ring-1 ring-theme-primary/20' : 'border-transparent bg-warm-50 text-warm-600 hover:bg-warm-100 hover:border-warm-200'">
                                <span class="flex -space-x-1">
                                    <span class="inline-block w-4 h-4 rounded-full border border-white/50" :style="{ backgroundColor: preset.primary }"></span>
                                    <span class="inline-block w-4 h-4 rounded-full border border-white/50" :style="{ backgroundColor: preset.sidebar }"></span>
                                    <span class="inline-block w-4 h-4 rounded-full border border-white/50" :style="{ backgroundColor: preset.accent }"></span>
                                </span>
                                <span x-text="preset.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Color Pickers --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Warna Utama</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="colors.primary" @input="applyColors()"
                                class="h-10 w-10 rounded-lg border border-warm-200 cursor-pointer p-0.5">
                            <input type="text" x-model="colors.primary" name="theme_primary" class="block w-full rounded-xl border-warm-200 px-3 py-2 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                        </div>
                        <p class="text-xs text-warm-400 mt-1">Tombol, badge, link aktif</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Warna Sidebar</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="colors.sidebar" @input="applyColors()"
                                class="h-10 w-10 rounded-lg border border-warm-200 cursor-pointer p-0.5">
                            <input type="text" x-model="colors.sidebar" name="theme_sidebar" class="block w-full rounded-xl border-warm-200 px-3 py-2 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                        </div>
                        <p class="text-xs text-warm-400 mt-1">Background sidebar kiri</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Teks Sidebar</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="colors.sidebarText" @input="applyColors()"
                                class="h-10 w-10 rounded-lg border border-warm-200 cursor-pointer p-0.5">
                            <input type="text" x-model="colors.sidebarText" name="theme_sidebar_text" class="block w-full rounded-xl border-warm-200 px-3 py-2 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                        </div>
                        <p class="text-xs text-warm-400 mt-1">Warna teks di sidebar</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-warm-700 mb-1.5">Warna Aksen</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="colors.accent" @input="applyColors()"
                                class="h-10 w-10 rounded-lg border border-warm-200 cursor-pointer p-0.5">
                            <input type="text" x-model="colors.accent" name="theme_accent" class="block w-full rounded-xl border-warm-200 px-3 py-2 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                        </div>
                        <p class="text-xs text-warm-400 mt-1">Hover, highlight</p>
                    </div>
                </div>

                {{-- Background Publik --}}
                <div class="border-t border-warm-100 pt-5">
                    <label class="block text-sm font-semibold text-warm-700 mb-3">Latar Halaman Publik</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-warm-600 mb-1">Dasar</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.bgBase" @input="applyColors()"
                                    class="h-9 w-9 rounded-lg border border-warm-200 cursor-pointer p-0.5 shrink-0">
                                <input type="text" x-model="colors.bgBase" name="bg_base" class="block w-full rounded-xl border-warm-200 px-2.5 py-1.5 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-warm-600 mb-1">Gradien</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.bgGradient" @input="applyColors()"
                                    class="h-9 w-9 rounded-lg border border-warm-200 cursor-pointer p-0.5 shrink-0">
                                <input type="text" x-model="colors.bgGradient" name="bg_gradient" class="block w-full rounded-xl border-warm-200 px-2.5 py-1.5 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-warm-600 mb-1">Blob</label>
                            <div class="flex items-center gap-2">
                                <input type="color" x-model="colors.bgBlob" @input="applyColors()"
                                    class="h-9 w-9 rounded-lg border border-warm-200 cursor-pointer p-0.5 shrink-0">
                                <input type="text" x-model="colors.bgBlob" name="bg_blob" class="block w-full rounded-xl border-warm-200 px-2.5 py-1.5 text-xs font-mono shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-warm-400 mt-2">Warna latar dan blob di halaman publik toko online Anda.</p>
                </div>

                {{-- Live Preview --}}
                <div class="rounded-xl overflow-hidden border border-warm-200">
                    <div class="bg-warm-50 px-4 py-2 border-b border-warm-200">
                        <span class="text-xs font-semibold text-warm-500 uppercase tracking-wider">Pratinjau Sidebar</span>
                    </div>
                    <div class="flex h-40">
                        <div class="w-1/3 flex flex-col items-center justify-center gap-2 text-xs font-medium" :style="{ backgroundColor: colors.sidebar, color: colors.sidebarText }">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" /></svg>
                            <span>Sidebar</span>
                        </div>
                        <div class="flex-1 flex flex-col items-center justify-center gap-2 bg-white">
                            <button class="rounded-lg px-4 py-1.5 text-xs font-bold text-white shadow-sm" :style="{ backgroundColor: colors.primary }">Tombol Utama</button>
                            <span class="text-xs" :style="{ color: colors.accent }">Teks Aksen</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array($tab, ['general', 'payment', 'notifications', 'receipt', 'promotions', 'appearance']))
        <div class="mt-6 flex justify-end">
            <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
                Simpan Pengaturan
            </button>
        </div>
        @endif
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('change', function(e) {
    if (e.target.id !== 'logo-upload-input') return;
    const file = e.target.files[0];
    if (!file) return;
    const status = document.getElementById('logo-upload-status');
    status.className = 'mt-2 text-xs font-medium text-theme-primary';
    status.textContent = 'Mengompres & mengunggah...';
    status.classList.remove('hidden');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.onload = function() {
        let w = img.width, h = img.height;
        const maxDim = 400;
        if (w > maxDim || h > maxDim) {
            const ratio = Math.min(maxDim / w, maxDim / h);
            w = Math.round(w * ratio);
            h = Math.round(h * ratio);
        }
        canvas.width = w;
        canvas.height = h;
        ctx.drawImage(img, 0, 0, w, h);
        canvas.toBlob(function(blob) {
            const form = new FormData();
            form.append('logo', blob, file.name.replace(/\.[^.]+$/, '') + '.webp');
            form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            status.textContent = 'Mengunggah...';
            fetch('{{ route('settings.receipt.upload-logo') }}', { method: 'POST', body: form })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        status.className = 'mt-2 text-xs font-medium text-emerald-600';
                        status.textContent = 'Logo berhasil diunggah!';
                        location.reload();
                    } else {
                        status.className = 'mt-2 text-xs font-medium text-rose-600';
                        status.textContent = d.error || 'Gagal mengunggah logo.';
                    }
                })
                .catch(function() {
                    status.className = 'mt-2 text-xs font-medium text-rose-600';
                    status.textContent = 'Gagal mengunggah logo.';
                });
        }, 'image/webp', 0.8);
    };
    img.onerror = function() {
        status.className = 'mt-2 text-xs font-medium text-rose-600';
        status.textContent = 'Gagal membaca gambar.';
    };
    img.src = URL.createObjectURL(file);
});

document.getElementById('qris-upload-btn')?.addEventListener('click', function() {
    document.getElementById('qris-file-input').click();
});

document.getElementById('qris-file-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const form = new FormData();
    form.append('qris_image', file);
    form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch('{{ route('settings.qris-upload') }}', { method: 'POST', body: form })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                location.reload();
            } else {
                alert(d.error || 'Gagal mengunggah QRIS.');
            }
        });
});

document.getElementById('qris-remove-btn')?.addEventListener('click', function() {
    if (!confirm('Hapus gambar QRIS?')) return;
    fetch('{{ route('settings.qris-remove') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); });
});

document.getElementById('favicon-input')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const form = new FormData();
    form.append('favicon', file);
    form.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    fetch('{{ route('settings.upload-favicon') }}', { method: 'POST', body: form })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('favicon-preview').innerHTML = '<img src="' + d.path + '" class="h-full w-full object-contain">';
            }
        });
});


</script>
@endpush
