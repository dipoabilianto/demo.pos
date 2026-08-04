@extends($public ? 'layouts.public' : (auth()->check() ? 'layouts.app' : 'layouts.public'))
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $settings = $settings::getSettings(); @endphp
@section('title', 'Checkout')
@section('content')
<style>
    @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    @keyframes shimmer{0%{background-position:-200% 0}100%{background-position:200% 0}}
    @keyframes pulseDot{0%,80%,100%{transform:scale(.6);opacity:.4}40%{transform:scale(1);opacity:1}}
    .anim-fadeUp{animation:fadeUp .5s ease-out both}
    .shimmer{background:linear-gradient(90deg,#f5f5f4 25%,#e7e5e4 50%,#f5f5f4 75%);background-size:200% 100%;animation:shimmer 1.4s infinite}
    .pulse-dot{width:6px;height:6px;border-radius:50%;background:#d97706;animation:pulseDot 1.4s ease-in-out infinite both}
    input.error,textarea.error{border-color:#e11d48!important}
    input.error:focus,textarea.error:focus{box-shadow:0 0 0 3px rgba(225,29,72,.12)!important}
    .item-appear{animation:fadeUp .35s ease-out both}
    .item-remove{animation:fadeIn .2s ease-out reverse both}
    .step-circle{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;transition:all .3s;flex-shrink:0}
    .step-line{flex:1;height:2px;transition:all .3s}
    .qty-btn{width:28px;height:28px;border-radius:8px;font-size:15px;font-weight:700;transition:all .15s;cursor:pointer;border:none;line-height:1;color:#57534e;background:#e7e5e4;display:flex;align-items:center;justify-content:center}
    .qty-btn:hover{background:#d6d3d1}
    .qty-btn:active{transform:scale(.9)}
    .qty-btn.add{background:#fef3c7;color:#d97706}
    .qty-btn.add:hover{background:#fde68a}
    .scrollbar-thin{scrollbar-width:thin;scrollbar-color:#d6d3d1 transparent}
    .checkout-wrap{max-width:840px;margin:0 auto}
    @media(max-width:640px){.hide-mobile{display:none!important}}
</style>

@if ($public)

<div class="min-h-[80vh] flex items-start justify-center py-6 sm:py-10 px-4">
    <div class="w-full max-w-lg bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl anim-fadeUp">
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
            <a href="{{ route('orders.public-catalog.default') }}" class="flex h-7 w-7 items-center justify-center rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
        </div>
        <form id="checkoutForm" novalidate class="px-5 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium text-stone-500 mb-1">Nomor Order</label>
                <div class="flex items-center gap-2 rounded-xl border border-stone-200 bg-stone-50/50 px-4 py-2.5">
                    <svg class="h-4 w-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    <span id="checkout-order-number" class="text-sm font-mono font-bold text-theme-primary"><span id="order-num-display">...</span></span>
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-medium text-stone-500">Pesanan</label>
                    <span id="itemCount" class="text-[11px] text-stone-400"></span>
                </div>
                <div id="cartSkeleton" class="space-y-1.5">
                    <div class="shimmer h-[60px] rounded-xl"></div>
                    <div class="shimmer h-[60px] rounded-xl"></div>
                </div>
                <div id="cartItems" class="hidden space-y-1.5"></div>
                <div id="cartEmpty" class="hidden flex flex-col items-center py-8 text-stone-400">
                    <svg class="h-10 w-10 mb-2 text-stone-300" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    <p class="text-sm font-medium text-stone-500">Keranjang masih kosong</p>
                    <a href="{{ route('orders.public-catalog.default') }}" class="mt-3 text-xs text-theme-primary underline underline-offset-2">Kembali ke Katalog</a>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-stone-500 mb-1">Kode Voucher (opsional)</label>
                <input type="text" id="voucherInput" placeholder="VCH-XXXXXXXX" maxlength="50" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white uppercase">
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1" for="customer_phone">No. Telepon <span class="text-rose-400">*</span></label>
                    <input type="tel" id="customer_phone" autocomplete="tel" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-500 mb-1" for="notes">Catatan</label>
                    <textarea id="notes" rows="2" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 text-sm shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white resize-none" placeholder="Misal: sertakan piring, titip pesan, dll"></textarea>
                </div>
            </div>
            <div class="rounded-xl bg-stone-50/70 p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-stone-500">Subtotal</span>
                    <span id="subtotalDisplay" class="font-semibold text-stone-800">Rp 0</span>
                </div>
                <hr class="border-stone-200">
                <div class="flex justify-between text-base">
                    <span class="font-bold text-stone-900">Total</span>
                    <span id="totalDisplay" class="font-bold text-theme-primary">Rp 0</span>
                </div>
            </div>
            <button type="submit" id="submitBtn" class="w-full rounded-xl bg-theme-gradient-r px-4 py-3 text-sm font-bold text-white shadow-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <span id="btnLabel">Konfirmasi Pesanan</span>
                <span id="btnSpinner" class="hidden"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></span>
            </button>
        </form>
    </div>
</div>

@else

<div class="checkout-wrap">
    <div class="anim-fadeUp max-w-xl mx-auto mb-6 sm:mb-8">
        <div class="flex items-center gap-2 sm:gap-3">
            <div class="step-circle bg-theme-primary text-white">1</div>
            <span class="text-sm font-semibold text-theme-primary">Pilih Roti</span>
            <div class="step-line bg-amber-300"></div>
            <div class="step-circle bg-theme-primary text-white">2</div>
            <span class="text-sm font-semibold text-stone-800">Checkout</span>
            <div class="step-line bg-stone-200"></div>
            <div class="step-circle bg-stone-200 text-stone-400">3</div>
            <span class="text-sm font-medium text-stone-400 hide-mobile">Bayar</span>
        </div>
    </div>

    <form id="checkoutForm" novalidate class="space-y-6">
        <section class="anim-fadeUp rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/60" style="animation-delay:.05s">
            <div class="flex items-center gap-3 pb-4 border-b border-stone-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-stone-900">Pesanan</h3>
                <span id="itemCount" class="ml-auto text-xs text-stone-400 font-medium"></span>
            </div>
            <div id="cartSkeleton" class="space-y-2">
                <div class="shimmer h-[66px] rounded-xl"></div>
                <div class="shimmer h-[66px] rounded-xl"></div>
            </div>
            <div id="cartItems" class="hidden space-y-2"></div>
            <div id="cartEmpty" class="hidden flex flex-col items-center py-12 text-stone-400">
                <div class="w-16 h-16 rounded-2xl bg-stone-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-stone-500">Keranjang masih kosong</p>
                <p class="text-xs text-stone-400 mt-1">Yuk tambah roti favorit dulu!</p>
                <a href="{{ route('orders.catalog') }}" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-theme-gradient-r px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-[.97] transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Lihat Katalog
                </a>
            </div>
        </section>

        <section class="anim-fadeUp rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/60" style="animation-delay:.1s">
            <div class="flex items-center gap-3 pb-4 border-b border-stone-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-stone-900">Data Pemesan</h3>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5" for="customer_name">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="customer_name" required autocomplete="name" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="Budi Santoso">
                    <p class="text-xs text-rose-500 mt-1 hidden" id="err-name">Nama lengkap harus diisi</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5" for="customer_phone">No. Telepon</label>
                    <input type="tel" id="customer_phone" autocomplete="tel" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white" placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1.5" for="notes">Catatan</label>
                    <textarea id="notes" rows="2" class="block w-full rounded-xl border-stone-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white resize-none" placeholder="Misal: sertakan piring, titip pesan, jangan terlalu manis, dll"></textarea>
                </div>
            </div>
        </section>

        <section class="anim-fadeUp rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/60" style="animation-delay:.15s">
            <div class="flex items-center gap-3 pb-4 border-b border-stone-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-stone-900">Ringkasan Harga</h3>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-sm text-stone-600">Subtotal</span>
                    <span id="subtotalDisplay" class="text-sm font-semibold text-stone-800">Rp 0</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-sm text-stone-600">Diskon</span>
                    <div class="relative w-40">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-stone-400 font-medium">Rp</span>
                        <input type="number" id="discountInput" min="0" value="0" class="block w-full rounded-lg border-stone-200 pl-8 pr-3 py-1.5 text-sm text-right font-semibold text-stone-800 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 bg-white">
                    </div>
                </div>
            </div>
            <hr class="border-stone-100 my-4">
            <div class="flex justify-between items-center">
                <span class="text-base font-bold text-stone-900">Total</span>
                <span id="totalDisplay" class="text-xl font-extrabold text-theme-primary">Rp 0</span>
            </div>
        </section>

        <div class="anim-fadeUp" style="animation-delay:.2s">
            <button type="submit" id="submitBtn" class="w-full py-3.5 rounded-2xl bg-theme-gradient text-white font-bold text-sm tracking-wide shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow hover:opacity-90 active:scale-[.98] transition-all duration-200 flex items-center justify-center gap-2.5">
                <span id="btnLabel">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Buat Pesanan Sekarang
                    </span>
                </span>
                <span id="btnSpinner" class="hidden flex items-center gap-1">
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                    <span class="pulse-dot"></span>
                </span>
            </button>
            <div class="mt-3 flex items-center justify-center gap-4 text-[10px] text-stone-400">
                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg> Data aman &amp; terenkripsi</span>
                <span class="flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Konfirmasi setelah bayar</span>
            </div>
        </div>
    </form>

    <div class="text-center py-6 text-xs text-stone-400">
        <a href="{{ route('orders.catalog') }}" class="hover:text-stone-600 transition-colors">&larr; Kembali ke Katalog</a>
    </div>
</div>

@endif
@endsection

@push('scripts')
<script>
(function () {
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function(e) {
        history.pushState(null, '', location.href);
        alert('Selesaikan transaksi terlebih dahulu!');
    });
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    function getCookie(name) {
        const m = document.cookie.match(new RegExp('(?:^|;)\\s*' + name + '\\s*=\\s*([^;]+)'));
        return m ? decodeURIComponent(m[1]) : null;
    }
    function setCookie(name, value, days) {
        const d = new Date(); d.setTime(d.getTime() + days * 864e5);
        document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; expires=' + d.toUTCString();
    }
    function fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }
    const isPublic = {{ $public ? 'true' : 'false' }};
    const $ = id => document.getElementById(id);
    const cartSkeleton = $('cartSkeleton');
    const cartItemsEl = $('cartItems');
    const cartEmptyEl = $('cartEmpty');
    const subtotalD = $('subtotalDisplay');
    const totalD = $('totalDisplay');
    const discountInput = $('discountInput');
    const voucherInput = $('voucherInput');
    const submitBtn = $('submitBtn');
    const btnLabel = $('btnLabel');
    const btnSpinner = $('btnSpinner');
    const form = $('checkoutForm');
    const itemCount = $('itemCount');
    const errName = $('err-name');
    const nameInput = $('customer_name');
    const orderNumDisplay = $('order-num-display');
    let products = [], cartMap = {};
    function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    async function loadCart() {
        try {
            const raw = getCookie('cart');
            if (!raw) { showEmpty(); return; }
            cartMap = JSON.parse(raw);
            const ids = Object.keys(cartMap).map(Number).filter(id => id > 0);
            if (ids.length === 0) { showEmpty(); return; }
            const resp = await fetch('{{ url('/api/products/batch') }}?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } });
            if (!resp.ok) throw new Error('Gagal memuat');
            const data = await resp.json();
            products = Array.isArray(data) ? data : [];
            renderCart();
        } catch (e) {
            cartSkeleton.classList.add('hidden');
            cartEmptyEl.classList.remove('hidden');
            cartEmptyEl.innerHTML = `<div class="w-16 h-16 rounded-2xl bg-rose-50 flex items-center justify-center mb-4"><svg class="w-8 h-8 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg></div><p class="text-sm font-medium text-rose-500">Gagal memuat keranjang</p><button onclick="location.reload()" class="mt-3 text-xs text-theme-primary underline underline-offset-2">Muat ulang halaman</button>`;
        }
    }
    function showEmpty() {
        cartSkeleton.classList.add('hidden');
        cartItemsEl.classList.add('hidden');
        cartEmptyEl.classList.remove('hidden');
        updatePrice(0);
        itemCount.textContent = '';
    }
    function renderCart() {
        cartSkeleton.classList.add('hidden');
        cartItemsEl.classList.remove('hidden');
        cartItemsEl.innerHTML = '';
        let subtotal = 0, anyOos = false, totalItems = 0;
        products.forEach((p, i) => {
            const qty = cartMap[p.id] || 0; if (qty <= 0) return;
            totalItems += qty;
            const effectivePrice = p.sale_price ?? p.price;
            const lineTotal = effectivePrice * qty;
            subtotal += lineTotal;
            const oos = !p.is_unlimited && p.stock !== undefined && p.stock < qty;
            if (oos) anyOos = true;
            const colors = ['#fef3c7','#fce7f3','#dbeafe','#d1fae5','#ede9fe','#ffedd5'];
            const bg = colors[p.id % colors.length];
            const div = document.createElement('div');
            div.className = 'item-appear flex items-center gap-3 p-3 rounded-xl ' + (oos ? 'bg-rose-50 border border-rose-200' : 'bg-stone-50/70 border border-stone-100');
            div.id = 'cart-item-' + p.id;
            div.style.animationDelay = (i * 0.06) + 's';
            div.innerHTML = `
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:${bg}">
                    <svg class="w-5.5 h-5.5" style="color:${oos ? '#e11d48' : '#d97706'}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.556C6.59 7.475 6 8.438 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-stone-900 truncate">${esc(p.name)}</p>
                    <p class="text-xs text-stone-400 mt-0.5">${p.sale_price ? '<span class="line-through">' + fmt(p.price) + '</span> ' + fmt(p.sale_price) : fmt(p.price)}</p>
                    ${oos ? '<p class="text-xs text-rose-500 font-medium mt-0.5">Stok tersedia: ' + p.stock + '</p>' : ''}
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="button" onclick="window.cartUpdate(${p.id},-1)" class="qty-btn">−</button>
                    <span class="w-7 text-center text-sm font-bold text-stone-900" id="qty-${p.id}">${qty}</span>
                    <button type="button" onclick="window.cartUpdate(${p.id},1)" class="qty-btn add">+</button>
                </div>
                <p class="text-sm font-bold text-stone-800 w-[72px] text-right shrink-0">${fmt(effectivePrice * qty)}</p>`;
            cartItemsEl.appendChild(div);
        });
        itemCount.textContent = totalItems > 0 ? totalItems + ' item' : '';
        if (subtotal === 0) { showEmpty(); return; }
        updatePrice(subtotal);
        submitBtn.disabled = anyOos;
        btnLabel.innerHTML = anyOos
            ? '<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg> Stok tidak mencukupi</span>'
            : '<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Buat Pesanan Sekarang</span>';
    }

    window.cartUpdate = function(id, delta) {
        const cur = cartMap[id] || 0;
        const next = cur + delta;
        const p = products.find(p => p.id === id);
        if (delta > 0 && p && !p.is_unlimited && p.stock !== undefined && p.stock < next) { alert('Stok ' + p.name + ' hanya tersedia ' + p.stock + '.'); return; }
        if (next <= 0) { delete cartMap[id]; const el = document.getElementById('cart-item-' + id); if (el) { el.classList.add('item-remove'); setTimeout(() => el.remove(), 200); } }
        else { cartMap[id] = next; const el = document.getElementById('qty-' + id); if (el) el.textContent = next; }
        const d = new Date(); d.setTime(d.getTime() + 7 * 864e5);
        document.cookie = 'cart=' + encodeURIComponent(JSON.stringify(cartMap)) + '; path=/; expires=' + d.toUTCString();
        recalcTotal();
    };
    function recalcTotal() {
        let subtotal = 0, totalItems = 0;
        products.forEach(p => { const qty = cartMap[p.id] || 0; if (qty <= 0) return; totalItems += qty; subtotal += (p.sale_price ?? p.price) * qty; });
        itemCount.textContent = totalItems > 0 ? totalItems + ' item' : '';
        updatePrice(subtotal);
        if (totalItems === 0) { cartItemsEl.classList.add('hidden'); cartEmptyEl.classList.remove('hidden'); }
    }
    function updatePrice(subtotal) {
        const discount = Math.min(parseInt(discountInput?.value) || 0, subtotal);
        const total = subtotal - discount;
        subtotalD.textContent = fmt(subtotal);
        totalD.textContent = fmt(total);
    }
    discountInput?.addEventListener('input', function() {
        updatePrice(getCurrentSubtotal());
    });
    function getCurrentSubtotal() {
        let s = 0;
        products.forEach(p => { const qty = cartMap[p.id] || 0; if (qty <= 0) return; s += (p.sale_price ?? p.price) * qty; });
        return s;
    }
    form.addEventListener('submit', async function (e) {
        e.preventDefault(); if (submitBtn.disabled) return;
        const items = [];
        products.forEach(p => { const qty = cartMap[p.id] || 0; if (qty > 0 && (p.is_unlimited || p.stock === undefined || p.stock >= qty)) { items.push({ product_id: p.id, quantity: qty }); } });
        if (items.length === 0) { alert('Tidak ada item yang dapat diproses.'); return; }

        let customer_name, customer_phone, notes;
        customer_phone = $('customer_phone')?.value.trim() || '';
        notes = $('notes')?.value.trim() || '';

        if (isPublic) {
            if (!customer_phone) { $('customer_phone')?.focus(); alert('No. Telepon harus diisi!'); return; }
            customer_name = 'Pembeli';
        } else {
            if (nameInput) { nameInput.classList.remove('error'); }
            if (errName) { errName.classList.add('hidden'); }
            customer_name = nameInput?.value.trim() || '';
            if (!customer_name) { nameInput?.classList.add('error'); errName?.classList.remove('hidden'); nameInput?.focus(); nameInput?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
        }

        submitBtn.disabled = true; btnLabel.classList.add('hidden'); btnSpinner.classList.remove('hidden');
        try {
            const url = isPublic ? '{{ route('orders.public-store') }}' : '{{ route('orders.store') }}';
            const body = { customer_name, customer_phone, notes, items };
            if (!isPublic) {
                body.discount = parseInt(discountInput?.value) || 0;
                const editingOrderId = getCookie('cart_order_id');
                if (editingOrderId) body.order_id = parseInt(editingOrderId);
            } else {
                const vc = (voucherInput?.value || '').trim();
                if (vc) body.voucher_code = vc;
            }
            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const result = await resp.json();
            if (!resp.ok) throw new Error(result.error || 'Terjadi kesalahan.');
            setCookie('cart', '{}', -1);
            setCookie('cart_order_id', '', -1);
            window.location.href = result.redirect || (isPublic ? '{{ route('orders.public-catalog.default') }}' : '{{ route('orders.catalog') }}');
        } catch (err) { alert(err.message || 'Gagal membuat pesanan.'); submitBtn.disabled = false; btnLabel.classList.remove('hidden'); btnSpinner.classList.add('hidden'); }
    });

    if (orderNumDisplay) {
        orderNumDisplay.textContent = '{{ $previewOrderNumber }}';
    }

    loadCart();
})();
</script>
@endpush