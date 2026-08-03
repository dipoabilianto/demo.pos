@props(['title' => 'Dashboard', 'subtitle' => ''])

<header class="sticky top-0 z-40 backdrop-blur-md border-b border-warm-200/50" style="background-color:color-mix(in srgb,var(--bg-base) 80%,transparent)">
    <div class="flex items-center justify-between px-8 h-16">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-warm-600 hover:bg-warm-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div>
                <h1 class="text-lg font-semibold text-warm-900">{!! $title !!}</h1>
                @if ($subtitle)
                    <p class="text-xs text-warm-500">{!! $subtitle !!}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php $topbarBranch = session('branch_id') ? \App\Models\Branch::find(session('branch_id')) : null; @endphp
            @if($topbarBranch)
            <a href="{{ route('orders.public-catalog', ['branch_id' => $topbarBranch->id]) }}" target="_blank" rel="noopener noreferrer" class="hidden md:flex items-center gap-2 rounded-lg bg-theme-primary/10 px-4 py-2 text-sm font-medium text-theme-primary hover:bg-theme-primary/20 transition-colors ring-1 ring-theme-primary/20">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>
                Toko Online
            </a>
            @endif
        </div>
    </div>
</header>
