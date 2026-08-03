@extends('layouts.print')
@section('title', 'LAPORAN STOK')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
@php
    $tab = request('tab', 'current');
    $lowStockMaterials = $materials->filter(fn($m) => $m->isLowStock());
@endphp

@if($tab === 'current' || !request('tab'))
<h3 style="margin:0 0 8px;font-size:11pt;">STOK SAAT INI</h3>
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

@if($lowStockMaterials->count() > 0)
<div class="page-break"></div>
<h3 style="margin:0 0 8px;font-size:11pt;">PERINGATAN STOK MINIMUM</h3>
<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th class="right">Stok</th>
            <th class="right">Min. Stok</th>
            <th class="right">Selisih</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lowStockMaterials as $material)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $material->name }}</td>
            <td>{{ $material->unit }}</td>
            <td class="right">{{ number_format($material->current_stock, 0) }}</td>
            <td class="right">{{ number_format($material->min_stock, 0) }}</td>
            <td class="right">{{ number_format($material->current_stock - $material->min_stock, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endif

@if($tab === 'mutasi' && request('material_id'))
@php
    $material = $materials->firstWhere('id', (int)request('material_id'));
    $transactions = $transactions ?? collect();
@endphp
<div class="page-break"></div>
<h3 style="margin:0 0 4px;font-size:11pt;">KARTU STOK</h3>
<div style="font-size:9pt;margin-bottom:8px;">
    Bahan: <strong>{{ $material->name ?? '' }}</strong> | Satuan: {{ $material->unit ?? '' }}
</div>
<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Tanggal</th>
            <th>Tipe</th>
            <th class="right">Qty</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $t)
        @php
            $qty = $t->type === 'out' ? -$t->quantity : $t->quantity;
        @endphp
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ ucfirst($t->type) }}</td>
            <td class="right">{{ $qty > 0 ? '+'.number_format($qty,0) : number_format($qty,0) }}</td>
            <td>{{ $t->note ?: '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="center">Belum ada transaksi untuk bahan ini</td></tr>
        @endforelse
    </tbody>
</table>
@endif
@endsection
