@extends('layouts.app')
@section('title', $settings['catalog_title'] ?? 'Pesan Roti')
@section('content')
<style>
    @keyframes fadeInUp { from { opacity:0;transform:translateY(20px); } to { opacity:1;transform:translateY(0); } }
    @keyframes slideCart { from { transform:translateX(100%); } to { transform:translateX(0); } }
    @keyframes slideCartOut { from { transform:translateX(0); } to { transform:translateX(100%); } }
    @keyframes toastIn { from { opacity:0;transform:translateY(12px) scale(0.95); } to { opacity:1;transform:translateY(0) scale(1); } }
    @keyframes toastOut { from { opacity:1;transform:translateY(0) scale(1); } to { opacity:0;transform:translateY(12px) scale(0.95); } }
    .animate-fadeUp { animation:fadeInUp .5s ease-out both; }
    .animate-slideCart { animation:slideCart .3s cubic-bezier(.22,1,.36,1) both; }
    .animate-slideCartOut { animation:slideCartOut .3s cubic-bezier(.22,1,.36,1) both; }
    .animate-toastIn { animation:toastIn .35s cubic-bezier(.22,1,.36,1) both; }
    .animate-toastOut { animation:toastOut .25s ease-in both; }
    .catalog-main { margin: -1.5rem -2rem; padding: 0; flex: 1; min-height: 0; display: flex; flex-direction: column; }
    @media (max-width:640px) { .catalog-main { margin: -1.5rem -1rem; } }
    .cart-scroll { scrollbar-width:thin; scrollbar-color:#d6d3d1 transparent; }
    .cart-scroll::-webkit-scrollbar { width:4px; }
    .cart-scroll::-webkit-scrollbar-thumb { background:#d6d3d1; border-radius:4px; }
</style>

<div x-data="{ cartOpen: false, editingOrderId: null, customerName: '', customerPhone: '', notes: '' }">
<div class="catalog-main">
    @php $catNames = $products->keys()->toArray(); @endphp
    <div class="shrink-0 z-30 bg-white/80 backdrop-blur-md border-b border-stone-100 shadow-sm">
        <div class="mx-auto px-2 sm:px-3 py-1.5">
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-thin">
                <button data-cat="all" class="cat-pill active rounded-full px-2.5 py-1 text-[11px] font-semibold bg-theme-primary text-white shadow-theme-shadow transition-all shrink-0">Semua</button>
                @foreach ($catNames as $cat)
                    <button data-cat="{{ Str::slug($cat) }}" class="cat-pill rounded-full px-2.5 py-1 text-[11px] font-medium text-stone-500 bg-stone-100 hover:bg-stone-200 hover:text-stone-800 transition-all shrink-0">{{ $cat }}</button>
                @endforeach
                <div class="flex-1 min-w-0"></div>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-2 top-1/2 -translate-y-1/2 h-3 w-3 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input id="search-input" type="text" placeholder="Cari..." autocomplete="off" class="w-28 sm:w-36 rounded-md border-stone-200 bg-stone-50 px-2 py-1 pl-6 text-[11px] focus:border-theme-primary focus:ring-theme-primary/20 focus:bg-white transition-all">
                    <div id="search-results" class="hidden absolute top-full right-0 z-50 mt-1 w-64 rounded-xl bg-white shadow-xl border border-stone-200 overflow-hidden max-h-80 overflow-y-auto"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 min-h-0 flex">
        {{-- Products --}}
        <main class="flex-1 min-w-0 max-w-full lg:max-w-[calc(100%-24rem)] xl:max-w-[calc(100%-28rem)] mx-auto px-3 sm:px-4 py-4 sm:py-6 overflow-y-auto">
            @forelse ($products as $category => $items)
                <section class="cat-section mb-4" data-cat="{{ Str::slug($category) }}">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="h-px flex-1 bg-gradient-to-r from-theme-primary/20 to-transparent"></div>
                        <h2 class="text-xs font-bold text-stone-800 flex items-center gap-1">
                            <svg class="h-3 w-3 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 21a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0z" /></svg>
                            {{ $category }}
                        </h2>
                        <div class="h-px flex-1 bg-gradient-to-l from-theme-primary/20 to-transparent"></div>
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-1.5 sm:gap-2">
                        @foreach ($items as $product)
                            <div class="product-card group bg-white rounded-xl shadow-sm ring-1 ring-stone-100 hover:ring-1 hover:ring-theme-primary/30 hover:shadow-md hover:shadow-theme-shadow transition-all duration-200" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->sale_price ?? $product->price }}" data-stock="{{ $product->stock }}" data-unlimited="{{ $product->is_unlimited ? 'true' : 'false' }}" data-sold-out="{{ $product->is_sold_out ? 'true' : 'false' }}" style="animation:fadeInUp .4s ease-out both;animation-delay:{{ $loop->index * 0.03 }}s">
                                <div class="aspect-[4/3] bg-gradient-to-br from-theme-primary/5 to-stone-100 flex items-center justify-center p-2 rounded-t-xl relative overflow-hidden {{ $product->is_sold_out ? 'opacity-50' : '' }}">
                                    @if ($product->is_sold_out)
                                        <div class="absolute inset-0 bg-stone-900/40 flex items-center justify-center z-10">
                                            <span class="rotate-[-12deg] text-xs font-black tracking-widest text-white drop-shadow-lg">SOLD</span>
                                        </div>
                                    @endif
                                    @if ($product->isUnlimited() && !$product->is_sold_out)
                                        <span class="absolute top-1 right-1 inline-flex items-center gap-0.5 rounded-full bg-sky-50/90 backdrop-blur-sm px-1 py-0.5 text-[8px] font-semibold text-sky-600 ring-1 ring-sky-200/30">∞</span>
                                    @elseif ($product->isLowStock())
                                        <span class="absolute top-1 right-1 inline-flex items-center gap-0.5 rounded-full bg-rose-50/90 backdrop-blur-sm px-1 py-0.5 text-[8px] font-semibold text-rose-600 ring-1 ring-rose-200/30"><span class="h-1 w-1 rounded-full bg-rose-500 animate-pulse"></span>{{ $product->stock }}</span>
                                    @endif
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="400" height="400" loading="lazy" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500 cursor-pointer" onclick="addToCart({{ $product->id }})">
                                    @else
                                        <svg class="h-8 w-8 text-theme-primary/20 group-hover:text-theme-primary/40 transition-all duration-500 group-hover:scale-105 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" onclick="addToCart({{ $product->id }})"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.056C6.59 7.475 6 8.938 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                                    @endif
                                </div>
                                <div class="p-1.5 sm:p-2">
                                    <div class="flex items-start justify-between gap-0.5">
                                        <h3 class="text-[10px] sm:text-xs font-semibold text-stone-900 leading-tight group-hover:text-theme-primary transition-colors {{ $product->is_sold_out ? 'line-through text-stone-400' : '' }}">{{ $product->name }}</h3>
                                        <button onclick="toggleSoldOut({{ $product->id }})" class="shrink-0 rounded p-0.5 text-stone-300 hover:text-{{ $product->is_sold_out ? 'emerald' : 'rose' }}-500 hover:bg-stone-100 transition-all" title="{{ $product->is_sold_out ? 'Tandai Tersedia' : 'Tandai Sold Out' }}">
                                            @if ($product->is_sold_out)
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @else
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @endif
                                        </button>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between gap-1">
                                        @if ($product->sale_price)
                                            <span class="text-[10px] font-medium text-warm-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span class="text-xs font-bold text-rose-600">Rp {{ number_format($product->sale_price, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-xs font-bold text-theme-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @endif
                                        <button onclick="addToCart({{ $product->id }})" class="add-to-cart-btn rounded-md {{ $product->is_sold_out ? 'bg-stone-300 cursor-not-allowed' : 'bg-theme-gradient-r' }} p-1 text-white shadow hover:shadow-md hover:opacity-90 active:scale-95 transition-all duration-150">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
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
                        <svg class="h-7 w-7 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.25v2.5c0 .777.845 1.74 1.976 1.834a30.633 30.633 0 00-1.614 4.481m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M6.362 17.25l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                    </div>
                    <p class="text-stone-500 text-sm font-medium">Belum ada roti tersedia</p>
                    <p class="text-stone-400 text-xs mt-0.5">Silakan cek kembali nanti!</p>
                </div>
            @endforelse
        </main>

        {{-- Cart Panel (Desktop) --}}
        <aside class="hidden lg:flex lg:flex-col w-96 xl:w-[28rem] border-l border-t border-stone-200/60 bg-stone-50/50">
            <div class="flex items-center justify-between px-4 pt-4 pb-3 border-b border-stone-200/60 bg-white">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10">
                        <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-stone-900">Keranjang</h3>
                        <p id="cart-sidebar-count" class="text-[11px] text-stone-400">0 item</p>
                    </div>
                </div>
            </div>

            <div id="cart-sidebar-items" class="flex-1 overflow-y-auto px-3 py-3 space-y-1.5 cart-scroll">
                <div class="flex flex-col items-center justify-center min-h-[160px] text-stone-300">
                    <svg class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    <p class="text-xs font-medium text-stone-400">Keranjang kosong</p>
                    <p class="text-[11px] text-stone-300 mt-0.5">Siap memulai transaksi selanjutnya!</p>
                </div>
            </div>

            <div id="cart-sidebar-footer" class="border-t border-stone-200/60 p-3 space-y-3 bg-white">
                <div class="space-y-2">
                    <input type="text" x-model="customerName" placeholder="Nama Pelanggan (opsional)" class="w-full rounded-lg border-stone-200 px-3 py-2 text-xs focus:border-theme-primary focus:ring-theme-primary/20">
                    <input type="text" x-model="customerPhone" placeholder="No. Telepon (opsional)" class="w-full rounded-lg border-stone-200 px-3 py-2 text-xs focus:border-theme-primary focus:ring-theme-primary/20">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-stone-500">Subtotal</span>
                    <span id="cart-sidebar-subtotal" class="font-bold text-stone-900">Rp 0</span>
                </div>

                <div class="flex gap-2">
                    <button id="save-order-btn" class="flex-1 rounded-lg ring-1 ring-stone-300 bg-white px-3 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition-all">Simpan Pesanan</button>
                    <a href="{{ route('orders.checkout') }}" class="flex-1 block text-center rounded-lg bg-theme-gradient-r px-3 py-2.5 text-xs font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.98] transition-all">Checkout</a>
                </div>
            </div>

            <div id="saved-orders-placeholder" class="px-3 py-2 text-xs border-t border-stone-200/60"></div>
        </aside>
    </div>
</div>

{{-- Mobile Cart Overlay --}}
<div id="cart-overlay-mobile" class="fixed inset-0 z-50 bg-stone-900/30 backdrop-blur-sm hidden opacity-0 lg:hidden"></div>
<div id="cart-sidebar-mobile" class="fixed top-0 right-0 z-50 h-full w-full sm:w-[380px] bg-white/90 backdrop-blur-2xl shadow-2xl hidden translate-x-full lg:hidden">
    <div class="flex h-full flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-stone-200/60">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10">
                    <svg class="h-4 w-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-stone-900">Keranjang</h3>
                    <p id="cart-sidebar-count-mobile" class="text-[11px] text-stone-400">0 item</p>
                </div>
            </div>
            <button id="cart-close-mobile" class="flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div id="cart-sidebar-items-mobile" class="flex-1 overflow-y-auto px-3 py-3 space-y-1.5 scrollbar-thin">
            <div class="flex flex-col items-center justify-center min-h-[160px] text-stone-300">
                <svg class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                <p class="text-xs font-medium text-stone-400">Keranjang kosong</p>
                <p class="text-[11px] text-stone-300 mt-0.5">Siap memulai transaksi selanjutnya!</p>
            </div>
        </div>
        <div id="cart-sidebar-footer-mobile" class="border-t border-stone-200/60 p-3 space-y-3 bg-white">
            <div class="space-y-2">
                <input type="text" x-model="customerName" placeholder="Nama Pelanggan (opsional)" class="w-full rounded-lg border-stone-200 px-3 py-2 text-xs focus:border-theme-primary focus:ring-theme-primary/20">
                <input type="text" x-model="customerPhone" placeholder="No. Telepon (opsional)" class="w-full rounded-lg border-stone-200 px-3 py-2 text-xs focus:border-theme-primary focus:ring-theme-primary/20">
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-stone-500">Subtotal</span>
                <span id="cart-sidebar-subtotal-mobile" class="font-bold text-stone-900">Rp 0</span>
            </div>
            <div class="flex gap-2">
                <button id="save-order-btn-mobile" class="flex-1 rounded-lg ring-1 ring-stone-300 bg-white px-3 py-2.5 text-xs font-semibold text-stone-700 hover:bg-stone-50 transition-all">Simpan Pesanan</button>
                <a href="{{ route('orders.checkout') }}" class="flex-1 block text-center rounded-lg bg-theme-gradient-r px-3 py-2.5 text-xs font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.98] transition-all">Checkout</a>
            </div>
        </div>
        <div id="saved-orders-placeholder-mobile" class="px-3 py-2 text-xs border-t border-stone-200/60"></div>
    </div>
</div>

<div class="lg:hidden fixed bottom-5 right-5 z-50">
    <button id="cart-toggle-mobile"
        class="flex h-12 w-12 items-center justify-center rounded-full bg-theme-gradient text-white shadow-xl shadow-theme-shadow hover:scale-105 active:scale-95 transition-all duration-200">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
        <span id="cart-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-[20px] rounded-full bg-rose-500 text-[10px] font-bold text-white px-1 shadow-md border-2 border-white">0</span>
    </button>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-[60] space-y-1.5 pointer-events-none"></div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    window.initAdminCatalog({
        baseUrl: '{{ url('') }}',
        batchUrl: '{{ url('/api/products/batch') }}',
        searchUrl: '{{ url('/api/products/search') }}',
        saveOrderUrl: '{{ route('orders.save') }}',
        savedListUrl: '{{ route('orders.saved-list') }}',
        checkoutUrl: '{{ route('orders.checkout') }}',
        csrf: CSRF,
    });
});
</script>
@endpush
