@extends('layouts.print')
@section('title', 'LAPORAN STOK OPNAME')
@section('subtitle', $settings['store_name'] ?? '')

@section('content')
<table>
    <thead>
        <tr>
            <th class="center" style="width:30px">No</th>
            <th>Tanggal</th>
            <th>Bahan</th>
            <th class="right">Selisih</th>
            <th>Keterangan</th>
            <th>Oleh</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $t)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $t->rawMaterial?->name ?? '-' }}</td>
            <td class="right">{{ number_format($t->quantity, 0) }}</td>
            <td>{{ $t->note ?: '-' }}</td>
            <td>{{ $t->user?->name ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="center">Tidak ada data stok opname</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
