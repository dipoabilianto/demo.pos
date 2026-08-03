@extends('layouts.app')
@section('title', 'Bahan Baku')
@php
    $subtitleText = $tab === 'opname' ? 'Lihat dan sesuaikan stok bahan baku.' : 'Kelola stok bahan baku produksi.';
@endphp
@section('subtitle', $subtitleText)
@section('content')

<div class="mb-6 flex gap-1 rounded-xl bg-white p-1.5 shadow-sm border border-warm-200/50 w-fit">
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'list']) }}"
       class="rounded-lg px-5 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'list' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
            Daftar
        </div>
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab' => 'opname']) }}"
       class="rounded-lg px-5 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'opname' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
        <div class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m-6.364-.636l12.728 0M4.5 21h15" /><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12.75l1.5-9h7.5l1.5 9" /></svg>
            Opname
        </div>
    </a>
</div>

@if ($tab === 'opname')
    @include('raw-materials._tab_opname')
@else
    @include('raw-materials._tab_list')
@endif

@endsection
