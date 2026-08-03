var selectedMethod = null;
var pollInterval = null;
var payPopup = null;
var config;

function processPayment() {
    var btn = document.getElementById('pay-btn');
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>';

    try {
        fetch(config.invoiceUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf },
            body: JSON.stringify({ payment_method: selectedMethod, is_public: config.isPublic }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success && data.is_direct) {
                    showDirectSuccess(data.payment_method || selectedMethod, data);
                } else if (data.success && data.paid_directly) {
                    var methodLabel = data.payment_method_label || selectedMethod;
                    showSuccess(methodLabel);
                } else if (data.invoice_url) {
                    showPayLink(data.invoice_url);
                    startPolling(data.xendit_id);
                } else if (data.error) {
                    showError(data.error);
                }
            })
            .catch(function () {
                showError('Gagal memproses pembayaran. Silakan coba lagi.');
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = orig;
            });
    } catch (e) {
        showError('Gagal memproses pembayaran. Silakan coba lagi.');
        btn.disabled = false;
        btn.innerHTML = orig;
    }
}

function showPayLink(url) {
    if (config.onShowPayLink) { config.onShowPayLink(); }
    payPopup = window.open(url, 'xendit_payment', 'width=480,height=720,scrollbars=yes');
}

function showSuccess(paymentMethod) {
    if (config.onShowSuccess) { config.onShowSuccess(paymentMethod); }
    if (payPopup && !payPopup.closed) { payPopup.close(); }
}

function showDirectSuccess(paymentMethod, data) {
    if (config.onShowDirectSuccess) { config.onShowDirectSuccess(paymentMethod, data); }
}

function showError(message) {
    if (config.onShowError) { config.onShowError(message); }
}

function startPolling(xenditId) {
    if (pollInterval) { clearTimeout(pollInterval); pollInterval = null; }
    var delay = 2000;
    const poll = function () {
        fetch('/orders/public/status/' + xenditId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status === 'paid' || data.status === 'success') {
                    showSuccess(data.payment_method || selectedMethod);
                    return;
                }
                if (data.status === 'expired') {
                    if (config.onExpired) config.onExpired();
                    return;
                }
                delay = Math.min(delay * 1.5, 10000);
            })
            .catch(function () {});
        pollInterval = setTimeout(poll, delay + Math.random() * 1000);
    };
    poll();
}

export function initPayment(cfg) {
    config = cfg;

    if (config.backPrevention !== false) {
        history.pushState(null, '', location.href);
        window.addEventListener('popstate', function () {
            history.pushState(null, '', location.href);
            alert('Selesaikan transaksi terlebih dahulu!');
        });
    }

    document.querySelectorAll('.pay-option').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.pay-option').forEach(function (b) {
                b.classList.remove('selected');
                var c = b.querySelector('.pay-check');
                if (c) c.setAttribute('class', 'h-5 w-5 text-stone-300 pay-check shrink-0');
            });
            this.classList.add('selected');
            var check = this.querySelector('.pay-check');
            if (check) check.setAttribute('class', 'h-5 w-5 text-theme-primary pay-check shrink-0');
            selectedMethod = this.dataset.method;
            document.getElementById('pay-btn').disabled = false;
            document.getElementById('pay-error').classList.add('hidden');
            if (config.onPayMethodChange) config.onPayMethodChange(selectedMethod);
        });
    });

    document.getElementById('pay-btn').addEventListener('click', function () {
        if (!selectedMethod) return;
        if (selectedMethod === 'cash' && config.onCashSelect) {
            config.onCashSelect(processPayment);
        } else {
            processPayment();
        }
    });
}

window.initPayment = initPayment;
