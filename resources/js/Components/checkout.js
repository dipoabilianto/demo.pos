var products = [], cartMap = {};

var $ = function (id) { return document.getElementById(id); };

function fmt(n) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); }

function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

function updatePrice(subtotal) {
    var discount = Math.min(parseInt($('discountInput')?.value) || 0, subtotal);
    var total = subtotal - discount;
    $('subtotalDisplay').textContent = fmt(subtotal);
    $('totalDisplay').textContent = fmt(total);
}

function getCurrentSubtotal() {
    var s = 0;
    products.forEach(function (p) { var qty = cartMap[p.id] || 0; if (qty <= 0) return; s += (p.sale_price ?? p.price) * qty; });
    return s;
}

function showEmpty() {
    $('cartSkeleton').classList.add('hidden');
    $('cartItems').classList.add('hidden');
    $('cartEmpty').classList.remove('hidden');
    updatePrice(0);
    $('itemCount').textContent = '';
}

function renderCart() {
    $('cartSkeleton').classList.add('hidden');
    $('cartItems').classList.remove('hidden');
    $('cartItems').innerHTML = '';
    var subtotal = 0, anyOos = false, totalItems = 0;
    products.forEach(function (p, i) {
        var qty = cartMap[p.id] || 0; if (qty <= 0) return;
        totalItems += qty;
        var effectivePrice = p.sale_price ?? p.price;
        var lineTotal = effectivePrice * qty;
        subtotal += lineTotal;
        var oos = !p.is_unlimited && p.stock !== undefined && p.stock < qty;
        if (oos) anyOos = true;
        var colors = ['#fef3c7', '#fce7f3', '#dbeafe', '#d1fae5', '#ede9fe', '#ffedd5'];
        var bg = colors[p.id % colors.length];
        var div = document.createElement('div');
        div.className = 'item-appear flex items-center gap-3 p-3 rounded-xl ' + (oos ? 'bg-rose-50 border border-rose-200' : 'bg-stone-50/70 border border-stone-100');
        div.id = 'cart-item-' + p.id;
        div.style.animationDelay = (i * 0.06) + 's';
        div.innerHTML =
            '<div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:' + bg + '">' +
                '<svg class="w-5.5 h-5.5" style="color:' + (oos ? '#e11d48' : '#d97706') + '" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v-1.5m0 1.5c-1.355 0-2.55.056-4.024.556C6.59 7.475 6 8.438 6 10.5v2.25c0 1.562.59 3.025 1.976 3.194m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M8.362 15.75l-1.388 4.149m11.026-4.149l1.388 4.149"/></svg>' +
            '</div>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-semibold text-stone-900 truncate">' + esc(p.name) + '</p>' +
                '<p class="text-xs text-stone-400 mt-0.5">' + (p.sale_price ? '<span class="line-through">' + fmt(p.price) + '</span> ' + fmt(p.sale_price) : fmt(p.price)) + '</p>' +
                (oos ? '<p class="text-xs text-rose-500 font-medium mt-0.5">Stok tersedia: ' + p.stock + '</p>' : '') +
            '</div>' +
            '<div class="flex items-center gap-1.5 shrink-0">' +
                '<button type="button" onclick="window.cartUpdate(' + p.id + ',-1)" class="qty-btn">−</button>' +
                '<span class="w-7 text-center text-sm font-bold text-stone-900" id="qty-' + p.id + '">' + qty + '</span>' +
                '<button type="button" onclick="window.cartUpdate(' + p.id + ',1)" class="qty-btn add">+</button>' +
            '</div>' +
            '<p class="text-sm font-bold text-stone-800 w-[72px] text-right shrink-0">' + fmt(effectivePrice * qty) + '</p>';
        $('cartItems').appendChild(div);
    });
    $('itemCount').textContent = totalItems > 0 ? totalItems + ' item' : '';
    if (subtotal === 0) { showEmpty(); return; }
    updatePrice(subtotal);
    $('submitBtn').disabled = anyOos;
    $('btnLabel').innerHTML = anyOos
        ? '<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg> Stok tidak mencukupi</span>'
        : '<span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Buat Pesanan Sekarang</span>';
}

window.cartUpdate = function (id, delta) {
    var cur = cartMap[id] || 0;
    var next = cur + delta;
    var p = products.find(function (p) { return p.id === id; });
    if (delta > 0 && p && !p.is_unlimited && p.stock !== undefined && p.stock < next) { alert('Stok ' + p.name + ' hanya tersedia ' + p.stock + '.'); return; }
    if (next <= 0) { delete cartMap[id]; var el = $('cart-item-' + id); if (el) { el.classList.add('item-remove'); setTimeout(function() { el.remove(); }, 200); } }
    else { cartMap[id] = next; var el = $('qty-' + id); if (el) el.textContent = next; }
    var d = new Date(); d.setTime(d.getTime() + 7 * 864e5);
    document.cookie = 'cart=' + encodeURIComponent(JSON.stringify(cartMap)) + '; path=/; expires=' + d.toUTCString();
    recalcTotal();
};

function recalcTotal() {
    var subtotal = 0, totalItems = 0;
    products.forEach(function (p) { var qty = cartMap[p.id] || 0; if (qty <= 0) return; totalItems += qty; subtotal += (p.sale_price ?? p.price) * qty; });
    $('itemCount').textContent = totalItems > 0 ? totalItems + ' item' : '';
    updatePrice(subtotal);
    if (totalItems === 0) { $('cartItems').classList.add('hidden'); $('cartEmpty').classList.remove('hidden'); }
}

export function initCheckout(config) {
    history.pushState(null, '', location.href);
    window.addEventListener('popstate', function (e) {
        history.pushState(null, '', location.href);
        alert('Selesaikan transaksi terlebih dahulu!');
    });
    var csrfToken = config.csrf;

    $('discountInput')?.addEventListener('input', function () {
        updatePrice(getCurrentSubtotal());
    });

    $('checkoutForm').addEventListener('submit', async function (e) {
        e.preventDefault(); if ($('submitBtn').disabled) return;
        var items = [];
        products.forEach(function (p) { var qty = cartMap[p.id] || 0; if (qty > 0 && (p.is_unlimited || p.stock === undefined || p.stock >= qty)) { items.push({ product_id: p.id, quantity: qty }); } });
        if (items.length === 0) { alert('Tidak ada item yang dapat diproses.'); return; }

        var customer_name, customer_phone, notes;
        customer_phone = $('customer_phone')?.value.trim() || '';
        notes = $('notes')?.value.trim() || '';

        if (config.isPublic) {
            if (!customer_phone) { $('customer_phone')?.focus(); alert('No. Telepon harus diisi!'); return; }
            customer_name = 'Pembeli';
        } else {
            if ($('customer_name')) { $('customer_name').classList.remove('error'); }
            if ($('err-name')) { $('err-name').classList.add('hidden'); }
            customer_name = $('customer_name')?.value.trim() || '';
            if (!customer_name) { $('customer_name')?.classList.add('error'); $('err-name')?.classList.remove('hidden'); $('customer_name')?.focus(); $('customer_name')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
        }

        $('submitBtn').disabled = true; $('btnLabel').classList.add('hidden'); $('btnSpinner').classList.remove('hidden');
        try {
            var url = config.isPublic ? config.publicStoreUrl : config.storeUrl;
            var body = { customer_name: customer_name, customer_phone: customer_phone, notes: notes, items: items };
            if (!config.isPublic) {
                body.discount = parseInt($('discountInput')?.value) || 0;
            } else {
                var vc = ($('voucherInput')?.value || '').trim();
                if (vc) body.voucher_code = vc;
            }
            var resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            var result = await resp.json();
            if (!resp.ok) throw new Error(result.error || 'Terjadi kesalahan.');
            var d = new Date(); d.setTime(d.getTime() - 864e5);
            document.cookie = 'cart=; path=/; expires=' + d.toUTCString();
            window.location.href = result.redirect || config.catalogUrl;
        } catch (err) { alert(err.message || 'Gagal membuat pesanan.'); $('submitBtn').disabled = false; $('btnLabel').classList.remove('hidden'); $('btnSpinner').classList.add('hidden'); }
    });

    if ($('order-num-display')) {
        $('order-num-display').textContent = config.previewOrderNumber;
    }

    (async function () {
        try {
            var raw = getCookie('cart');
            if (!raw) { showEmpty(); return; }
            cartMap = JSON.parse(raw);
            var ids = Object.keys(cartMap).map(Number).filter(function (id) { return id > 0; });
            if (ids.length === 0) { showEmpty(); return; }
            var resp = await fetch(config.batchUrl + '?ids=' + ids.join(','), { headers: { 'Accept': 'application/json' } });
            if (!resp.ok) throw new Error('Gagal memuat');
            var data = await resp.json();
            products = Array.isArray(data) ? data : [];
            renderCart();
        } catch (e) {
            $('cartSkeleton').classList.add('hidden');
            $('cartEmpty').classList.remove('hidden');
            $('cartEmpty').innerHTML = '<div class="w-16 h-16 rounded-2xl bg-rose-50 flex items-center justify-center mb-4"><svg class="w-8 h-8 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg></div><p class="text-sm font-medium text-rose-500">Gagal memuat keranjang</p><button onclick="location.reload()" class="mt-3 text-xs text-theme-primary underline underline-offset-2">Muat ulang halaman</button>';
        }
    })();
}

function getCookie(name) {
    var m = document.cookie.match(new RegExp('(?:^|;)\\s*' + name + '\\s*=\\s*([^;]+)'));
    return m ? decodeURIComponent(m[1]) : null;
}

window.initCheckout = initCheckout;
