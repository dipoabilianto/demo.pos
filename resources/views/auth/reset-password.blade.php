@inject('settings', 'App\Http\Controllers\SettingsController')
@php $storeSettings = $settings::getSettings(); @endphp
<!DOCTYPE html>
<html lang="id" class="h-full"
    style="--theme-primary: {{ $storeSettings['theme_primary'] ?? '#d97706' }}; --theme-sidebar: {{ $storeSettings['theme_sidebar'] ?? '#3b1e10' }}; --theme-sidebar-text: {{ $storeSettings['theme_sidebar_text'] ?? '#ffffff' }}; --theme-accent: {{ $storeSettings['theme_accent'] ?? '#f59e0b' }}; --bg-base: {{ $storeSettings['bg_base'] ?? '#fdf8f0' }}; --bg-gradient: {{ $storeSettings['bg_gradient'] ?? '#fde68a' }}; --bg-blob: {{ $storeSettings['bg_blob'] ?? ($storeSettings['theme_accent'] ?? '#f59e0b') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full antialiased bg-white">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>

    <div class="relative z-10 flex min-h-full flex-col lg:flex-row">
        {{-- Left Panel (65%) --}}
        @php
            $loginLogo = !empty($storeSettings['login_logo']) ? asset('storage/' . $storeSettings['login_logo']) : null;
            $loginBg = !empty($storeSettings['login_background']) ? asset('storage/' . $storeSettings['login_background']) : null;
            $loginDesc = ($storeSettings['login_description'] ?? '') ?: ($storeSettings['store_description'] ?? '');
        @endphp
        <div class="relative flex items-center justify-center overflow-hidden px-8 py-16 lg:w-[65%] lg:px-16 {{ $loginBg ? '' : 'animated-bg' }}"
            @if($loginBg) style="background-image: url('{{ $loginBg }}'); background-size: cover; background-position: center;" @endif>
            @if($loginBg)
                <div class="absolute inset-0 bg-black/40"></div>
            @endif

            <div class="absolute inset-0 backdrop-blur-xl {{ $loginBg ? 'bg-white/5' : 'bg-white/10' }}"></div>

            <div class="relative z-10 max-w-lg text-center {{ $loginBg ? 'text-white' : 'text-stone-900' }}">
                @if($loginLogo)
                    <img src="{{ $loginLogo }}" alt="{{ $storeSettings['store_name'] ?? '' }}"
                        class="mx-auto mb-6 h-20 w-auto object-contain rounded-2xl shadow-lg">
                @else
                    <div class="mx-auto mb-6 inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-theme-gradient shadow-lg shadow-theme-shadow">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.5c0-3.5 2-7.5 7.5-7.5s7.5 4 7.5 7c0 .8-.3 1.5-1.2 2H5.7c-.9-.5-1.2-1.2-1.2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.7 17.5c0 1.2 1.2 2.5 6.3 2.5s6.3-1.3 6.3-2.5" />
                        </svg>
                    </div>
                @endif

                <h1 class="text-3xl font-bold">{{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</h1>
                <p class="mt-3 text-lg {{ $loginBg ? 'text-white/80' : 'text-stone-500' }}">
                    {{ $loginDesc }}
                </p>

                <div class="mx-auto mt-8 h-px w-16 {{ $loginBg ? 'bg-white/20' : 'bg-stone-300' }}"></div>

                <p class="mt-8 text-sm {{ $loginBg ? 'text-white/50' : 'text-stone-400' }}">
                    &copy; {{ date('Y') }} {{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}
                </p>
            </div>
        </div>

        {{-- Right Panel (35%) --}}
        <div class="flex w-full items-center justify-center px-6 py-12 lg:w-[35%]">
            <div class="w-full max-w-sm">
                <div class="text-center mb-8 lg:hidden">
                    <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-gradient shadow-lg shadow-theme-shadow">
                        <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-stone-900">{{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</h2>
                    <p class="text-sm text-stone-500 mt-1">{{ $loginDesc }}</p>
                </div>

                <div class="rounded-2xl bg-white p-8 ring-1 ring-stone-100 shadow-xl shadow-stone-200/50">
                    <h2 class="text-xl font-bold text-stone-900">Reset Password</h2>
                    <p class="text-sm text-stone-500 mb-6">Buat password baru Anda</p>

                    @if ($errors->any())
                        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div>
                            <label for="email" class="block text-sm font-medium text-stone-700 mb-1.5">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" required readonly
                                class="block w-full rounded-xl border-stone-300 px-4 py-2.5 text-sm text-stone-900 bg-stone-50 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-stone-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password" id="password" required minlength="8"
                                class="block w-full rounded-xl border-stone-300 px-4 py-2.5 text-sm text-stone-900 placeholder-stone-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                                placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-stone-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="block w-full rounded-xl border-stone-300 px-4 py-2.5 text-sm text-stone-900 placeholder-stone-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                                placeholder="Ulangi password baru">
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:opacity-90 hover:shadow-xl hover:shadow-theme-shadow active:scale-[0.98] transition-all">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
