@inject('settings', 'App\Http\Controllers\SettingsController')
@php $storeSettings = $settings::getSettings(); @endphp
<!DOCTYPE html>
<html lang="id" class="h-full"
    style="--theme-primary: {{ $storeSettings['theme_primary'] ?? '#d97706' }}; --theme-sidebar: {{ $storeSettings['theme_sidebar'] ?? '#3b1e10' }}; --theme-sidebar-text: {{ $storeSettings['theme_sidebar_text'] ?? '#ffffff' }}; --theme-accent: {{ $storeSettings['theme_accent'] ?? '#f59e0b' }}; --bg-base: {{ $storeSettings['bg_base'] ?? '#fdf8f0' }}; --bg-gradient: {{ $storeSettings['bg_gradient'] ?? '#fde68a' }}; --bg-blob: {{ $storeSettings['bg_blob'] ?? ($storeSettings['theme_accent'] ?? '#f59e0b') }};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</title>
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
                {{-- Mobile branding --}}
                <div class="text-center mb-8 lg:hidden">
                    <div class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-theme-gradient shadow-lg shadow-theme-shadow">
                        <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.5c0-3.5 2-7.5 7.5-7.5s7.5 4 7.5 7c0 .8-.3 1.5-1.2 2H5.7c-.9-.5-1.2-1.2-1.2-2z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.7 17.5c0 1.2 1.2 2.5 6.3 2.5s6.3-1.3 6.3-2.5" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-stone-900">{{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</h2>
                    <p class="text-sm text-stone-500 mt-1">{{ $loginDesc }}</p>
                </div>

                <div class="rounded-2xl bg-white p-8 ring-1 ring-stone-100 shadow-xl shadow-stone-200/50">
                    <h2 class="text-xl font-bold text-stone-900">Selamat datang kembali</h2>
                    <p class="text-sm text-stone-500 mb-6">Silakan masuk ke akun Anda</p>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="login" class="block text-sm font-medium text-stone-700 mb-1.5">Email atau Nama</label>
                            <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                                class="block w-full rounded-xl border-stone-300 px-4 py-2.5 text-sm text-stone-900 placeholder-stone-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                                placeholder="admin@oribun.app atau nama user">
                            @error('login')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-stone-700 mb-1.5">Password</label>
                            <input type="password" name="password" id="password" required
                                class="block w-full rounded-xl border-stone-300 px-4 py-2.5 text-sm text-stone-900 placeholder-stone-400 focus:border-theme-primary focus:ring-2 focus:ring-theme-primary/20 transition-all"
                                placeholder="Masukkan password">
                            @error('password')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" class="rounded border-stone-300 text-theme-primary focus:ring-theme-primary/20">
                                <span class="text-sm text-stone-600">Ingat saya</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm text-theme-primary hover:text-theme-primary/80 font-medium transition-colors">
                                Lupa password?
                            </a>
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-theme-gradient-r px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:opacity-90 hover:shadow-xl hover:shadow-theme-shadow active:scale-[0.98] transition-all">
                            Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2 w-80" x-data="toastManager">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.id" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                class="flex items-start gap-3 p-4 rounded-xl shadow-lg backdrop-blur-sm border"
                :class="{
                    'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type === 'success',
                    'bg-rose-50 border-rose-200 text-rose-800': toast.type === 'error',
                    'bg-amber-50 border-amber-200 text-amber-800': toast.type === 'warning',
                    'bg-sky-50 border-sky-200 text-sky-800': toast.type === 'info',
                }">
                <div class="shrink-0" x-html="toast.type === 'success' ? '&#10003;' : toast.type === 'error' ? '&#10007;' : '&#8230;'"></div>
                <p class="text-sm font-medium flex-1" x-text="toast.message"></p>
                <button @click="remove(toast.id)" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">&times;</button>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                setTimeout(function() {
                    Alpine.$data(document.getElementById('toast-container')).success({{ Js::from(session('success')) }});
                }, 200);
            @endif
        });
    </script>
</body>
</html>
