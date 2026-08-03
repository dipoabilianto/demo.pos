@extends('layouts.print')
@section('title', 'LAPORAN BAHAN BAKU')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
@php
    $totalStock = $materials->sum('current_stock');
    $lowStockCount = $materials->filter(fn($m) => $m->isLowStock())->count();
@endphp

<div class="summary-card">
    <div class="row"><span>Total Bahan Baku</span><span>{{ $materials->count() }}</span></div>
    <div class="row"><span>Total Stok</span><span>{{ number_format($totalStock, 0) }}</span></div>
    <div class="row"><span>Bahan Stok Menipis</span><span>{{ $lowStockCount }}</span></div>
</div>

<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th class="right">Stok</th>
            <th class="right">Min. Stok</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $material)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $material->name }}</td>
            <td>{{ $material->unit }}</td>
            <td class="right">{{ number_format($material->current_stock, 0) }}</td>
            <td class="right">{{ number_format($material->min_stock, 0) }}</td>
            <td>{{ $material->isLowStock() ? 'STOK MENIPIS' : 'Tersedia' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="center">Tidak ada data bahan baku</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
