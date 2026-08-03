let config;

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

function getCart() { try { return JSON.parse(getCookie('cart') || '{}'); } catch { return {}; } }

function saveCart(cart) { setCookie('cart', JSON.stringify(cart)); updateCartUI(cart); }

let sidebarLoading = false;

function updateCartUI(cart) {
    const ids = Object.keys(cart);
    const totalQty = ids.reduce((s, id) => s + cart[id], 0);
    document.getElementById('cart-badge').textContent = totalQty;
    renderCartSidebar(cart);
}

async function renderCartSidebar(cart) {
    const ids = Object.keys(cart);
    const totalQty = ids.reduce((s, id) => s + cart[id], 0);

    const containers = [
        { items: document.getElementById('cart-sidebar-items'), count: document.getElementById('cart-sidebar-count'), footer: document.getElementById('cart-sidebar-footer'), subtotal: document.getElementById('cart-sidebar-subtotal') },
        { items: document.getElementById('cart-sidebar-items-mobile'), count: document.getElementById('cart-sidebar-count-mobile'), footer: document.getElementById('cart-sidebar-footer-mobile'), subtotal: document.getElementById('cart-sidebar-subtotal-mobile') },
    ];

    containers.forEach(({ count }) => { if (count) count.textContent = totalQty > 0 ? totalQty + ' item' : '0 item'; });

    if (ids.length === 0) {
        const emptyHtml = `<div class="flex flex-col items-center justify-center min-h-[160px] text-stone-300"><svg class="h-10 w-10 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="0.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.2.665-.35 1.614-1.119 1.614H4.25a1.125 1.125 0 01-1.119-1.243l1.263-12A1.125 1.125 0 015.477 7.5h12.5c.576 0 1.059.435 1.119 1.007zM8.75 10.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg><p class="text-xs font-medium text-stone-400">Keranjang kosong</p><p class="text-[11px] text-stone-300 mt-0.5">Siap memulai transaksi selanjutnya!</p></div>`;
        containers.forEach(({ items }) => { if (items) items.innerHTML = emptyHtml; });
        return;
    }

    if (sidebarLoading) {
        const spinner = `<div class="flex items-center justify-center py-8"><svg class="animate-spin h-5 w-5 text-theme-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></div>`;
        containers.forEach(({ items }) => { if (items) items.innerHTML = spinner; });
        return;
    }

    sidebarLoading = true;
    const spinner = `<div class="flex items-center justify-center py-8"><svg class="animate-spin h-5 w-5 text-theme-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg></div>`;
    containers.forEach(({ items }) => { if (items) items.innerHTML = spinner; });

    try {
        const res = await fetch(config.batchUrl + '?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } });
        const products = await res.json();
        let subtotal = 0;
        let html = '';
        ids.forEach((id, idx) => {
            const qty = cart[id];
            const p = products.find(p => p.id == id);
            if (!p) return;
            const sub = p.price * qty;
            subtotal += sub;
            html += `<div class="flex items-center gap-3 bg-white rounded-lg px-3 py-2.5 ring-1 ring-stone-100" style="animation:fadeInUp .3s ease-out both;animation-delay:${idx*0.03}s">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-stone-900 truncate">${p.name}</p>
                    <p class="text-xs text-stone-400">Rp ${Number(p.price).toLocaleString('id-ID')}</p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button onclick="updateQty(${id},-1)" class="flex h-6 w-6 items-center justify-center rounded bg-stone-100 text-stone-500 hover:bg-stone-200 text-xs font-bold">−</button>
                    <span class="w-6 text-center text-xs font-semibold text-stone-900">${qty}</span>
                    <button onclick="updateQty(${id},1)" class="flex h-6 w-6 items-center justify-center rounded bg-stone-100 text-stone-500 hover:bg-theme-primary/10 hover:text-theme-primary text-xs font-bold">+</button>
                </div>
            </div>`;
        });
        containers.forEach(({ items }) => { if (items) items.innerHTML = html; });
        const fmt = 'Rp ' + subtotal.toLocaleString('id-ID');
        containers.forEach(({ subtotal: el }) => { if (el) el.textContent = fmt; });
    } catch (e) {
        const err = `<div class="flex flex-col items-center justify-center py-10 text-rose-500"><p class="text-xs">Gagal memuat keranjang</p></div>`;
        containers.forEach(({ items }) => { if (items) items.innerHTML = err; });
    }
    sidebarLoading = false;
}

window.toggleSoldOut = async function (productId) {
    try {
        const resp = await fetch(config.baseUrl + '/products/' + productId + '/toggle-sold', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await resp.json();
        if (resp.ok) {
            const card = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (card) {
                card.dataset.soldOut = data.is_sold_out ? 'true' : 'false';
                const imgDiv = card.querySelector('[class*="aspect-"]');
                const overlay = imgDiv.querySelector('.absolute.inset-0');
                const badge = card.querySelector('h3');
                const btn = card.querySelector('.add-to-cart-btn');
                const toggleBtn = card.querySelector('[onclick*="toggleSoldOut"]');
                if (data.is_sold_out) {
                    imgDiv.classList.add('opacity-50');
                    if (!overlay) {
                        const div = document.createElement('div');
                        div.className = 'absolute inset-0 bg-stone-900/40 flex items-center justify-center z-10';
                        div.innerHTML = '<span class="rotate-[-12deg] text-base sm:text-lg font-black tracking-widest text-white drop-shadow-lg">SOLD</span>';
                        imgDiv.appendChild(div);
                    }
                    if (badge) badge.classList.add('line-through', 'text-stone-400');
                    if (btn) { btn.classList.remove('bg-theme-gradient-r'); btn.classList.add('bg-stone-300', 'cursor-not-allowed'); }
                    if (toggleBtn) toggleBtn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                } else {
                    imgDiv.classList.remove('opacity-50');
                    if (overlay) overlay.remove();
                    if (badge) badge.classList.remove('line-through', 'text-stone-400');
                    if (btn) { btn.classList.remove('bg-stone-300', 'cursor-not-allowed'); btn.classList.add('bg-theme-gradient-r'); }
                    if (toggleBtn) toggleBtn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>';
                }
            }
            Alpine.store('toastManager').success(data.is_sold_out ? 'Menu ditandai SOLD' : 'Menu tersedia kembali');
        } else {
            Alpine.store('toastManager').error(data.message || 'Gagal mengubah status');
        }
    } catch (e) {
        Alpine.store('toastManager').error('Gagal mengubah status');
    }
};

function initCategoryFilters() {
    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.className = 'cat-pill rounded-full px-3 py-1.5 text-[11px] font-medium text-stone-500 bg-stone-100/80 hover:bg-stone-200/80 hover:text-stone-800 hover:shadow-sm';
            });
            if (this.dataset.cat === 'all') {
                this.className = 'cat-pill active rounded-full px-3 py-1.5 text-[11px] font-semibold bg-theme-gradient-r text-white shadow-sm shadow-theme-shadow';
                document.querySelectorAll('.cat-section').forEach(s => { s.style.display = ''; });
            } else {
                this.className = 'cat-pill category-pill-active active rounded-full px-3 py-1.5 text-[11px] font-bold bg-theme-primary/10 text-theme-primary ring-2 ring-theme-primary/30';
                document.querySelectorAll('.cat-section').forEach(s => { s.style.display = s.dataset.cat === this.dataset.cat ? '' : 'none'; });
            }
        });
    });
}

function initBackPrevention() {
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function (e) {
        if (Object.keys(getCart()).length > 0) {
            history.pushState(null, '', location.href);
            Alpine.store('toastManager').error('Selesaikan transaksi terlebih dahulu!');
        }
    });
}

function initMobileCart() {
    const overlayMobile = document.getElementById('cart-overlay-mobile');
    const sidebarMobile = document.getElementById('cart-sidebar-mobile');

    window.openCartMobile = function () {
        overlayMobile.classList.remove('hidden');
        sidebarMobile.classList.remove('hidden', 'translate-x-full');
        setTimeout(() => overlayMobile.classList.remove('opacity-0'), 10);
        renderCartSidebar(getCart());
    };

    window.closeCartMobile = function () {
        overlayMobile.classList.add('opacity-0');
        sidebarMobile.classList.add('translate-x-full');
        setTimeout(() => { sidebarMobile.classList.add('hidden'); overlayMobile.classList.add('hidden'); }, 300);
    };

    document.getElementById('cart-toggle-mobile')?.addEventListener('click', openCartMobile);
    document.getElementById('cart-close-mobile')?.addEventListener('click', closeCartMobile);
    overlayMobile?.addEventListener('click', closeCartMobile);
}

function initSearch() {
    let searchTimeout;
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 1) { searchResults.classList.add('hidden'); return; }
        searchTimeout = setTimeout(() => {
            fetch(config.searchUrl + '?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(products => {
                    if (products.length === 0) {
                        searchResults.innerHTML = '<div class="px-4 py-3 text-xs text-stone-400 text-center">Produk tidak ditemukan</div>';
                    } else {
                        searchResults.innerHTML = products.map(p => {
                            const price = p.sale_price || p.price;
                            return '<div class="search-result flex items-center gap-3 px-4 py-2.5 hover:bg-amber-50 cursor-pointer border-b border-stone-100 last:border-0 transition-colors" data-id="' + p.id + '" data-name="' + p.name.replace(/'/g, '\\\'') + '" data-price="' + price + '" data-stock="' + p.stock + '" data-unlimited="' + (p.is_unlimited ? 'true' : 'false') + '" data-sold-out="' + (p.is_sold_out ? 'true' : 'false') + '">' +
                                '<div class="flex-1 min-w-0"><div class="text-sm font-medium text-stone-900 truncate">' + p.name + '</div><div class="text-xs text-stone-400">Rp ' + price.toLocaleString('id-ID') + (p.sku ? ' &middot; ' + p.sku : '') + '</div></div>' +
                                '<div class="shrink-0 text-xs font-semibold ' + (p.is_sold_out ? 'text-stone-300' : (p.is_unlimited || p.stock > 0 ? 'text-emerald-600' : 'text-rose-500')) + '">' + (p.is_sold_out ? 'SOLD' : (p.is_unlimited ? '&infin;' : p.stock)) + '</div>' +
                                '</div>';
                        }).join('');
                    }
                    searchResults.classList.remove('hidden');
                });
        }, 200);
    });

    searchResults.addEventListener('click', function (e) {
        const item = e.target.closest('.search-result');
        if (!item) return;
        searchInput.value = '';
        searchResults.classList.add('hidden');
        addToCart(item.dataset.id);
    });

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { searchResults.classList.add('hidden'); this.blur(); }
        if (e.key === 'Enter') {
            e.preventDefault();
            const first = searchResults.querySelector('.search-result');
            if (first) {
                searchInput.value = '';
                searchResults.classList.add('hidden');
                addToCart(first.dataset.id);
            }
        }
    });

    searchInput.addEventListener('blur', function () {
        setTimeout(() => searchResults.classList.add('hidden'), 200);
    });

    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length > 0) searchResults.classList.remove('hidden');
    });
}

let editingOrderId = null;

function updateEditModeUI() {
    const btn = document.getElementById('save-order-btn');
    if (!btn) return;
    if (editingOrderId) {
        btn.textContent = 'Simpan Perubahan';
        btn.classList.remove('ring-stone-300', 'bg-white', 'text-stone-700');
        btn.classList.add('ring-amber-400', 'bg-amber-50', 'text-amber-700');
    } else {
        btn.textContent = 'Simpan Pesanan';
        btn.classList.remove('ring-amber-400', 'bg-amber-50', 'text-amber-700');
        btn.classList.add('ring-stone-300', 'bg-white', 'text-stone-700');
    }
}

window.editOrder = async function (orderId) {
    if (!confirm('Mengedit akan mengosongkan keranjang saat ini. Lanjutkan?')) return;
    try {
        const res = await fetch(config.baseUrl + '/orders/' + orderId + '/items');
        if (!res.ok) {
            throw new Error('HTTP ' + res.status + ' loading order ' + orderId + ' items');
        }
        const items = await res.json();
        const cart = {};
        items.forEach(i => { cart[i.product_id] = i.quantity; });
        saveCart(cart);
        editingOrderId = orderId;
        updateEditModeUI();
        closeCartMobile();
        Alpine.store('toastManager').info('Pesanan dimuat ke keranjang. Silakan edit item.');
    } catch (err) {
        console.error('editOrder failed:', err);
        Alpine.store('toastManager').error('Gagal memuat pesanan');
    }
};

async function fetchSavedOrders() {
    const placeholder = document.getElementById('saved-orders-placeholder');
    if (!placeholder) return;
    try {
        const res = await fetch(config.savedListUrl);
        const html = await res.text();
        placeholder.innerHTML = html;
    } catch {}
}

async function saveOrder() {
    const cart = getCart();
    const ids = Object.keys(cart);
    if (ids.length === 0) {
        Alpine.store('toastManager').error('Keranjang kosong!');
        return;
    }
    const items = ids.map(id => ({ product_id: parseInt(id), quantity: cart[id] }));
    const customerName = document.querySelector('[x-model="customerName"]')?.value || '';
    const customerPhone = document.querySelector('[x-model="customerPhone"]')?.value || '';
    const payload = { items, customer_name: customerName, customer_phone: customerPhone };
    if (editingOrderId) payload.order_id = editingOrderId;
    try {
        const res = await fetch(config.saveOrderUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': config.csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            saveCart({});
            editingOrderId = null;
            updateEditModeUI();
            fetchSavedOrders();
            const msg = data.is_update ? 'Pesanan ' + data.order_number + ' diperbarui!' : 'Pesanan ' + data.order_number + ' disimpan!';
            Alpine.store('toastManager').success(msg);
        } else {
            Alpine.store('toastManager').error(data.error || 'Gagal menyimpan');
        }
    } catch {
        Alpine.store('toastManager').error('Gagal menghubungi server');
    }
}

document.getElementById('save-order-btn')?.addEventListener('click', saveOrder);
document.getElementById('save-order-btn-mobile')?.addEventListener('click', saveOrder);

function initAddToCart() {
    window.updateQty = function (id, delta) {
        const cart = getCart();
        const current = cart[id] || 0;
        const next = current + delta;
        if (next <= 0) { delete cart[id]; } else { cart[id] = next; }
        saveCart(cart);
    };

    window.addToCart = function (productId, qty = 1) {
        const card = document.querySelector(`.product-card[data-id="${productId}"]`);
        const soldOut = card?.dataset?.soldOut === 'true';
        if (soldOut) {
            Alpine.store('toastManager').error('Menu sedang SOLD, tidak dapat ditambahkan!');
            return;
        }
        const cart = getCart();
        const current = cart[productId] || 0;
        const next = current + qty;
        const unlimited = card?.dataset?.unlimited === 'true';
        const stock = parseInt(card?.dataset?.stock || 0);
        if (!unlimited && stock < next) {
            Alpine.store('toastManager').error('Stok hanya ' + stock + ', tidak mencukupi!');
            return;
        }
        cart[productId] = next;
        saveCart(cart);
        Alpine.store('toastManager').success('Ditambahkan ke keranjang!');
    };
}

export function initAdminCatalog(cfg) {
    config = cfg;
    initAddToCart();
    initCategoryFilters();
    initBackPrevention();
    initMobileCart();
    initSearch();
    updateCartUI(getCart());
    fetchSavedOrders();
}

window.initAdminCatalog = initAdminCatalog;