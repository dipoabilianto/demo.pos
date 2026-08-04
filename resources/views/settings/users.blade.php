@extends('layouts.app')

@section('title', 'Pengguna & Role')
@section('subtitle', 'Kelola akses pengguna, divisi, dan hak akses role')

@section('content')
<div>
    <div class="mb-6 flex gap-1 overflow-x-auto rounded-xl bg-white p-1.5 shadow-sm border border-warm-200/50 max-w-full lg:w-fit">
        @if (in_array('users', $allowedTabs))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'users']) }}" class="shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'users' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                Pengguna
            </div>
        </a>
        @endif
        @if (in_array('roles', $allowedTabs))
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'roles']) }}" class="shrink-0 rounded-lg px-4 py-2 text-sm font-medium transition-all duration-200 {{ $tab === 'roles' ? 'bg-theme-primary text-white shadow-sm' : 'text-warm-600 hover:bg-warm-100' }}">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                Role & Hak Akses
            </div>
        </a>
        @endif
    </div>

    @if ($tab === 'users' && in_array('users', $allowedTabs))
        @include('settings.tabs.users')
    @elseif ($tab === 'roles' && in_array('roles', $allowedTabs))
        @include('settings.tabs.roles')
    @endif
</div>
@endsection
