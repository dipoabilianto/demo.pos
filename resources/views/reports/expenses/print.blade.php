@extends('layouts.print')
@section('title', 'LAPORAN PENGELUARAN')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
@php
    $totalAmount = $expenses->sum('amount');
    $count = $expenses->count();
    $byCategory = $expenses->groupBy('category')->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('amount')]);
@endphp

<div class="summary-card">
    <div class="row"><span>Total Pengeluaran</span><span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span></div>
    <div class="row"><span>Jumlah Transaksi</span><span>{{ $count }}</span></div>
</div>

@if($byCategory->count() > 1)
<div class="summary-card">
    <div class="row" style="font-weight:600;border-bottom:1px solid #000;margin-bottom:4px;padding-bottom:4px;"><span>Kategori</span><span>Total</span></div>
    @foreach($byCategory as $cat => $data)
    <div class="row"><span>{{ $cat ?: 'Tanpa Kategori' }} ({{ $data['count'] }})</span><span>Rp {{ number_format($data['total'], 0, ',', '.') }}</span></div>
    @endforeach
    <div class="row total-row"><span>Total</span><span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span></div>
</div>
@endif

<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Tanggal</th>
            <th>Judul</th>
            <th>Kategori</th>
            <th class="right">Jumlah</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($expenses as $expense)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
            <td>{{ $expense->title }}</td>
            <td>{{ $expense->category ?: '-' }}</td>
            <td class="right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
            <td>{{ Str::limit($expense->description, 40) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="center">Tidak ada data pengeluaran</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="right">GRAND TOTAL</td>
            <td class="right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
@endsection
