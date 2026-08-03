@extends('layouts.app')
@section('title', 'Absensi')
@section('content')
<div class="max-w-3xl mx-auto" x-data="attendancePage" x-init="init()">
    {{-- Status & Check-out Card --}}
    <div x-show="status" class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 p-5 mb-6">
        <template x-if="status === 'checkin'">
            <div>
                <div class="flex items-center gap-3 text-emerald-600 mb-4">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">Sudah Absen Hari Ini</p>
                        <p class="text-xs text-emerald-600" x-text="'Shift: ' + (todayShift || '-')"></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Hadir</p>
                        <p class="text-base font-bold text-stone-800 mt-0.5" x-text="checkInTime + ' WIB'"></p>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Uang Awal</p>
                        <p class="text-base font-bold text-stone-800 mt-0.5" x-text="'Rp ' + Number(openingBalance).toLocaleString('id-ID')"></p>
                    </div>
                </div>
                <div class="border-t border-stone-100 pt-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Uang Akhir Shift</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-stone-400 font-medium">Rp</span>
                            <input type="number" x-model="form.closing_balance" min="0" inputmode="numeric" class="w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" placeholder="Masukkan jumlah uang akhir">
                        </div>
                    </div>
                    <div class="rounded-xl bg-stone-50 border border-stone-200 p-3 text-xs text-stone-500">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            <span x-text="locationText">Mendeteksi lokasi...</span>
                        </div>
                    </div>
                    <button @click="checkOut" :disabled="loading" class="w-full rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        <span x-show="!loading">📍 Absen Pulang</span>
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            Memproses...
                        </span>
                    </button>
                    <p x-show="error" x-text="error" class="text-sm text-red-600 text-center"></p>
                </div>
            </div>
        </template>

        <template x-if="status === 'checkout'">
            <div>
                <div class="flex items-center gap-3 text-emerald-600 mb-4">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">Absensi Selesai</p>
                        <p class="text-xs text-emerald-600" x-text="'Shift: ' + (todayShift || '-')"></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Hadir</p>
                        <p class="text-base font-bold text-stone-800 mt-0.5" x-text="checkInTime + ' WIB'"></p>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Pulang</p>
                        <p class="text-base font-bold text-stone-800 mt-0.5" x-text="checkOutTime + ' WIB'"></p>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Uang Awal</p>
                        <p class="text-base font-bold text-stone-800 mt-0.5" x-text="'Rp ' + Number(openingBalance).toLocaleString('id-ID')"></p>
                    </div>
                    <div class="rounded-xl bg-stone-50 px-4 py-3">
                        <p class="text-xs text-stone-400">Selisih Kas</p>
                        <p class="text-base font-bold mt-0.5" :class="diff >= 0 ? 'text-emerald-600' : 'text-red-600'" x-text="'Rp ' + Number(diff).toLocaleString('id-ID')"></p>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Riwayat --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-stone-900">Riwayat Absensi</h2>
    </div>

    @if ($attendances->isNotEmpty())
    <div class="space-y-3">
        @foreach ($attendances as $attendance)
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 px-5 py-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-stone-900">{{ $attendance->date->format('d M Y') }}</span>
                    <span class="text-xs bg-stone-100 text-stone-600 px-2 py-0.5 rounded-full">{{ $attendance->shift->name ?? '-' }}</span>
                </div>
                <span class="text-xs {{ $attendance->status === 'present' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded-full font-medium">
                    {{ $attendance->status === 'present' ? 'Hadir' : ($attendance->status === 'late' ? 'Terlambat' : 'Absen') }}
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-stone-400">Hadir</span>
                    <p class="text-sm font-medium text-stone-800">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') . ' WIB' : '-' }}</p>
                </div>
                <div>
                    <span class="text-stone-400">Pulang</span>
                    <p class="text-sm font-medium text-stone-800">{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') . ' WIB' : '-' }}</p>
                </div>
                <div>
                    <span class="text-stone-400">Uang Awal</span>
                    <p class="text-sm font-medium text-stone-800">Rp {{ number_format($attendance->opening_balance, 0, ',', '.') }}</p>
                </div>
                <div>
                    <span class="text-stone-400">Uang Akhir</span>
                    <p class="text-sm font-medium text-stone-800">{{ $attendance->closing_balance !== null ? 'Rp ' . number_format($attendance->closing_balance, 0, ',', '.') : '-' }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $attendances->links() }}</div>
    @else
    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 px-6 py-12 text-center">
        <svg class="h-10 w-10 mx-auto text-stone-300 mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
        <p class="text-sm text-stone-400">Belum ada riwayat absensi.</p>
    </div>
    @endif
</div>
@endsection


