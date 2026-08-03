@extends('layouts.print')
@section('title', 'LAPORAN PENJUALAN')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
@php
    $totalSubtotal = $sales->sum('subtotal');
    $totalDiscount = $sales->sum('discount');
    $totalTax = $sales->sum('tax');
    $totalGrand = $sales->sum('total');
    $totalItems = $sales->sum(fn($s) => $s->items->sum('quantity'));
    $count = $sales->count();
    $byMethod = $sales->groupBy('payment_method')->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('total')]);
@endphp

<div class="summary-card">
    <div class="row"><span>Total Penjualan</span><span>Rp {{ number_format($totalGrand, 0, ',', '.') }}</span></div>
    <div class="row"><span>Total Item Terjual</span><span>{{ number_format($totalItems, 0) }}</span></div>
    <div class="row"><span>Total Diskon</span><span>Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span></div>
    <div class="row"><span>Total Pajak ({{ $settings['tax_name'] ?? 'PPN' }})</span><span>Rp {{ number_format($totalTax, 0, ',', '.') }}</span></div>
    <div class="row"><span>Rata-rata per Transaksi</span><span>Rp {{ $count ? number_format($totalGrand / $count, 0, ',', '.') : '0' }}</span></div>
    <div class="row"><span>Jumlah Transaksi</span><span>{{ $count }}</span></div>
</div>

@if($byMethod->count() > 1)
<div class="summary-card">
    <div class="row" style="font-weight:600;border-bottom:1px solid #000;margin-bottom:4px;padding-bottom:4px;"><span>Metode Pembayaran</span><span>Total</span></div>
    @foreach($byMethod as $method => $data)
    <div class="row"><span>{{ ucfirst($method) }} ({{ $data['count'] }} transaksi)</span><span>Rp {{ number_format($data['total'], 0, ',', '.') }}</span></div>
    @endforeach
</div>
@endif

<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Invoice</th>
            <th>Tanggal</th>
            <th class="right">Item</th>
            <th class="right">Subtotal</th>
            <th class="right">Diskon</th>
            <th class="right">{{ $settings['tax_name'] ?? 'Pajak' }}</th>
            <th class="right">Total</th>
            <th>Metode</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $sale->invoice_number }}</td>
            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
            <td class="right">{{ $sale->items->sum('quantity') }}</td>
            <td class="right">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
            <td class="right">{{ $sale->discount > 0 ? '-Rp '.number_format($sale->discount, 0, ',', '.') : '-' }}</td>
            <td class="right">{{ $sale->tax > 0 ? 'Rp '.number_format($sale->tax, 0, ',', '.') : '-' }}</td>
            <td class="right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
            <td>{{ ucfirst($sale->payment_method) }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="center">Tidak ada data penjualan</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="right">GRAND TOTAL</td>
            <td class="right">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($totalTax, 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($totalGrand, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endsection
