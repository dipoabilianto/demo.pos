@php use App\Services\SettingService; $storeSettings = app(SettingService::class)->getSettings(); @endphp

<nav x-cloak x-show="sidebarOpen"
     x-transition:enter="transition-transform duration-300 ease-out"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition-transform duration-300 ease-in"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="flex flex-col shrink-0 overflow-x-hidden
            fixed inset-y-0 left-0 z-50 lg:relative lg:z-0
            w-72"
     @click="isMobile && $event.target.closest('a[href]') && (sidebarOpen = false)"
     style="background: linear-gradient(to bottom, var(--theme-sidebar), color-mix(in srgb, var(--theme-sidebar) 70%, #020617));">

    <div x-show="sidebarOpen && isMobile"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <div class="group flex h-16 shrink-0 items-center px-6 gap-x-3 border-b border-white/5 transition-colors duration-300 hover:bg-white/[0.03]">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-gradient shadow-theme-shadow overflow-hidden transition-all duration-500 group-hover:shadow-lg group-hover:scale-105 group-hover:rotate-2">
            @if (!empty($storeSettings['receipt_logo']))
                <img src="{{ asset('storage/' . $storeSettings['receipt_logo']) }}" class="h-10 w-10 object-contain transition-transform duration-500 group-hover:scale-110">
            @else
                <svg class="h-6 w-6 text-white transition-transform duration-500 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                </svg>
            @endif
        </div>
        <div>
            <span class="text-lg font-bold text-theme-sidebar-text tracking-tight transition-colors duration-300 group-hover:text-white">{{ $storeSettings['store_name'] ?? 'Oribun Bakery' }}</span>
            <p class="text-xs text-theme-sidebar-text -mt-0.5">Management System</p>
        </div>
    </div>

    <div class="flex flex-1 flex-col overflow-y-auto pt-4 pb-4 px-3">
        <x-layout.branch-selector />

        @php
            $u = auth()->user();
            $isTopLevel = $u && ($u->isSuperadmin() || $u->isOwner());
            $showPengaturan = $u && ($u->hasPermission('settings.view') || $u->hasPermission('users.view') || $u->hasPermission('roles.view') || $u->hasPermission('branches.view') || $u->hasPermission('payment-methods.view') || $u->hasPermission('shifts.view'));
        @endphp

        @if ($isTopLevel)
        <nav class="flex-1 space-y-0.5">
            @if ($u?->hasPermission('orders.create'))
                <x-nav-link href="{{ route('orders.catalog') }}" :active="request()->routeIs('orders.catalog') || request()->routeIs('orders.checkout')" icon="cake">
                    Transaksi Baru
                </x-nav-link>
            @endif
            @if ($u && ($u->isOwner() || $u->isSuperadmin()))
                <x-nav-link href="{{ route('owner.dashboard') }}" :active="request()->routeIs('owner.dashboard')" icon="chart">
                    Owner Dashboard
                </x-nav-link>
            @endif
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-nav-link>
            @if ($u?->hasPermission('orders.view'))
                <x-nav-link href="{{ route('orders.history') }}" :active="request()->routeIs('orders.history') || request()->routeIs('orders.show')" icon="clipboard">
                    Riwayat Pesanan
                    <span id="order-badge" class="ml-auto hidden h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-red-300">0</span>
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('products.view'))
                <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')" icon="croissant">
                    Produk
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('raw-materials.view') || $u?->hasPermission('stock-opname.view'))
                <x-nav-link href="{{ route('raw-materials.index') }}" :active="request()->routeIs('raw-materials.*') || request()->routeIs('stock-opname.*')" icon="package">
                    Bahan Baku
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('expenses.view'))
                <x-nav-link href="{{ route('expenses.index') }}" :active="request()->routeIs('expenses.*')" icon="cookie">
                    Pengeluaran
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('reports.view'))
                <x-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')" icon="chart">
                    Laporan
                </x-nav-link>
            @endif
            @if ($u?->canAttendance())
                <x-nav-link href="{{ route('attendances.index') }}" :active="request()->routeIs('attendances.*')" icon="clipboard">
                    Absensi
                </x-nav-link>
            @endif
        </nav>

        @else
        {{-- Flat: admin & below --}}
        <nav class="flex-1 space-y-0.5">
            @if ($u?->hasPermission('orders.create'))
                <x-nav-link href="{{ route('orders.catalog') }}" :active="request()->routeIs('orders.catalog') || request()->routeIs('orders.checkout')" icon="cake">
                    Transaksi Baru
                </x-nav-link>
            @endif
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
                Dashboard
            </x-nav-link>
            @if ($u?->hasPermission('orders.view'))
                <x-nav-link href="{{ route('orders.history') }}" :active="request()->routeIs('orders.history') || request()->routeIs('orders.show')" icon="clipboard">
                    Riwayat Pesanan
                    <span id="order-badge" class="ml-auto hidden h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-[10px] font-bold text-white ring-2 ring-red-300">0</span>
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('products.view'))
                <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.*')" icon="croissant">
                    Produk
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('raw-materials.view') || $u?->hasPermission('stock-opname.view'))
                <x-nav-link href="{{ route('raw-materials.index') }}" :active="request()->routeIs('raw-materials.*') || request()->routeIs('stock-opname.*')" icon="package">
                    Bahan Baku
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('expenses.view'))
                <x-nav-link href="{{ route('expenses.index') }}" :active="request()->routeIs('expenses.*')" icon="cookie">
                    Pengeluaran
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('reports.view'))
                <x-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')" icon="chart">
                    Laporan
                </x-nav-link>
            @endif
            @if ($u?->canAttendance())
                <x-nav-link href="{{ route('attendances.index') }}" :active="request()->routeIs('attendances.*')" icon="clipboard">
                    Absensi
                </x-nav-link>
            @endif
        </nav>

        @endif
    </div>

    @if ($showPengaturan)
    <div class="border-t border-white/10 pt-2 px-3">
        <p class="sidebar-section-label px-3 text-xs font-semibold uppercase tracking-widest mb-1">Pengaturan</p>
        <div class="space-y-0.5 pb-2">
            @if ($u?->hasPermission('settings.view') || $u?->hasPermission('users.view') || $u?->hasPermission('roles.view') || $u?->hasPermission('shifts.view'))
                <x-nav-link href="{{ route('settings.general') }}" :active="request()->routeIs('settings.*') && !request()->routeIs('settings.cabang*') && !request()->routeIs('settings.payment-methods.*') && !request()->routeIs('settings.users.*') && !request()->routeIs('settings.roles.*')" icon="cog">
                    Pengaturan
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('users.view') || $u?->hasPermission('roles.view'))
                <x-nav-link href="{{ route('settings.users.index') }}" :active="request()->routeIs('settings.users.*') || request()->routeIs('settings.roles.*')" icon="users">
                    Pengguna & Role
                </x-nav-link>
            @endif
            @if ($u?->hasPermission('branches.view'))
                <x-nav-link href="{{ route('settings.cabang') }}" :active="request()->routeIs('settings.cabang*')" icon="store">
                    Cabang & Tipe Bisnis
                </x-nav-link>
            @endif
        </div>
    </div>
    @endif

    <div class="border-t border-white/10 px-3 py-3">
        <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-theme-gradient text-xs font-bold text-white shadow-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-white/50 truncate">{{ auth()->user()->email ?? 'admin@oribun.app' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-lg p-1.5 text-white/30 hover:text-white/70 hover:bg-white/5 transition-all" title="Logout">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                </button>
            </form>
        </div>
    </div>
</nav>
