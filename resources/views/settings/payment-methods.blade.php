@extends('layouts.app')
@section('title', 'Metode Pembayaran')
@section('subtitle', 'Atur metode pembayaran yang tersedia untuk pelanggan.')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-warm-900">Metode Pembayaran</h2>
            <p class="text-sm text-warm-500 mt-0.5">Aktifkan atau nonaktifkan metode pembayaran yang tersedia untuk pelanggan</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @php
            $groups = $methods->groupBy('group');
        @endphp
        @foreach (['offline' => 'Offline (Bayar Langsung)', 'virtual_account' => 'Virtual Account', 'ewallet' => 'E-Wallet', 'bank_transfer' => 'Bank Transfer', 'retail' => 'Ritel', 'over_the_counter' => 'OTC', 'qris' => 'QRIS'] as $groupKey => $groupLabel)
            @if ($groups->has($groupKey))
                <div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
                    <div class="px-5 py-3 border-b border-warm-100 bg-warm-50/50">
                        <h3 class="text-sm font-semibold text-warm-700">{{ $groupLabel }}</h3>
                    </div>
                    <div class="divide-y divide-warm-100">
                        @foreach ($groups[$groupKey] as $method)
                            <form method="POST" action="{{ route('settings.payment-methods.toggle', $method) }}" class="flex items-center justify-between px-5 py-3.5 hover:bg-warm-50 transition-colors">
                                @csrf
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $method->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-warm-100 text-warm-400' }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium {{ $method->is_active ? 'text-warm-900' : 'text-warm-400' }} truncate">{{ $method->name }}</p>
                                        @if ($method->description)
                                            <p class="text-xs {{ $method->is_active ? 'text-warm-500' : 'text-warm-300' }} truncate">{{ $method->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-2">
                                    <input type="checkbox" onchange="this.form.submit()" {{ $method->is_active ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-10 h-5 bg-warm-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-theme-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-theme-primary"></div>
                                </label>
                            </form>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <div class="rounded-xl bg-theme-primary/10 border border-theme-primary/20 p-4 text-sm text-theme-primary flex items-start gap-3">
        <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
        <span>Metode yang dinonaktifkan tidak akan muncul di halaman pembayaran pelanggan. Pengaturan Xendit API ada di menu <a href="{{ route('settings.general') }}" class="font-medium underline underline-offset-2">Pengaturan &rarr; Pembayaran</a>.</span>
    </div>
</div>
@endsection
