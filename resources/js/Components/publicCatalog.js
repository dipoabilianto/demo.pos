export function promoCarousel(total) {
    return {
        current: 0,
        total: total,
        timer: null,
        init() {
            this.resetTimer();
        },
        destroy() {
            clearInterval(this.timer);
        },
        resetTimer() {
            clearInterval(this.timer);
            if (this.total > 1) {
                this.timer = setInterval(() => this.advance(), 5000);
            }
        },
        advance() {
            this.current = (this.current + 1) % this.total;
        },
        next() {
            this.advance();
            this.resetTimer();
        },
        prev() {
            this.current = (this.current - 1 + this.total) % this.total;
            this.resetTimer();
        },
        go(i) {
            this.current = i;
            this.resetTimer();
        },
    };
}

function getCookie(name) {
    const val = `; ${document.cookie}`.split(`; ${name}=`);
    if (val.length === 2) return val.pop().split(';').shift();
    return null;
}

function setCookie(name, value, days = 7) {
    const d = new Date();
    d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value}; path=/; expires=${d.toUTCString()}`;
}

function cartCookieName() { return 'cart_public_' + config.branchId; }
function getCart() { try { return JSON.parse(getCookie(cartCookieName()) || '{}'); } catch { return {}; } }
function saveCart(cart) { setCookie(cartCookieName(), JSON.stringify(cart)); updateCartUI(cart); }

let sidebarLoading = false;
let sidebarPending = false;
let checkoutProducts = [];

function fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }

let config;

async function renderCartSidebar(cart) {
    const ids = Object.keys(cart);
    const container = document.getElementById('cart-sidebar-items');
    const footer = document.getElementById('cart-sidebar-footer');
    const countLabel = document.getElementById('cart-sidebar-count');
    const totalQty = ids.reduce((s, id) => s + cart[id], 0);
    countLabel.textContent = totalQty > 0 ? totalQty + ' item' : '0 item';
    if (ids.length === 0) {
        container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-stone-300"><svg class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg><p class="text-xs font-medium text-stone-400">Keranjang kosong</p><p class="text-[11px] text-stone-300 mt-0.5">Tambah roti favoritmu!</p></div>`;
        footer.classList.add('hidden');
        return;
    }
    footer.classList.remove('hidden');
    if (sidebarLoading) {
        sidebarPending = true;
        return;
    }
    sidebarLoading = true;
    try {
        const res = await fetch(config.batchUrl + '?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } });
        checkoutProducts = await res.json();
        let subtotal = 0;
        let html = '';
        ids.forEach((id, idx) => {
            const qty = cart[id];
            const p = checkoutProducts.find(p => p.id == id);
            if (!p) return;
            const effectivePrice = p.sale_price ?? p.price;
            const sub = effectivePrice * qty;
            subtotal += sub;
            html += `<div class="flex items-center gap-2.5 bg-white rounded-lg p-2.5 ring-1 ring-stone-100" style="animation:fadeInUp .3s ease-out both;animation-delay:${idx*0.04}s">
                <div class="shrink-0 w-10 h-10 rounded-lg bg-theme-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.556C6.59 7.475 6 8.438 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-stone-900 truncate">${p.name}</p>
                    <p class="text-[11px] text-stone-400">Rp ${effectivePrice.toLocaleString('id-ID')} × ${qty}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="updateQty(${id},-1)" class="flex h-6 w-6 items-center justify-center rounded-md bg-stone-100 text-stone-500 hover:bg-stone-200 transition-all text-xs font-bold">−</button>
                    <span class="w-5 text-center text-xs font-semibold text-stone-900">${qty}</span>
                    <button onclick="updateQty(${id},1)" class="flex h-6 w-6 items-center justify-center rounded-md bg-stone-100 text-stone-500 hover:bg-theme-primary/10 hover:text-theme-primary transition-all text-xs font-bold">+</button>
                </div>
            </div>`;
        });
        container.innerHTML = html;
        document.getElementById('cart-sidebar-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    } catch (e) {
        container.innerHTML = `<div class="flex flex-col items-center justify-center py-10 text-rose-500"><p class="text-xs">Gagal memuat keranjang</p></div>`;
    } finally {
        sidebarLoading = false;
        if (sidebarPending) {
            sidebarPending = false;
            renderCartSidebar(getCart());
        }
    }
}

function updateCartUI(cart) {
    const ids = Object.keys(cart);
    const totalQty = ids.reduce((s, id) => s + cart[id], 0);
    document.getElementById('cart-badge').textContent = totalQty;
    document.getElementById('cart-fab-badge').textContent = totalQty;
    document.getElementById('cart-floating').classList.toggle('hidden', totalQty === 0);
    renderCartSidebar(cart);
}

function initAddToCart() {
    window.updateQty = function(id, delta) {
        const cart = getCart();
        const current = cart[id] || 0;
        const next = current + delta;
        if (next <= 0) { delete cart[id]; } else { cart[id] = next; }
        saveCart(cart);
    };

    window.addToCart = function(productId, qty = 1) {
        const card = document.querySelector(`.product-card[data-id="${productId}"]`);
        const soldOut = card?.dataset?.soldOut === 'true';
        if (soldOut) {
            showToast('<svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', 'Menu sedang SOLD, tidak dapat ditambahkan!', 'rose');
            return;
        }
        if (!config.isOnline) {
            showToast('<svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', 'Pemesanan online sedang tutup!', 'rose');
            return;
        }
        const cart = getCart();
        const current = cart[productId] || 0;
        const next = current + qty;
        const unlimited = card?.dataset?.unlimited === 'true';
        const stock = parseInt(card?.dataset?.stock || 0);
        if (!unlimited && stock < next) {
            showToast('<svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', 'Stok hanya ' + stock + ', tidak mencukupi!', 'rose');
            return;
        }
        cart[productId] = next;
        saveCart(cart);
        showToast('<svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>', 'Ditambahkan ke keranjang!', 'emerald');
    };
}

function showToast(icon, msg, color = 'amber') {
    const colors = { amber: 'bg-theme-gradient-r shadow-theme-shadow', emerald: 'from-emerald-600 to-emerald-700 shadow-emerald-300/30', rose: 'from-rose-600 to-rose-700 shadow-rose-300/30' };
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `pointer-events-auto animate-toastIn flex items-center gap-2 rounded-lg bg-gradient-to-r ${colors[color]||colors.amber} px-3 py-2 text-xs font-medium text-white shadow-lg backdrop-blur-sm`;
    toast.innerHTML = `${icon}<span>${msg}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.className = toast.className.replace('animate-toastIn', 'animate-toastOut'); setTimeout(() => toast.remove(), 350); }, 2500);
}

function initCategoryFilters() {
    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.className = 'cat-pill rounded-full px-3 py-1.5 text-xs font-medium text-stone-500 bg-stone-100 hover:bg-stone-200 hover:text-stone-700 transition-all';
            });
            const target = this.dataset.cat;
            if (target === 'all') {
                this.className = 'cat-pill active rounded-full px-3 py-1.5 text-xs font-semibold bg-theme-primary text-white shadow shadow-theme-shadow transition-all';
                document.querySelectorAll('.cat-section').forEach(s => {
                    s.classList.remove('sliding-out');
                    s.classList.add('sliding-in');
                    s.style.display = '';
                });
            } else {
                this.className = 'cat-pill category-pill-active active rounded-full px-3 py-1.5 text-xs font-bold bg-theme-primary/10 text-theme-primary ring-2 ring-theme-primary/30 transition-all';
                document.querySelectorAll('.cat-section').forEach(s => {
                    if (s.dataset.cat === target) {
                        s.classList.remove('sliding-out');
                        s.classList.add('sliding-in');
                        s.style.display = '';
                    } else {
                        s.classList.remove('sliding-in');
                        s.classList.add('sliding-out');
                        setTimeout(() => {
                            if (s.classList.contains('sliding-out')) {
                                s.style.display = 'none';
                            }
                        }, 250);
                    }
                });
            }
        });
    });
}

function initBackPrevention() {
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function (e) {
        if (Object.keys(getCart()).length > 0) {
            history.pushState(null, '', location.href);
            showToast('<svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', 'Selesaikan transaksi terlebih dahulu!', 'rose');
        }
    });
}

function initCartModal() {
    const modal = document.getElementById('cart-modal');
    const overlay = document.getElementById('cart-overlay');

    window.openCart = function () {
        overlay.classList.remove('hidden', 'opacity-0');
        modal.classList.remove('hidden');
        const inner = document.getElementById('cart-modal-inner');
        inner.classList.remove('hidden');
        inner.className = 'w-full max-w-lg bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl animate-scaleIn';
        renderCartSidebar(getCart());
    };

    function closeCart() {
        const inner = document.getElementById('cart-modal-inner');
        inner.className = 'w-full max-w-lg bg-white/95 backdrop-blur-2xl rounded-2xl shadow-2xl animate-scaleOut';
        overlay.classList.add('opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); inner.classList.add('hidden'); overlay.classList.add('hidden'); }, 300);
    }
    window.closeCart = closeCart;

    document.getElementById('cart-toggle-btn').addEventListener('click', openCart);
    document.getElementById('cart-fab-btn').addEventListener('click', openCart);
    document.getElementById('cart-close-btn').addEventListener('click', closeCart);
    overlay.addEventListener('click', closeCart);
}

function initCheckoutModal() {
    const chkModal = document.getElementById('checkout-modal');
    const chkOverlay = document.getElementById('checkout-overlay');

    async function renderCheckoutItems() {
        const cart = getCart();
        const ids = Object.keys(cart);
        const itemsEl = document.getElementById('checkout-items');
        const emptyEl = document.getElementById('checkout-empty');
        const countEl = document.getElementById('checkout-item-count');
        const subtotalEl = document.getElementById('checkout-subtotal');
        const totalEl = document.getElementById('checkout-total');

        if (ids.length === 0) {
            itemsEl.classList.add('hidden');
            emptyEl.textContent = 'Keranjang kosong';
            emptyEl.classList.remove('hidden');
            subtotalEl.textContent = fmt(0);
            totalEl.textContent = fmt(0);
            countEl.textContent = '';
            return;
        }

        // checkoutProducts is populated by the cart sidebar's own async
        // fetch, which may not have resolved (or run at all) by the time
        // checkout opens — always refetch here so this modal reflects the
        // current cart instead of a stale or empty product list.
        try {
            const res = await fetch(config.batchUrl + '?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } });
            checkoutProducts = await res.json();
        } catch (e) {
            itemsEl.classList.add('hidden');
            emptyEl.textContent = 'Gagal memuat keranjang';
            emptyEl.classList.remove('hidden');
            return;
        }

        if (checkoutProducts.length === 0) {
            itemsEl.classList.add('hidden');
            emptyEl.textContent = 'Keranjang kosong';
            emptyEl.classList.remove('hidden');
            subtotalEl.textContent = fmt(0);
            totalEl.textContent = fmt(0);
            countEl.textContent = '';
            return;
        }
        itemsEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');

        let subtotal = 0, totalQty = 0, html = '';
        ids.forEach((id, idx) => {
            const qty = cart[id];
            const p = checkoutProducts.find(r => r.id == id);
            if (!p) return;
            const price = p.sale_price ?? p.price;
            subtotal += price * qty;
            totalQty += qty;
            html += `<div class="flex items-center gap-2.5 bg-white rounded-lg p-2.5 ring-1 ring-stone-100" style="animation:fadeInUp .3s ease-out both;animation-delay:${idx*0.04}s">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-theme-primary/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.556C6.59 7.475 6 8.438 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-stone-900 truncate">${p.name}</p>
                    <p class="text-[11px] text-stone-400">${fmt(price)} × ${qty}</p>
                </div>
                <p class="text-xs font-bold text-stone-800">${fmt(price * qty)}</p>
            </div>`;
        });
        itemsEl.innerHTML = html;
        countEl.textContent = totalQty + ' item';
        subtotalEl.textContent = fmt(subtotal);
        totalEl.textContent = fmt(subtotal);
    }

    window.openCheckoutModal = function () {
        document.getElementById('order-num-display').textContent = config.previewOrderNumber;
        chkOverlay.classList.remove('hidden', 'opacity-0');
        chkModal.classList.remove('hidden');
        const inner = document.getElementById('checkout-modal-inner');
        inner.classList.remove('hidden');
        inner.className = 'w-full max-w-lg glass-strong rounded-2xl shadow-2xl animate-scaleIn';
        renderCheckoutItems();
    };

    function closeCheckoutModal() {
        const inner = document.getElementById('checkout-modal-inner');
        inner.className = 'w-full max-w-lg glass-strong rounded-2xl shadow-2xl animate-scaleOut';
        chkOverlay.classList.add('opacity-0');
        setTimeout(() => { chkModal.classList.add('hidden'); inner.classList.add('hidden'); chkOverlay.classList.add('hidden'); }, 300);
    }
    window.closeCheckoutModal = closeCheckoutModal;

    chkOverlay.addEventListener('click', closeCheckoutModal);

    window.submitCheckout = async function () {
        const btn = document.getElementById('checkout-submit-btn');
        const label = document.getElementById('checkout-btn-label');
        const spinner = document.getElementById('checkout-btn-spinner');
        if (btn.disabled) return;
        const phone = document.getElementById('checkout-phone').value.trim();
        if (!phone) {
            document.getElementById('checkout-phone').focus();
            showToast('<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', 'No. Telepon harus diisi!', 'rose');
            return;
        }
        btn.disabled = true; label.classList.add('hidden'); spinner.classList.remove('hidden');

        const cart = getCart();
        const items = [];
        checkoutProducts.forEach(p => {
            const qty = cart[p.id] || 0;
            if (qty > 0 && (p.is_unlimited || p.stock === undefined || p.stock >= qty)) {
                items.push({ product_id: p.id, quantity: qty });
            }
        });
        if (items.length === 0) { showToast('', 'Tidak ada item yang dapat diproses', 'rose'); btn.disabled = false; label.classList.remove('hidden'); spinner.classList.add('hidden'); return; }

        const voucherCode = document.getElementById('checkout-voucher').value.trim();
        if (voucherCode) {
            const subtotal = parseInt((document.getElementById('checkout-subtotal').textContent || '0').replace(/[^0-9]/g, '')) || 0;
            try {
                const checkResp = await fetch(config.checkVoucherUrl + '?code=' + encodeURIComponent(voucherCode) + '&subtotal=' + subtotal + '&branch_id=' + config.branchId, {
                    headers: { 'Accept': 'application/json' },
                });
                const checkData = await checkResp.json();
                if (!checkResp.ok || !checkData.valid) {
                    showVoucherError(checkData.error || 'Kode voucher tidak valid');
                    btn.disabled = false; label.classList.remove('hidden'); spinner.classList.add('hidden');
                    return;
                }
            } catch (e) {
                showVoucherError('Gagal memeriksa kode voucher. Silakan coba lagi.');
                btn.disabled = false; label.classList.remove('hidden'); spinner.classList.add('hidden');
                return;
            }
        }

        try {
            const resp = await fetch(config.storeUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({
                    customer_name: 'Pembeli',
                    customer_phone: phone,
                    notes: document.getElementById('checkout-notes').value.trim(),
                    items,
                    voucher_code: document.getElementById('checkout-voucher').value.trim(),
                    branch_id: config.branchId,
                }),
            });
            const result = await resp.json();
            if (!resp.ok) throw new Error(result.error || 'Terjadi kesalahan.');
            setCookie(cartCookieName(), '{}', -1);
            window.location.href = result.redirect || (result.branch_slug ? config.baseUrl + '/public/' + result.branch_slug : config.catalogUrl);
        } catch (err) {
            showToast('<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>', err.message || 'Gagal membuat pesanan', 'rose');
            btn.disabled = false; label.classList.remove('hidden'); spinner.classList.add('hidden');
        }
    };
}

const CSRF = document.querySelector('meta[name="csrf-token"]').content;

window.showVoucherError = function (message) {
    const overlay = document.getElementById('voucher-error-overlay');
    const modal = document.getElementById('voucher-error-modal');
    const msgEl = document.getElementById('voucher-error-msg');
    overlay.classList.remove('hidden', 'opacity-0');
    modal.classList.remove('hidden');
    msgEl.textContent = message;
};

window.closeVoucherError = function () {
    const overlay = document.getElementById('voucher-error-overlay');
    const modal = document.getElementById('voucher-error-modal');
    overlay.classList.add('opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); overlay.classList.add('hidden'); }, 200);
};

document.getElementById('voucher-error-overlay')?.addEventListener('click', window.closeVoucherError);

function initVoucherCheck() {
    let voucherTimeout;
    const voucherInput = document.getElementById('checkout-voucher');
    const checkoutDiscountRow = document.getElementById('checkout-discount-row');
    const checkoutDiscountEl = document.getElementById('checkout-discount');
    const checkoutSubtotalEl = document.getElementById('checkout-subtotal');
    const checkoutTotalEl = document.getElementById('checkout-total');

    voucherInput.addEventListener('input', function () {
        clearTimeout(voucherTimeout);
        const code = this.value.trim();
        if (!code) {
            checkoutDiscountRow.classList.add('hidden');
            checkoutTotalEl.textContent = checkoutSubtotalEl.textContent;
            return;
        }
        voucherTimeout = setTimeout(async () => {
            const raw = checkoutSubtotalEl.textContent.replace(/[^0-9]/g, '');
            const subtotal = parseInt(raw) || 0;
            if (subtotal === 0) return;
            try {
                const resp = await fetch(config.checkVoucherUrl + '?code=' + encodeURIComponent(code) + '&subtotal=' + subtotal + '&branch_id=' + config.branchId, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await resp.json();
                if (resp.ok && data.valid) {
                    checkoutDiscountEl.textContent = '-' + fmt(data.discount);
                    checkoutDiscountRow.classList.remove('hidden');
                    const total = Math.max(0, subtotal - data.discount);
                    checkoutTotalEl.textContent = fmt(total);
                } else {
                    checkoutDiscountRow.classList.add('hidden');
                    checkoutTotalEl.textContent = checkoutSubtotalEl.textContent;
                }
            } catch (e) {
                checkoutDiscountRow.classList.add('hidden');
                checkoutTotalEl.textContent = checkoutSubtotalEl.textContent;
            }
        }, 300);
    });
}

function initScrollReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); observer.unobserve(e.target); } });
    }, { threshold: .08, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

function initHeaderShrink() {
    const header = document.querySelector('header');
    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        header.style.borderBottomColor = y > 20 ? 'rgba(231,229,228,.6)' : 'transparent';
        lastScroll = y;
    }, { passive: true });
}

export function initPublicCatalog(cfg) {
    config = cfg;
    // Cart used to live under one unscoped 'cart_public' cookie shared by
    // every branch; now that it's per-branch (cart_public_{id}), drop any
    // leftover old cookie instead of leaving it to silently reappear.
    if (getCookie('cart_public') !== null) {
        setCookie('cart_public', '', -1);
    }
    initAddToCart();
    initCategoryFilters();
    initBackPrevention();
    initCartModal();
    initCheckoutModal();
    initVoucherCheck();
    initScrollReveal();
    initHeaderShrink();
    updateCartUI(getCart());
}

window.initPublicCatalog = initPublicCatalog;
