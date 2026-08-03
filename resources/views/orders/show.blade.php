@extends('layouts.app')
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $settings = $settings::getSettings(); @endphp
@section('title', 'Pesanan ' . ($order->order_number ?? ''))
@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-bold text-stone-900">{{ $order->order_number }}</h1>
                <p class="text-sm text-stone-500">{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
                @php
                    $_ps = $order->payment_status;
                    $_badgePaid = in_array($_ps, ['success', 'paid']);
                    $_badgeFailed = in_array($_ps, ['failed', 'expired']);
                    $_badgeOverdue = $_ps === 'pending' && $order->created_at->diffInMinutes(now()) > 5;
                    $_badgeColor = $_badgePaid ? 'bg-emerald-100 text-emerald-800' : ($_badgeFailed ? 'bg-red-100 text-red-800' : ($_badgeOverdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'));
                    $_badgeText = $_badgePaid ? 'Lunas' : ($_badgeFailed ? ($_ps === 'expired' ? 'Kadaluwarsa' : 'Gagal') : ($_badgeOverdue ? 'Belum Dibayar (>5 mnt)' : 'Belum Dibayar'));
                @endphp
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $_badgeColor }}">
                    {{ $_badgeText }}
                </span>
            </div>
        </div>

        <div class="border-t border-stone-200 pt-4">
            <h3 class="text-sm font-semibold text-stone-900 mb-2">Data Pemesan</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div><dt class="text-stone-500">Nama</dt><dd class="text-stone-900">{{ $order->customer_name }}</dd></div>
                    @if ($order->customer_phone)<div><dt class="text-stone-500">Telepon</dt><dd class="text-stone-900">{{ $order->customer_phone }}</dd></div>@endif
                    @if ($order->customer_email)<div><dt class="text-stone-500">Email</dt><dd class="text-stone-900">{{ $order->customer_email }}</dd></div>@endif
                </dl>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200 mb-6">
        <h3 class="text-sm font-semibold text-stone-900 mb-4">Item Pesanan</h3>
        <div class="space-y-3">
            @foreach ($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-stone-600">{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="font-medium text-stone-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-stone-200 mt-4 pt-4 space-y-1 text-sm">
            <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-stone-600"><span>Diskon</span><span>{{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : 'Rp 0' }}</span></div>
                @php $vc = $order->voucher->code ?? $order->voucher_code ?? null; @endphp
                @if ($vc)
                    <div class="flex justify-between text-stone-400 text-xs"><span>Kode Voucher</span><span class="font-mono">{{ $vc }}</span></div>
                @endif
                <div class="flex justify-between text-base font-bold text-stone-900 pt-2 border-t border-stone-200"><span>Total</span><span>Rp {{ number_format($order->total, 0, ',', '.') }}</span></div>
        </div>
    </div>

    @if ($_badgeFailed)
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm flex items-start gap-2 mb-3">
            <svg class="h-5 w-5 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <p class="text-red-800">Pesanan ini telah <strong>{{ $_badgeText }}</strong>. Stok sudah dikembalikan.</p>
        </div>
    @elseif ($_badgeOverdue)
        <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm flex items-start gap-2 mb-3">
            <svg class="h-5 w-5 shrink-0 mt-0.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <p class="text-red-800">Pesanan ini belum dibayar lebih dari 5 menit dan akan segera kadaluwarsa.</p>
        </div>
    @endif

    @if (!in_array($order->payment_status, ['success', 'paid']))
        @if (!$_badgeFailed)
        <a href="{{ route('orders.payment', $order) }}" class="block w-full rounded-lg bg-theme-primary px-6 py-3 text-sm font-semibold text-white text-center hover:opacity-90">
            Lanjutkan Pembayaran
        </a>
        @endif
    @else
        <div class="flex gap-3">
            <button onclick="printReceipt('{{ route('orders.receipt.consumer', $order) }}')" class="flex-1 block rounded-lg bg-theme-primary px-6 py-3 text-sm font-semibold text-white text-center hover:opacity-90 cursor-pointer">
                <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 14.25l.75-.75c.418-.418.418-1.082 0-1.5l-.75-.75m4.5 0l-.75.75a1.056 1.056 0 000 1.5l.75.75M9 9.75l-2.25 2.25M15 9.75l2.25 2.25M9 14.25l-2.25 2.25M15 14.25l2.25 2.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75H7.5a3 3 0 00-3 3v10.5a3 3 0 003 3h9a3 3 0 003-3V6.75a3 3 0 00-3-3z" /></svg>
                Struk Konsumen
            </button>
            <button onclick="printReceipt('{{ route('orders.receipt.kitchen', $order) }}')" class="flex-1 block rounded-lg bg-stone-700 px-6 py-3 text-sm font-semibold text-white text-center hover:bg-stone-800 cursor-pointer">
                <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" /></svg>
                Struk Dapur
            </button>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function printReceipt(url) {
    window.open(url, 'print_receipt', 'width=400,height=600,scrollbars=yes');
}
</script>
@endpush