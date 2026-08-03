@extends('layouts.app')
@section('title', 'Riwayat Transaksi Stok')
@section('subtitle', 'Riwayat masuk, keluar, dan opname bahan baku.')
@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-warm-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Keterangan..." class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white w-48">
            </div>
            <div>
                <label class="block text-xs font-medium text-warm-500 mb-1">Bahan Baku</label>
                <select name="raw_material_id" class="rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    @foreach ($materials as $m)
                        <option value="{{ $m->id }}" {{ request('raw_material_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-warm-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-warm-800 transition-all shadow-sm">Filter</button>
        </form>
    </div>
    <a href="{{ route('stock-opname.index') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 transition-all duration-200">
        Kembali
    </a>
</div>

<div class="rounded-2xl bg-white shadow-md shadow-warm-900/5 border border-warm-200/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-warm-100">
            <thead>
                <tr class="bg-warm-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Bahan Baku</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Tipe</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Keterangan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-warm-500 uppercase tracking-wider">Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-100">
                @forelse ($transactions as $t)
                    <tr class="hover:bg-warm-50 transition-colors">
                        <td class="px-6 py-4 text-sm text-warm-500 whitespace-nowrap">{{ $t->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-warm-900">{{ $t->rawMaterial?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $typeStyles = [
                                    'in' => 'bg-emerald-100 text-emerald-700',
                                    'out' => 'bg-rose-100 text-rose-700',
                                    'opname' => 'bg-theme-primary/20 text-theme-primary',
                                ];
                                $typeLabels = [
                                    'in' => 'Masuk',
                                    'out' => 'Keluar',
                                    'opname' => 'Opname',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-medium {{ $typeStyles[$t->type] ?? 'bg-warm-100 text-warm-700' }}">
                                {{ $typeLabels[$t->type] ?? $t->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold">
                            <span class="{{ $t->type === 'in' ? 'text-emerald-600' : ($t->type === 'out' ? 'text-rose-600' : 'text-theme-primary') }}">
                                {{ number_format($t->quantity, 0) }} {{ $t->rawMaterial?->unit ?? '' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-warm-500 max-w-[200px] truncate">{{ $t->note ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-warm-400">{{ $t->user?->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <svg class="h-12 w-12 text-warm-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-sm text-warm-400">Belum ada transaksi stok.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($transactions->hasPages())
        <div class="px-6 py-4 border-t border-warm-100 bg-warm-50/30">{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
