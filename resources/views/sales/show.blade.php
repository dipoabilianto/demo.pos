@extends('layouts.app')
@section('title', 'Detail Penjualan')
@section('subtitle', 'Invoice: ' . $sale->invoice_number)
@section('content')
<div class="flex items-center justify-between mb-6">
    <div></div>
    <div class="flex gap-3">
<button onclick="printReceipt('{{ route('sales.receipt.consumer', $sale) }}')" class="rounded-xl bg-theme-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-all duration-200">
                            Struk Konsumen
                        </button>
                        <button onclick="printReceipt('{{ route('sales.receipt.kitchen', $sale) }}')" class="rounded-xl bg-stone-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-stone-700 transition-all duration-200">
                            Struk Dapur
                        </button>
        <script>
function printReceipt(url) {
    window.open(url, 'print_receipt', 'width=400,height=600,scrollbars=yes');
}
</script>
    <a href="{{ route('payments.checkout', $sale) }}" class="rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all duration-200">
            Bayar via Xendit
        </a>
        <a href="{{ route('sales.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
            <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                </div>
                <h3 class="text-base font-semibold text-warm-900">Item Penjualan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-warm-100">
                    <thead>
                        <tr class="bg-warm-50/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-warm-500 uppercase">Produk</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-warm-500 uppercase">Harga</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-warm-500 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-warm-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-100">
                        @foreach ($sale->items as $item)
                            <tr class="hover:bg-warm-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-warm-900 font-medium">{{ $item->product_name }}</td>
                                <td class="px-4 py-3 text-sm text-warm-700 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-warm-700 text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-warm-900 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-warm-500 text-right">Subtotal</td>
                            <td class="px-4 py-3 text-sm font-medium text-warm-900 text-right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if ($sale->discount > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-warm-500 text-right">Diskon</td>
                            <td class="px-4 py-3 text-sm font-medium text-rose-600 text-right">-Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if ($sale->tax > 0)
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-warm-500 text-right">PPN</td>
                            <td class="px-4 py-3 text-sm font-medium text-warm-900 text-right">Rp {{ number_format($sale->tax, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="font-bold">
                            <td colspan="3" class="px-4 py-3 text-sm text-warm-900 text-right border-t border-warm-100">Total</td>
                            <td class="px-4 py-3 text-sm font-bold text-theme-primary text-right border-t border-warm-100">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
            <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                </div>
                <h3 class="text-base font-semibold text-warm-900">Informasi</h3>
            </div>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500 font-medium">Metode</dt>
                    <dd class="text-sm font-medium text-warm-900 capitalize">{{ $sale->payment_method }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500 font-medium">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sale->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-theme-primary/10 text-theme-primary border border-theme-primary/20' }}">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500 font-medium">Dibayar</dt>
                    <dd class="text-sm font-semibold text-warm-900">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</dd>
                </div>
                @if ($sale->change_amount > 0)
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500 font-medium">Kembali</dt>
                    <dd class="text-sm font-semibold text-emerald-600">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500 font-medium">Tanggal</dt>
                    <dd class="text-sm text-warm-700">{{ $sale->created_at->format('d M Y H:i') }}</dd>
                </div>
                @if ($sale->notes)
                <div class="pt-2 border-t border-warm-100">
                    <dt class="text-xs text-warm-500 font-medium mb-1">Catatan</dt>
                    <dd class="text-sm text-warm-700 bg-warm-50 rounded-lg p-3">{{ $sale->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if ($sale->transaction)
        <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
            <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25l.213-.427A1.377 1.377 0 0010.18 13h3.66a1.37 1.37 0 00.958.573m.959-.927l.252.504M6.75 4.5h10.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg>
                </div>
                <h3 class="text-base font-semibold text-warm-900">Transaksi Online</h3>
            </div>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500">Channel</dt>
                    <dd class="text-sm font-medium text-warm-900">{{ $sale->transaction->payment_channel }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-xs text-warm-500">Status</dt>
                    <dd class="text-sm font-medium">{{ ucfirst($sale->transaction->status) }}</dd>
                </div>
            </dl>
        </div>
        @endif
    </div>
</div>

@endsection
