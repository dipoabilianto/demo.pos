@extends('layouts.app')
@section('title', 'Edit Shift')
@section('content')
<div class="max-w-lg mx-auto">
    <a href="{{ route('settings.shifts.index') }}" class="inline-flex items-center gap-1.5 text-sm text-stone-500 hover:text-stone-700 mb-4 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        Kembali
    </a>
    <h1 class="text-xl font-bold text-stone-900 mb-6">Edit Shift: {{ $shift->name }}</h1>

    <form method="POST" action="{{ route('settings.shifts.update', $shift) }}" class="rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-medium text-stone-700 mb-1">Nama Shift</label>
            <input type="text" name="name" value="{{ old('name', $shift->name) }}" inputmode="text" class="w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Jam Mulai (WIB)</label>
                <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" class="w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                @error('start_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Jam Selesai (WIB)</label>
                <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" class="w-full rounded-xl border-warm-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                @error('end_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_24_hours" value="1" {{ old('is_24_hours', $shift->is_24_hours) ? 'checked' : '' }} class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20" id="is_24_hours">
            <label for="is_24_hours" class="text-sm text-stone-700">Shift 24 Jam <span class="text-xs text-stone-400">(lintas tengah malam, contoh 22:00 - 06:00 WIB)</span></label>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $shift->is_active) ? 'checked' : '' }} class="rounded border-warm-300 text-theme-primary focus:ring-theme-primary/20" id="is_active">
            <label for="is_active" class="text-sm text-stone-700">Shift Aktif</label>
        </div>
        <button type="submit" class="w-full rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 transition-all">Simpan Perubahan</button>
    </form>
</div>
@endsection
