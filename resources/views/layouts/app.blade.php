<!DOCTYPE html>
<html lang="id" class="h-full" style="background-color:var(--bg-base)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php use App\Services\SettingService; $storeSettings = app(SettingService::class)->getSettings(); @endphp
<title>@yield('title', 'Dashboard') - {{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</title>
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
<body class="h-full antialiased" x-data="toastManager">
    <div x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        isMobile: window.innerWidth < 1024
    }"
    @resize.window="isMobile = window.innerWidth < 1024; if(window.innerWidth >= 1024) sidebarOpen = true"
    x-init="$watch('isMobile', val => { if(!val) sidebarOpen = true })"
    class="h-screen overflow-hidden">
        <div class="flex h-full">
            <x-layout.sidebar />

        <div class="flex flex-col flex-1 min-w-0 overflow-y-auto">
            <x-layout.topbar :title="$__env->yieldContent('title', 'Dashboard')" :subtitle="$__env->yieldContent('subtitle', '')" />

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <footer class="border-t border-warm-200/50 px-8 py-4" style="background-color:color-mix(in srgb,var(--bg-base) 50%,white)">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p class="text-xs text-warm-400">
                        &copy; {{ date('Y') }} <span class="font-medium text-warm-500">{{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</span>. All rights reserved.
                    </p>
                    <div class="flex items-center gap-3 text-xs text-warm-400">
                        <a href="{{ route('settings.general') }}" class="hover:text-theme-primary transition-colors">Pengaturan</a>
                        <span class="w-px h-3 bg-warm-200"></span>
                        <span>v1.0</span>
                    </div>
                </div>
            </footer>
        </div>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="toasts.length > 0" class="fixed top-4 right-4 z-[100] space-y-2 w-80">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-show="toast.id" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                    class="flex items-start gap-3 p-4 rounded-xl shadow-lg backdrop-blur-sm border"
                    :class="{
                        'bg-emerald-50 border-emerald-200 text-emerald-800': toast.type === 'success',
                        'bg-rose-50 border-rose-200 text-rose-800': toast.type === 'error',
                        'bg-amber-50 border-amber-200 text-amber-800': toast.type === 'warning',
                        'bg-sky-50 border-sky-200 text-sky-800': toast.type === 'info',
                    }">
                    <div class="shrink-0" x-html="toast.type === 'success' ? '&check;' : toast.type === 'error' ? '&times;' : '&hellip;'"></div>
                    <p class="text-sm font-medium flex-1" x-text="toast.message"></p>
                    <button @click="remove(toast.id)" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">&times;</button>
                </div>
            </template>
        </div>
    </template>

    @stack('scripts')

    <script>
    (function() {
        if (!window.__notifPresets) {
            var sr = 44100;
            function makeWav(duration, genFn) {
                var ns = Math.floor(sr * duration);
                var buf = new ArrayBuffer(44 + ns * 2);
                var v = new DataView(buf);
                function w(str, off) { for (var i = 0; i < str.length; i++) v.setUint8(off + i, str.charCodeAt(i)); }
                w('RIFF', 0);
                v.setUint32(4, 36 + ns * 2, true);
                w('WAVE', 8);
                w('fmt ', 12);
                v.setUint32(16, 16, true);
                v.setUint16(20, 1, true);
                v.setUint16(22, 1, true);
                v.setUint32(24, sr, true);
                v.setUint32(28, sr * 2, true);
                v.setUint16(32, 2, true);
                v.setUint16(34, 16, true);
                w('data', 36);
                v.setUint32(40, ns * 2, true);
                var samples = new Float32Array(ns);
                genFn(samples, sr);
                for (var i = 0; i < ns; i++) {
                    var s = Math.max(-1, Math.min(1, samples[i]));
                    v.setInt16(44 + i * 2, s * 32767, true);
                }
                return URL.createObjectURL(new Blob([buf], { type: 'audio/wav' }));
            }
            window.__notifPresets = {
                nada1: makeWav(0.4, function(s, sr) {
                    for (var i = 0; i < s.length; i++) {
                        var t = i / sr;
                        var freq = t < 0.12 ? 800 : t < 0.24 ? 1000 : 1200;
                        s[i] = Math.sin(2 * Math.PI * freq * t) * Math.max(0, 1 - t / 0.4) * 0.25;
                    }
                }),
                nada2: makeWav(0.45, function(s, sr) {
                    for (var i = 0; i < s.length; i++) {
                        var t = i / sr;
                        var freq = t < 0.15 ? 1200 : t < 0.3 ? 1000 : 800;
                        s[i] = Math.sin(2 * Math.PI * freq * t) * Math.max(0, 1 - t / 0.45) * 0.25;
                    }
                }),
                nada3: makeWav(0.5, function(s, sr) {
                    for (var i = 0; i < s.length; i++) {
                        var t = i / sr;
                        var env = Math.max(0, 1 - t / 0.5);
                        s[i] = (Math.sin(2 * Math.PI * 523 * t) + Math.sin(2 * Math.PI * 659 * t)) * 0.5 * env * 0.25;
                    }
                }),
                nada4: makeWav(0.5, function(s, sr) {
                    for (var i = 0; i < s.length; i++) {
                        var t = i / sr;
                        var phase = t % 0.2;
                        if (phase < 0.1) {
                            s[i] = Math.sin(2 * Math.PI * 880 * t) * (1 - phase / 0.1) * 0.25;
                        }
                    }
                }),
            };
            window.__playNotifSound = function(preset, customUrl) {
                try {
                    var url = preset === 'custom' ? customUrl : window.__notifPresets[preset];
                    if (!url) return;
                    var a = new Audio(url);
                    a.volume = 0.25;
                    a.play();
                } catch(e) { console.error('[NotifSound]', e); }
            };
        }

        var POLL_INTERVAL = 5000;
        var REMINDER_INTERVAL = 60000;

        var lastCheckedAt = localStorage.getItem('notif_last_checked') || '';
        var seenIds = JSON.parse(localStorage.getItem('notif_seen_ids') || '[]');
        var notifiedIds = new Set();
        var popupTimer = null;
        var latestServerTime = '';

        @php
            $notifSoundEnabled = $storeSettings['notification_sound_enabled'] ?? true;
            $notifSoundPreset = $storeSettings['notification_sound_preset'] ?? 'nada1';
            $notifSoundFile = !empty($storeSettings['notification_sound_file']) ? asset('storage/' . $storeSettings['notification_sound_file']) : '';
        @endphp
        var notifSoundEnabled = {{ $notifSoundEnabled ? 'true' : 'false' }};
        var notifSoundPreset = '{{ $notifSoundPreset }}';
        var notifSoundUrl = '{{ $notifSoundFile }}';

        window.__markNotifSeen = function() {
            seenIds = [...notifiedIds];
            localStorage.setItem('notif_seen_ids', JSON.stringify(seenIds));
            updateBadge();
        };

        function playNotifSound() {
            if (!notifSoundEnabled) return;
            window.__playNotifSound(notifSoundPreset, notifSoundUrl);
        }

        function updateBadge() {
            var badge = document.getElementById('order-badge');
            if (!badge) return;
            var unseen = [...notifiedIds].filter(function(id) { return !seenIds.includes(id); }).length;
            if (unseen > 0) {
                badge.textContent = unseen > 99 ? '99+' : unseen;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function checkReminder() {
            if (!notifSoundEnabled) return;
            var unseen = [...notifiedIds].filter(function(id) { return !seenIds.includes(id); }).length;
            if (unseen > 0) {
                window.__playNotifSound(notifSoundPreset, notifSoundUrl);
            }
        }

        function showOrderPopup(data) {
            var existing = document.getElementById('new-order-popup');
            if (existing) existing.remove();

            var title = data.type === 'new_order' ? 'Pesanan Baru' : data.type === 'payment_pending' ? 'Pembayaran Perlu Konfirmasi' : 'Pembayaran Masuk';
            var iconBg = data.type === 'new_order' ? 'bg-blue-100 text-blue-600' : data.type === 'payment_pending' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600';

            var popup = document.createElement('div');
            popup.id = 'new-order-popup';
            popup.className = 'fixed bottom-6 right-6 z-[100] w-80 animate-fadeUp';
            popup.innerHTML =
                '<div class="glass-strong rounded-2xl p-4 shadow-xl">' +
                    '<div class="flex items-start justify-between mb-2">' +
                        '<div class="flex items-center gap-2">' +
                            '<span class="flex h-8 w-8 items-center justify-center rounded-full ' + iconBg + '">' +
                                '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>' +
                            '</span>' +
                            '<div>' +
                                '<p class="text-sm font-bold text-stone-900">' + title + '</p>' +
                                '<p class="text-[11px] text-stone-400">' + data.created_at + '</p>' +
                            '</div>' +
                        '</div>' +
                        '<button onclick="this.closest(\'#new-order-popup\').remove()" class="flex h-6 w-6 items-center justify-center rounded text-stone-400 hover:text-stone-600 hover:bg-stone-100 transition-all">' +
                            '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>' +
                        '</button>' +
                    '</div>' +
                    '<div class="bg-white/50 rounded-xl px-3 py-2.5 mb-3 space-y-1">' +
                        '<p class="text-xs font-semibold text-theme-primary">' + data.order_number + '</p>' +
                        '<p class="text-sm font-medium text-stone-800">' + (data.customer_name || 'Pelanggan') + '</p>' +
                        '<p class="text-xs text-stone-400">' + data.item_count + ' item &middot; Rp ' + Number(data.total).toLocaleString('id-ID') + '</p>' +
                    '</div>' +
                    '<div class="flex gap-2">' +
                        '<a href="/orders/' + data.id + '" onclick="__markNotifSeen()" class="flex-1 rounded-xl bg-theme-primary text-white text-sm font-semibold py-2 text-center hover:opacity-90 transition-all">Proses</a>' +
                        '<button onclick="this.closest(\'#new-order-popup\').remove()" class="flex-1 rounded-xl bg-stone-100 text-stone-600 text-sm font-medium py-2 hover:bg-stone-200 transition-all">Nanti</button>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(popup);

            if (popupTimer) clearTimeout(popupTimer);
            popupTimer = setTimeout(function() {
                if (popup.parentNode) popup.remove();
            }, 20000);
        }

        async function checkNewOrder() {
            try {
                var url = '/api/orders/latest';
                if (lastCheckedAt) url += '?since=' + encodeURIComponent(lastCheckedAt);
                var res = await fetch(url);
                var json = await res.json();
                if (!json || !json.orders) return;

                latestServerTime = json.server_time || new Date().toISOString();

                json.orders.forEach(function(data) {
                    if (!data || typeof data.id !== 'number') return;
                    if (notifiedIds.has(data.id)) return;

                    notifiedIds.add(data.id);

                    if (data.type === 'new_order') {
                        playNotifSound();
                        showOrderPopup(data);
                    }

                    if (document.hidden && 'Notification' in window && Notification.permission === 'granted' && data.type) {
                        var notifTitle = data.type === 'new_order' ? 'Pesanan Baru' : data.type === 'payment_pending' ? 'Pembayaran Perlu Dikonfirmasi' : 'Pembayaran Masuk';
                        new Notification(notifTitle, {
                            body: data.order_number + ' - ' + (data.customer_name || 'Pelanggan'),
                            icon: '/favicon.ico',
                        });
                    }
                });

                updateBadge();
                lastCheckedAt = latestServerTime;
                localStorage.setItem('notif_last_checked', lastCheckedAt);
            } catch(e) {}
        }

        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        (function() {
            var path = window.location.pathname;
            if (path === '/orders/history' || /^\/orders\/\d+$/.test(path)) {
                var seenTimer = setInterval(function() {
                    if (notifiedIds.size > 0) {
                        window.__markNotifSeen();
                        clearInterval(seenTimer);
                    }
                }, 1000);
            }
        })();

        setInterval(checkNewOrder, POLL_INTERVAL);
        setTimeout(checkNewOrder, 3000);

        setInterval(checkReminder, REMINDER_INTERVAL);
    })();
    </script>

    @if (session('success'))
        <script>
            (function(){
                var msg = {{ Js::from(session('success')) }};
                function showToast(){
                    var el = document.createElement('div');
                    el.className = 'fixed top-4 right-4 z-[999] flex items-start gap-3 p-4 rounded-xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800 animate-toastIn';
                    el.innerHTML = '<div class="shrink-0 font-bold text-lg leading-none">&check;</div><p class="text-sm font-medium flex-1">' + msg + '</p><button onclick="this.parentElement.remove()" class="shrink-0 opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>';
                    document.body.appendChild(el);
                    setTimeout(function(){ if(el.parentNode) el.remove(); }, 5000);
                }
                function tryAlpine(){
                    if(window.Alpine && document.body && document.body.__x){
                        Alpine.$data(document.body).success(msg);
                    } else {
                        showToast();
                    }
                }
                if(document.readyState === 'complete') tryAlpine();
                else window.addEventListener('load', tryAlpine);
            })();
        </script>
    @endif
    @if ($errors->any())
        <script>
            (function(){
                var msg = '';
                @foreach ($errors->all() as $error)
                    msg += {{ Js::from($error . '\n') }};
                @endforeach
                msg = msg.trim();
                function showToast(){
                    var el = document.createElement('div');
                    el.className = 'fixed top-4 right-4 z-[100] flex items-start gap-3 p-4 rounded-xl shadow-lg border bg-rose-50 border-rose-200 text-rose-800 animate-toastIn';
                    el.innerHTML = '<div class="shrink-0 font-bold text-lg leading-none">&times;</div><p class="text-sm font-medium flex-1">' + msg.replace(/\n/g, '<br>') + '</p><button onclick="this.parentElement.remove()" class="shrink-0 opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>';
                    document.body.appendChild(el);
                    setTimeout(function(){ if(el.parentNode) el.remove(); }, 5000);
                }
                function tryAlpine(){
                    if(window.Alpine && document.body && document.body.__x){
                        Alpine.$data(document.body).error(msg);
                    } else {
                        showToast();
                    }
                }
                if(document.readyState === 'complete') tryAlpine();
                else window.addEventListener('load', tryAlpine);
            })();
        </script>
    @endif
</body>
</html>
