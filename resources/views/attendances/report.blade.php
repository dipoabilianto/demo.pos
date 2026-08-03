@extends('layouts.app')
@section('title', 'Laporan Absensi')
@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-xl font-bold text-stone-900 mb-6">Laporan Absensi</h1>

    @if ($attendances->isNotEmpty())
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200">
                        <th class="text-left px-4 py-3 font-semibold text-stone-600">Tanggal</th>
                        <th class="text-left px-4 py-3 font-semibold text-stone-600">Karyawan</th>
                        <th class="text-left px-4 py-3 font-semibold text-stone-600">Shift</th>
                        <th class="text-left px-4 py-3 font-semibold text-stone-600">Hadir</th>
                        <th class="text-left px-4 py-3 font-semibold text-stone-600">Pulang</th>
                        <th class="text-right px-4 py-3 font-semibold text-stone-600">Uang Awal</th>
                        <th class="text-right px-4 py-3 font-semibold text-stone-600">Uang Akhir</th>
                        <th class="text-right px-4 py-3 font-semibold text-stone-600">Selisih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($attendances as $a)
                    <tr class="hover:bg-stone-50/50">
                        <td class="px-4 py-3 text-stone-900 font-medium">{{ $a->date->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $a->user->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $a->shift->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $a->check_in_time ? \Carbon\Carbon::parse($a->check_in_time)->format('H:i') . ' WIB' : '-' }}</td>
                        <td class="px-4 py-3">{{ $a->check_out_time ? \Carbon\Carbon::parse($a->check_out_time)->format('H:i') . ' WIB' : '-' }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($a->opening_balance, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ $a->closing_balance !== null ? 'Rp ' . number_format($a->closing_balance, 0, ',', '.') : '-' }}</td>
                        <td class="px-4 py-3 text-right font-medium {{ $a->closing_balance !== null ? (($a->closing_balance - $a->opening_balance) >= 0 ? 'text-emerald-600' : 'text-red-600') : 'text-stone-400' }}">
                            {{ $a->closing_balance !== null ? 'Rp ' . number_format($a->closing_balance - $a->opening_balance, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-6">{{ $attendances->links() }}</div>
    @else
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 px-6 py-12 text-center">
        <p class="text-sm text-stone-400">Belum ada data absensi.</p>
    </div>
    @endif
</div>
@endsection
