<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php use App\Services\SettingService; $storeSettings = app(SettingService::class)->getSettings(); @endphp
    <title>@yield('title', $storeSettings['store_name'] ?? 'Oribun Bakery') - {{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</title>
    @php $favicon = !empty($storeSettings['favicon']) ? asset('storage/' . $storeSettings['favicon']) : null; @endphp
    <link rel="icon" type="{{ $favicon ? 'image/png' : 'image/svg+xml' }}" href="{{ $favicon ?: 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke-width=%221.5%22 stroke=%22%23d97706%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244%22 /%3E%3C/svg%3E' }}">
    <style>
        :root {
            --theme-primary: {{ $storeSettings['theme_primary'] ?? '#d97706' }};
            --theme-sidebar: {{ $storeSettings['theme_sidebar'] ?? '#3b1e10' }};
            --theme-accent: {{ $storeSettings['theme_accent'] ?? '#f59e0b' }};
            --theme-sidebar-text: {{ $storeSettings['theme_sidebar_text'] ?? '#ffffff' }};
            --bg-base: {{ $storeSettings['bg_base'] ?? '#fdf8f0' }};
            --bg-gradient: {{ $storeSettings['bg_gradient'] ?? '#fde68a' }};
            --bg-blob: {{ $storeSettings['bg_blob'] ?? ($storeSettings['theme_accent'] ?? '#f59e0b') }};
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased animated-bg overflow-x-hidden">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="relative z-10">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
