@extends('layouts.print')
@section('title', 'LAPORAN ANALISA KEUANGAN')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
@php
    $totalRevenue = $sales->sum('total');
    $totalSalesItems = $sales->sum(fn($s) => $s->items->sum('quantity'));
    $hpp = $sales->sum(function($s) {
        return $s->items->sum(function($item) {
            return ($item->product ? $item->product->cost_price : 0) * $item->quantity;
        });
    });
    $totalExpenses = $expenses->sum('amount');
    $labaKotor = $totalRevenue - $hpp;
    $labaBersih = $labaKotor - $totalExpenses;
    $marginKotor = $totalRevenue > 0 ? ($labaKotor / $totalRevenue) * 100 : 0;
    $marginBersih = $totalRevenue > 0 ? ($labaBersih / $totalRevenue) * 100 : 0;
    $ratioBiaya = $totalRevenue > 0 ? ($totalExpenses / $totalRevenue) * 100 : 0;
@endphp

<h3 style="margin:0 0 8px;font-size:11pt;">LAPORAN LABA RUGI</h3>
<table style="border:none;">
    <tr style="border:none;"><td style="border:none;padding:3px 6px;font-weight:600;" colspan="2">PENDAPATAN</td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;padding-left:20px;">Total Penjualan</td><td style="border:none;text-align:right;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;padding-left:20px;">Total Item Terjual</td><td style="border:none;text-align:right;">{{ number_format($totalSalesItems, 0) }} item</td></tr>
    <tr style="border:none;"><td style="border-bottom:1px solid #000;padding:2px 6px;"></td><td style="border-bottom:1px solid #000;"></td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;font-weight:600;">Total Pendapatan</td><td style="border:none;text-align:right;font-weight:600;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td></tr>
</table>

<table style="border:none;margin-top:8px;">
    <tr style="border:none;"><td style="border:none;padding:3px 6px;font-weight:600;" colspan="2">HARGA POKOK PENJUALAN (HPP)</td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;padding-left:20px;">HPP Produk Terjual</td><td style="border:none;text-align:right;">Rp {{ number_format($hpp, 0, ',', '.') }}</td></tr>
    <tr style="border:none;"><td style="border-bottom:1px solid #000;padding:2px 6px;"></td><td style="border-bottom:1px solid #000;"></td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;font-weight:600;">Total HPP</td><td style="border:none;text-align:right;font-weight:600;">Rp {{ number_format($hpp, 0, ',', '.') }}</td></tr>
</table>

<table style="border:none;margin-top:8px;">
    <tr style="border:none;"><td style="border:none;padding:3px 6px;font-weight:600;border-top:2px solid #000;">LABA KOTOR</td><td style="border:none;text-align:right;font-weight:600;border-top:2px solid #000;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td></tr>
</table>

<table style="border:none;margin-top:8px;">
    <tr style="border:none;"><td style="border:none;padding:3px 6px;font-weight:600;" colspan="2">BIAYA OPERASIONAL</td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;padding-left:20px;">Total Pengeluaran</td><td style="border:none;text-align:right;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td></tr>
    <tr style="border:none;"><td style="border-bottom:1px solid #000;padding:2px 6px;"></td><td style="border-bottom:1px solid #000;"></td></tr>
    <tr style="border:none;"><td style="border:none;padding:2px 6px;font-weight:600;">Total Biaya</td><td style="border:none;text-align:right;font-weight:600;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td></tr>
</table>

<table style="border:none;margin-top:8px;">
    <tr style="border:none;"><td style="border:none;padding:4px 6px;font-weight:600;font-size:11pt;border-top:2px solid #000;">LABA BERSIH</td><td style="border:none;text-align:right;font-weight:600;font-size:11pt;border-top:2px solid #000;">Rp {{ number_format($labaBersih, 0, ',', '.') }}</td></tr>
</table>

<div class="page-break"></div>
<h3 style="margin:0 0 8px;font-size:11pt;">RASIO KEUANGAN</h3>
<table>
    <tr><th style="width:60%">Indikator</th><th class="right">Nilai</th></tr>
    <tr><td>Margin Laba Kotor</td><td class="right">{{ number_format($marginKotor, 1) }}%</td></tr>
    <tr><td>Margin Laba Bersih</td><td class="right">{{ number_format($marginBersih, 1) }}%</td></tr>
    <tr><td>Rasio Biaya terhadap Pendapatan</td><td class="right">{{ number_format($ratioBiaya, 1) }}%</td></tr>
</table>

@if($periods->count() > 0)
<div class="page-break"></div>
<h3 style="margin:0 0 8px;font-size:11pt;">TREN PERIODE</h3>
<table>
    <thead>
        <tr>
            <th>Periode</th>
            <th class="right">Pendapatan</th>
            <th class="right">HPP</th>
            <th class="right">Laba Kotor</th>
            <th class="right">Biaya</th>
            <th class="right">Laba Bersih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($periods as $p)
        <tr>
            <td>{{ $p['label'] }}</td>
            <td class="right">Rp {{ number_format($p['revenue'], 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($p['hpp'], 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($p['laba_kotor'], 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($p['expenses'], 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($p['laba_bersih'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL</td>
            <td class="right">Rp {{ number_format($periods->sum('revenue'), 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($periods->sum('hpp'), 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($periods->sum('laba_kotor'), 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($periods->sum('expenses'), 0, ',', '.') }}</td>
            <td class="right">Rp {{ number_format($periods->sum('laba_bersih'), 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
@endif
@endsection
