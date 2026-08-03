@extends('layouts.public')
@section('title', $settings['catalog_title'] ?? 'Pesan Roti')
@section('content')
<style>
    @keyframes fadeInUp { from { opacity:0;transform:translateY(18px); } to { opacity:1;transform:translateY(0); } }
    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
    @keyframes scaleIn { from { opacity:0;transform:scale(.92); } to { opacity:1;transform:scale(1); } }
    @keyframes scaleOut { from { opacity:1;transform:scale(1); } to { opacity:0;transform:scale(.92); } }
    @keyframes slideUp { from { opacity:0;transform:translateY(12px) scale(.97); } to { opacity:1;transform:translateY(0) scale(1); } }
    @keyframes slideDownOut { from { opacity:1;transform:translateY(0) scale(1); } to { opacity:0;transform:translateY(-8px) scale(.97); } }
    .animate-fadeUp { animation:fadeInUp .5s cubic-bezier(.22,1,.36,1) both; }
    .animate-scaleIn { animation:scaleIn .4s cubic-bezier(.22,1,.36,1) both; }
    .animate-scaleOut { animation:scaleOut .3s cubic-bezier(.22,1,.36,1) both; }
    .cat-section { transition:opacity .3s ease, transform .3s ease; }
    .cat-section.sliding-out { animation:slideDownOut .25s cubic-bezier(.22,1,.36,1) both; }
    .cat-section.sliding-in { animation:slideUp .3s cubic-bezier(.22,1,.36,1) both; }
</style>

<div>
    <header class="sticky top-0 z-40 glass-strong border-b border-white/30">
        <div class="mx-auto max-w-7xl px-3 sm:px-4">
            <div class="flex h-14 sm:h-16 items-center justify-between">
                <a href="{{ route('orders.public-catalog', ['branch_id' => $branch->id]) }}" class="flex items-center gap-2.5 group">
                    @if (!empty($settings['receipt_logo']))
                        <img src="{{ asset('storage/' . $settings['receipt_logo']) }}" class="h-8 sm:h-9 w-auto object-contain rounded-lg">
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-theme-gradient shadow-sm shadow-theme-shadow group-hover:shadow-theme-shadow group-hover:scale-105 transition-all duration-300">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.25v2.5c0 .777.845 1.74 1.976 1.834a30.633 30.633 0 00-1.614 4.481m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M6.362 17.25l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                        </div>
                    @endif
                    <span class="text-sm sm:text-base font-bold text-stone-900 tracking-tight">{{ $settings['store_name'] ?? 'Oribun Bakery' }}</span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        {{ $branch->name }}
                    </span>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    <button id="cart-toggle-btn" class="relative flex items-center gap-1.5 rounded-xl glass-strong px-3 py-2 sm:px-3.5 sm:py-2 text-xs sm:text-sm font-medium text-stone-700 hover:ring-theme-primary/40 hover:text-theme-primary hover:bg-white/80 hover:shadow-md hover:shadow-theme-shadow transition-all duration-200 active:scale-[.97]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                        <span class="hidden sm:inline">Keranjang</span>
                        <span id="cart-badge" class="inline-flex items-center justify-center min-w-[20px] h-[20px] rounded-full bg-theme-gradient-r text-[10px] font-bold text-white px-1 shadow-sm shadow-theme-shadow">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    @if (!$isOnline)
    <div class="bg-rose-600 text-white text-center text-xs font-semibold py-2 px-4">
        Pemesanan Online Sedang Tutup
    </div>
    @endif

    @php $catNames = $products->keys()->toArray(); @endphp
    @if (count($catNames) > 1)
    <div class="sticky top-16 z-30 glass border-b border-white/20">
        <div class="mx-auto max-w-7xl px-3 sm:px-4 py-2 overflow-x-auto scrollbar-thin">
            <div class="flex gap-1.5 min-w-max">
                <button data-cat="all" class="cat-pill active rounded-full px-3 py-1.5 text-[11px] font-semibold bg-theme-gradient-r text-white shadow-sm shadow-theme-shadow">Semua</button>
                @foreach ($catNames as $cat)
                    <button data-cat="{{ Str::slug($cat) }}" class="cat-pill rounded-full px-3 py-1.5 text-[11px] font-medium text-stone-500 bg-stone-100/80 hover:bg-stone-200/80 hover:text-stone-800 hover:shadow-sm">{{ $cat }}</button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @php
        $activePromos = array_values(array_filter($settings['promotions'] ?? [], fn($p) => !empty($p['active'])));
        $activePromos = array_map(fn($p) => array_merge($p, [
            'image' => ($p['image'] ?? null) ? \Illuminate\Support\Facades\Storage::disk('public')->url($p['image']) : null,
        ]), $activePromos);
    @endphp
    @php $promoCount = count($activePromos); @endphp
    @if ($promoCount > 0)
    <div class="mx-auto max-w-7xl px-3 sm:px-4 pt-2 sm:pt-3">
        <div x-data="promoCarousel({{ $promoCount }})" x-init="init()" class="promo-carousel relative shadow rounded-lg overflow-hidden" x-cloak>
            <div class="relative w-full h-full">
                @foreach ($activePromos as $i => $promo)
                <div x-show="current === {{ $i }}" x-transition.opacity.duration.300ms class="absolute inset-0 w-full h-full">
                    @if ($promo['image'] && !empty($promo['link']))
                    <a href="{{ $promo['link'] }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
                        <img src="{{ $promo['image'] }}" width="800" height="350" loading="lazy" class="w-full h-full object-cover" alt="{{ $promo['title'] }}">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4 sm:p-6 hidden md:block">
                            <h5 class="font-bold text-white mb-1 text-base sm:text-lg">{{ $promo['title'] }}</h5>
                            @if ($promo['description'])
                            <p class="text-white/80 text-xs sm:text-sm max-w-xl">{{ $promo['description'] }}</p>
                            @endif
                        </div>
                    </a>
                    @elseif ($promo['image'])
                    <div class="w-full h-full">
                        <img src="{{ $promo['image'] }}" width="800" height="350" loading="lazy" class="w-full h-full object-cover" alt="{{ $promo['title'] }}">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4 sm:p-6 hidden md:block">
                            <h5 class="font-bold text-white mb-1 text-base sm:text-lg">{{ $promo['title'] }}</h5>
                            @if ($promo['description'])
                            <p class="text-white/80 text-xs sm:text-sm max-w-xl">{{ $promo['description'] }}</p>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="w-full h-full bg-gradient-to-br from-theme-primary to-theme-primary/80 flex flex-col items-center justify-center">
                        <div class="inline-flex items-center justify-center rounded-full bg-white/20 mb-2" style="width:48px;height:48px">
                            <svg class="text-white" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h5 class="font-bold text-white mb-1 text-base sm:text-lg">{{ $promo['title'] }}</h5>
                        @if ($promo['description'])
                        <p class="text-theme-primary/80 text-xs sm:text-sm max-w-md text-center px-4">{{ $promo['description'] }}</p>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if ($promoCount > 1)
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                @foreach ($activePromos as $i => $promo)
                <button @click="go({{ $i }})" :class="current === {{ $i }} ? 'bg-white/90 w-3' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300 hover:bg-white/70" aria-label="Slide {{ $i+1 }}"></button>
                @endforeach
            </div>

            <div>
                <button @click="prev()" class="absolute top-1/2 -translate-y-1/2 left-2 z-10 flex items-center justify-center w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm text-white hover:bg-white/40 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                </button>
                <button @click="next()" class="absolute top-1/2 -translate-y-1/2 right-2 z-10 flex items-center justify-center w-8 h-8 rounded-full bg-white/20 backdrop-blur-sm text-white hover:bg-white/40 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <main class="mx-auto max-w-7xl px-3 sm:px-4 py-3 sm:py-4">
        <div class="relative">
            @if (!$isOnline)
            <div class="absolute inset-0 z-40 flex flex-col items-center justify-center bg-white/90 backdrop-blur-sm rounded-2xl min-h-[50vh]">
                <div class="text-center max-w-sm px-6 py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-100 mb-4 mx-auto">
                        <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-stone-800 mb-2">Pemesanan Online Sedang Tutup</h3>
                    <p class="text-sm text-stone-500 leading-relaxed">Saat ini {{ $branch->name }} belum menerima pesanan online. Silakan kunjungi cabang ini secara langsung atau hubungi kami untuk informasi lebih lanjut.</p>
                </div>
            </div>
            @endif
            @forelse ($products as $category => $items)
                    <section class="cat-section reveal mb-4 sm:mb-6" data-cat="{{ Str::slug($category) }}">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-px flex-1 bg-gradient-to-r from-theme-primary/20 to-transparent"></div>
                            <h2 class="text-xs sm:text-sm font-bold text-stone-800 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 21a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0z" /></svg>
                                {{ $category }}
                            </h2>
                            <div class="h-px flex-1 bg-gradient-to-l from-theme-primary/20 to-transparent"></div>
                        </div>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-1.5 sm:gap-2">
                            @foreach ($items as $product)
                                @php $globalIdx = ($globalIdx ?? 0) + 1; @endphp
                                <div class="product-card group glass-card rounded-xl" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sale_price ?? $product->price }}" data-stock="{{ $product->stock }}" data-unlimited="{{ $product->is_unlimited ? 'true' : 'false' }}" data-sold-out="{{ $product->is_sold_out ? 'true' : 'false' }}" style="animation:fadeInUp .4s cubic-bezier(.22,1,.36,1) both;animation-delay:{{ $loop->index * 0.04 }}s">
                                <div class="aspect-[4/3] bg-gradient-to-br from-theme-primary/[0.08] to-stone-100/80 flex items-center justify-center p-3 rounded-t-xl relative overflow-hidden {{ $product->is_sold_out ? 'opacity-50' : '' }}">
                                    @if ($product->is_sold_out)
                                        <div class="absolute inset-0 bg-stone-900/40 flex items-center justify-center z-10">
                                            <span class="rotate-[-12deg] text-sm sm:text-base font-black tracking-widest text-white drop-shadow-lg">SOLD</span>
                                        </div>
                                    @endif
                                    @if ($product->isUnlimited() && !$product->is_sold_out)
                                        <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 rounded-full bg-white/90 backdrop-blur-sm px-1.5 py-0.5 text-[8px] font-semibold text-sky-600 shadow-sm ring-1 ring-sky-200/30">∞</span>
                                    @elseif ($product->isLowStock())
                                        <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-1 rounded-full bg-white/90 backdrop-blur-sm px-1.5 py-0.5 text-[8px] font-semibold text-rose-600 shadow-sm ring-1 ring-rose-200/30"><span class="h-1 w-1 rounded-full bg-rose-500 animate-pulse"></span>{{ $product->stock }}</span>
                                    @endif
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="400" height="300" loading="lazy" @if($globalIdx <= 6) fetchpriority="high" @endif class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out cursor-pointer" onclick="addToCart({{ $product->id }})">
                                    @else
                                        <svg class="h-8 w-8 sm:h-10 sm:w-10 text-theme-primary/15 group-hover:text-theme-primary/30 transition-all duration-500 group-hover:scale-110 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" onclick="addToCart({{ $product->id }})"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.056C6.59 7.475 6 8.938 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                                    @endif
                                </div>
                                <div class="p-2 sm:p-2.5">
                                    <h3 class="text-[11px] sm:text-xs font-semibold text-stone-900 leading-snug group-hover:text-theme-primary transition-colors duration-200 {{ $product->is_sold_out ? 'line-through text-stone-400' : '' }}">{{ $product->name }}</h3>
                                    <div class="mt-2 flex items-center justify-between gap-1">
                                        @if ($product->sale_price)
                                            <div>
                                                <span class="text-[10px] font-medium text-stone-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                                <span class="text-xs sm:text-sm font-bold text-rose-600 block">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs sm:text-sm font-bold text-theme-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                        <button onclick="addToCart({{ $product->id }})" class="add-to-cart-btn rounded-lg {{ $product->is_sold_out ? 'bg-stone-300 cursor-not-allowed pointer-events-none opacity-60' : 'bg-theme-gradient-r' }} p-1.5 text-white shadow-sm shadow-theme-shadow hover:shadow-md hover:shadow-theme-shadow hover:opacity-90 active:scale-90 transition-all duration-200" {{ $product->is_sold_out ? 'disabled' : '' }}>
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="text-center py-16 animate-fadeUp">
                        <div class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-theme-primary/10 mb-4">
                            <svg class="h-7 w-7 text-theme-primary/40" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.25v2.5c0 .777.845 1.74 1.976 1.834a30.633 30.633 0 00-1.614 4.481m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M6.362 17.25l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                        </div>
                        <p class="text-stone-500 text-sm font-medium">Belum ada roti tersedia</p>
                        <p class="text-stone-400 text-xs mt-0.5">Silakan cek kembali nanti!</p>
                    </div>
                @endforelse
        </div>
    </main>

    <footer class="glass mt-6 sm:mt-8 rounded-t-2xl animate-fadeUp transition-all duration-700 hover:shadow-lg hover:shadow-black/5">
        <div class="mx-auto max-w-7xl px-3 sm:px-4 py-4 sm:py-5">
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-stone-500">
                @if (!empty($settings['store_address']))
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <span>{{ $settings['store_address'] }}</span>
                    </span>
                @endif
                @if (!empty($settings['store_phone']))
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <span>{{ $settings['store_phone'] }}</span>
                    </span>
                @endif
                @if (!empty($settings['store_whatsapp']))
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['store_whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-1.5 hover:text-emerald-600 transition-colors">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                        <span>WhatsApp</span>
                    </a>
                @endif
                @if (!empty($settings['store_email']))
                    <span class="flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <span>{{ $settings['store_email'] }}</span>
                    </span>
                @endif
            </div>
            @if (!empty($settings['store_description']))
                <div class="mt-2.5 text-xs text-stone-400 text-center max-w-lg mx-auto italic leading-relaxed">{{ $settings['store_description'] }}</div>
            @endif
            <div class="mt-3 text-center text-xs text-stone-400">&copy; {{ date('Y') }} {{ $settings['store_name'] ?? 'Oribun Bakery' }}</div>
        </div>
    </footer>
</div>

{{-- Cart Centered Modal --}}
<div id="cart-overlay" class="fixed inset-0 z-50 bg-stone-900/20 backdrop-blur-md hidden opacity-0"></div>
<div id="cart-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div id="cart-modal-inner" class="w-full max-w-lg glass-strong rounded-2xl shadow-2xl hidden">
        <div class="flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200/60">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10">
                        <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Keranjang</h3>
                        <p id="cart-sidebar-count" class="text-[11px] text-stone-400">0 item</p>
                    </div>
                </div>
                <button id="cart-close-btn" class="flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div id="cart-sidebar-items" class="flex-1 overflow-y-auto px-4 py-4 space-y-2 scrollbar-thin">
                <div class="flex flex-col items-center justify-center py-12 text-stone-300">
                    <svg class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    <p class="text-xs font-medium text-stone-400">Keranjang kosong</p>
                    <p class="text-[11px] text-stone-300 mt-0.5">Tambahkan roti favoritmu!</p>
                </div>
            </div>
                <div id="cart-sidebar-footer" class="border-t border-white/30 px-5 py-4 space-y-3 bg-white/40 hidden">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-stone-500">Subtotal</span>
                    <span id="cart-sidebar-subtotal" class="font-semibold text-stone-800">Rp 0</span>
                </div>
                <button onclick="closeCart(); openCheckoutModal()" class="block w-full rounded-xl bg-theme-gradient-r px-4 py-3 text-sm font-bold text-white text-center shadow-lg hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">
                    Lanjut ke Checkout
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Checkout Modal --}}
<div id="checkout-overlay" class="fixed inset-0 z-[70] bg-stone-900/20 backdrop-blur-md hidden opacity-0"></div>
<div id="checkout-modal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 hidden">
    <div id="checkout-modal-inner" class="w-full max-w-lg glass-strong rounded-2xl shadow-2xl hidden">
        <div class="flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-stone-200/60">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10">
                        <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Konfirmasi Pesanan</h3>
                        <p class="text-[11px] text-stone-400">Periksa pesanan sebelum dikonfirmasi</p>
                    </div>
                </div>
                <button onclick="closeCheckoutModal()" class="flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="overflow-y-auto px-5 py-4 space-y-4 scrollbar-thin">
                {{-- Auto-generated Order Number --}}
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Nomor Order</label>
                    <div class="flex items-center gap-2 rounded-xl border border-stone-200 bg-stone-50/50 px-4 py-2.5">
                        <svg class="h-4 w-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                        <span id="checkout-order-number" class="text-sm font-mono font-bold text-theme-primary"><span id="order-num-display">...</span></span>
                    </div>
                </div>

                {{-- Cart Items Summary --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-medium text-stone-500">Pesanan</label>
                        <span id="checkout-item-count" class="text-[11px] text-stone-400"></span>
                    </div>
                    <div id="checkout-items" class="space-y-1.5"></div>
                    <div id="checkout-empty" class="hidden text-center py-6 text-stone-400 text-xs">Keranjang kosong</div>
                </div>

                {{-- Voucher --}}
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1">Kode Voucher (opsional)</label>
                    <input type="text" id="checkout-voucher" placeholder="Masukkan kode voucher" maxlength="50" inputmode="text" autocapitalize="characters" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
                </div>

                {{-- Phone & Notes --}}
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1">No. Telepon <span class="text-rose-400">*</span></label>
                        <x-ui.onscreen-keyboard name="customer_phone" mode="phone" input-id="checkout-phone" placeholder="08xxxxxxxxxx" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-stone-500 mb-1" for="checkout-notes">Catatan</label>
                        <textarea id="checkout-notes" rows="2" inputmode="text" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white resize-none" placeholder="Misal: sertakan piring, titip pesan, dll"></textarea>
                    </div>
                </div>

                {{-- Price Summary --}}
                <div class="rounded-xl bg-stone-50/70 p-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Subtotal</span>
                        <span id="checkout-subtotal" class="font-semibold text-stone-800">Rp 0</span>
                    </div>
                    <div id="checkout-discount-row" class="hidden flex justify-between text-sm">
                        <span class="text-stone-500">Diskon Voucher</span>
                        <span id="checkout-discount" class="font-semibold text-emerald-600">-Rp 0</span>
                    </div>
                    <hr class="border-stone-200">
                    <div class="flex justify-between text-base">
                        <span class="font-bold text-stone-900">Total</span>
                        <span id="checkout-total" class="font-bold text-theme-primary">Rp 0</span>
                    </div>
                </div>
            </div>
            <div class="border-t border-stone-200/60 px-5 py-4 space-y-2">
                <button onclick="submitCheckout()" id="checkout-submit-btn" class="w-full rounded-xl bg-theme-gradient-r px-4 py-3 text-sm font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span id="checkout-btn-label">Konfirmasi Pesanan</span>
                    <span id="checkout-btn-spinner" class="hidden"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Voucher Error Popup --}}
<div id="voucher-error-overlay" class="fixed inset-0 z-[80] bg-stone-900/20 backdrop-blur-md hidden opacity-0"></div>
<div id="voucher-error-modal" class="fixed inset-0 z-[80] flex items-center justify-center p-4 hidden">
    <div id="voucher-error-inner" class="w-full max-w-sm bg-white rounded-2xl shadow-2xl animate-scaleIn p-6 text-center">
        <div class="flex justify-center mb-3">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
                <svg class="h-7 w-7 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            </div>
        </div>
        <h3 class="text-base font-bold text-stone-900 mb-1">Voucher Tidak Valid</h3>
        <p id="voucher-error-msg" class="text-sm text-stone-500 mb-5">Kode voucher yang Anda masukkan tidak valid atau sudah tidak berlaku.</p>
        <button onclick="closeVoucherError()" class="w-full rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90 active:scale-[0.98] transition-all cursor-pointer">Tutup</button>
    </div>
</div>

<div id="cart-floating" class="fixed bottom-5 right-5 z-40 hidden">
    <button id="cart-fab-btn" class="flex h-12 w-12 items-center justify-center rounded-full bg-theme-gradient text-white shadow-xl shadow-theme-shadow hover:scale-105 active:scale-95 transition-all animate-pulseRing">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
        <span id="cart-fab-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-rose-500 text-[10px] font-bold text-white px-1 shadow-md">0</span>
    </button>
</div>

<div id="toast-container" class="fixed bottom-16 right-5 sm:bottom-16 sm:right-6 z-[60] space-y-1.5 pointer-events-none"></div>

<script>document.addEventListener('alpine:init', function () {
    initPublicCatalog({
        batchUrl: '{{ url('/api/products/batch') }}',
        isOnline: {{ $isOnline ? 'true' : 'false' }},
        previewOrderNumber: '{{ $previewOrderNumber }}',
        storeUrl: '{{ route('orders.public-store') }}',
        branchId: {{ $branch->id }},
        baseUrl: '{{ url('') }}',
        catalogUrl: '{{ route('orders.public-catalog', ['branch_id' => $branch->id]) }}',
        checkVoucherUrl: '/orders/public/check-voucher',
    });
});</script>
@endsection
