@extends('layouts.app')
@section('title', 'Transaksi Baru')
@section('subtitle', 'Pilih produk dan selesaikan transaksi.')
@section('content')
<form method="POST" action="{{ route('sales.store') }}" id="sale-form" class="space-y-6">
    @csrf

    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Pilih Produk</h3>
        </div>
        <div id="product-list" class="space-y-3">
            <div class="flex gap-3 items-end product-row bg-warm-50/50 rounded-xl p-3 border border-warm-100">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-warm-600 mb-1">Produk</label>
                    <select name="items[0][product_id]" required class="product-select block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                        <option value="">Pilih Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->sale_price ?? $product->price }}" data-stock="{{ $product->stock }}" data-unlimited="{{ $product->is_unlimited ? 'true' : 'false' }}">
                                {{ $product->name }} (Stok: {{ $product->isUnlimited() ? '∞' : $product->stock }}) - Rp {{ number_format($product->sale_price ?? $product->price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-24">
                    <label class="block text-xs font-medium text-warm-600 mb-1">Qty</label>
                    <input type="number" name="items[0][quantity]" min="1" value="1" required class="qty-input block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>
                <div class="w-32 pt-6">
                    <span class="text-sm font-semibold text-warm-800 line-total">Rp 0</span>
                </div>
                <button type="button" class="remove-row pt-6 text-rose-400 hover:text-rose-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
        <button type="button" id="add-product" class="mt-3 flex items-center gap-1 text-sm font-medium text-theme-primary hover:text-theme-primary transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Tambah Produk
        </button>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-4">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V6.75a60.007 60.007 0 00-15.797-2.101M4.5 18.75V6.75m0 0h15M6 12.75h3m-3 3.75h3m-6-3.75h.008M6 6.75h.008" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Ringkasan</h3>
        </div>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-warm-500">Subtotal</span>
                <span class="font-semibold text-warm-900" id="subtotal-display">Rp 0</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-warm-500">Diskon</span>
                <input type="number" name="discount" value="0" min="0" step="1000" id="discount-input" class="w-40 rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm text-right bg-white">
            </div>
            <div class="border-t border-warm-100 pt-3 flex justify-between text-base">
                <span class="font-bold text-warm-900">Total</span>
                <span class="font-bold text-theme-primary text-lg" id="total-display">Rp 0</span>
                <input type="hidden" name="total_hidden" id="total-hidden">
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50 space-y-4">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 text-sky-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Pembayaran</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                <select name="payment_method" required class="block w-full rounded-xl border-warm-200 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                    <option value="cash">Tunai</option>
                    <option value="transfer">Transfer</option>
                    <option value="xendit">Xendit (Online)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-warm-700 mb-1.5">Jumlah Dibayar <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-warm-400 font-medium">Rp</span>
                    <input type="number" name="paid_amount" value="0" required min="0" step="1000" id="paid-amount" class="block w-full rounded-xl border-warm-200 pl-10 pr-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white">
                </div>
            </div>
        </div>
        <div class="text-right text-sm font-medium" id="change-display"></div>
        <div>
            <label class="block text-sm font-medium text-warm-700 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2" class="block w-full rounded-xl border-warm-200 px-4 py-2.5 shadow-sm focus:border-theme-primary focus:ring-theme-primary/20 text-sm bg-white"></textarea>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <a href="{{ route('sales.index') }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-warm-700 ring-1 ring-warm-200 hover:bg-warm-50 hover:ring-warm-300 transition-all duration-200">Batal</a>
        <button type="submit" class="rounded-xl bg-theme-gradient-r px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 hover:shadow-md transition-all duration-200">
            Proses Penjualan
        </button>
    </div>
</form>

@push('scripts')
<script>
let rowIndex = 1;

function updateTotal(row) {
    const select = row.querySelector('.product-select');
    const qty = row.querySelector('.qty-input');
    const lineTotal = row.querySelector('.line-total');
    const price = select.selectedOptions[0]?.dataset?.price || 0;
    const unlimited = select.selectedOptions[0]?.dataset?.unlimited === 'true';
    const stock = parseInt(select.selectedOptions[0]?.dataset?.stock || 0);
    const requested = parseInt(qty.value || 0);
    if (!unlimited && requested > stock) {
        qty.value = stock;
        if (stock === 0) qty.value = 1;
    }
    const total = price * parseInt(qty.value || 0);
    lineTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let subtotal = 0;
    document.querySelectorAll('.line-total').forEach(el => {
        const val = el.textContent.replace(/[^0-9]/g, '');
        subtotal += parseInt(val || 0);
    });
    const discount = parseInt(document.getElementById('discount-input').value || 0);
    const total = Math.max(0, subtotal - discount);
    document.getElementById('subtotal-display').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total-hidden').value = total;

    const paid = parseInt(document.getElementById('paid-amount').value || 0);
    const change = paid - total;
    const changeDisplay = document.getElementById('change-display');
    if (change > 0) {
        changeDisplay.innerHTML = '<span class="text-emerald-600">Kembali: Rp ' + change.toLocaleString('id-ID') + '</span>';
    } else if (paid > 0 && paid < total) {
        changeDisplay.innerHTML = '<span class="text-theme-primary">Kurang: Rp ' + (total - paid).toLocaleString('id-ID') + '</span>';
    } else {
        changeDisplay.textContent = '';
    }
}

document.getElementById('add-product').addEventListener('click', function() {
    const list = document.getElementById('product-list');
    const firstRow = list.querySelector('.product-row');
    const newRow = firstRow.cloneNode(true);
    newRow.innerHTML = newRow.innerHTML.replace(/items\[0\]/g, 'items[' + rowIndex + ']');
    newRow.querySelectorAll('input').forEach(el => { el.value = ''; if (el.type === 'number') el.value = 1; });
    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('.line-total').textContent = 'Rp 0';
    newRow.querySelector('.product-select').addEventListener('change', () => updateTotal(newRow));
    newRow.querySelector('.qty-input').addEventListener('input', function() {
        const select = this.closest('.product-row').querySelector('.product-select');
        const unlimited = select.selectedOptions[0]?.dataset?.unlimited === 'true';
        const stock = parseInt(select.selectedOptions[0]?.dataset?.stock || 0);
        if (!unlimited && parseInt(this.value) > stock) { this.value = stock; }
        updateTotal(this.closest('.product-row'));
    });
    newRow.querySelector('.remove-row').addEventListener('click', () => { newRow.remove(); calculateGrandTotal(); });
    list.appendChild(newRow);
    rowIndex++;
});

document.querySelectorAll('.product-select').forEach(el => {
    el.addEventListener('change', function() { updateTotal(this.closest('.product-row')); });
});
document.querySelectorAll('.qty-input').forEach(el => {
    el.addEventListener('input', function() {
        const row = this.closest('.product-row');
        const select = row.querySelector('.product-select');
        const unlimited = select.selectedOptions[0]?.dataset?.unlimited === 'true';
        const stock = parseInt(select.selectedOptions[0]?.dataset?.stock || 0);
        if (!unlimited && parseInt(this.value) > stock) { this.value = stock; }
        updateTotal(row);
    });
});
document.getElementById('discount-input').addEventListener('input', calculateGrandTotal);
document.getElementById('paid-amount').addEventListener('input', calculateGrandTotal);
calculateGrandTotal();
</script>
@endpush
@endsection
