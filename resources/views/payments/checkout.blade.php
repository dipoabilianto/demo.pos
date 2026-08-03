@extends('layouts.app')
@section('title', 'Pembayaran Xendit')
@section('subtitle', 'Invoice: ' . $sale->invoice_number)
@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
        <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-theme-primary/10 text-theme-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
            </div>
            <h3 class="text-base font-semibold text-warm-900">Ringkasan Pesanan</h3>
        </div>
        <dl class="space-y-3">
            <div class="flex justify-between text-sm py-2">
                <dt class="text-warm-500">Invoice</dt>
                <dd class="font-semibold text-warm-900">{{ $sale->invoice_number }}</dd>
            </div>
            <div class="flex justify-between text-sm py-2 border-t border-warm-100">
                <dt class="text-warm-500">Total</dt>
                <dd class="font-bold text-theme-primary text-lg">Rp {{ number_format($sale->total, 0, ',', '.') }}</dd>
            </div>
        </dl>

        <div class="mt-6 space-y-3">
            <label class="block text-sm font-semibold text-warm-700 mb-2">Pilih Metode Pembayaran</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse ($paymentMethods as $method)
                    <button type="button" class="payment-option p-3 rounded-xl border-2 border-warm-200 hover:border-theme-primary hover:bg-theme-primary/10 text-sm font-medium text-warm-700 text-center transition-all duration-200" data-method="{{ $method->code }}">
                        <img src="{{ asset('images/payment/' . $method->code . '.svg') }}" alt="{{ $method->name }}" class="h-6 mx-auto mb-1.5 object-contain">
                        {{ $method->name }}
                    </button>
                @empty
                    <p class="col-span-2 text-sm text-warm-400 text-center py-8">Tidak ada metode pembayaran yang tersedia.</p>
                @endforelse
            </div>

            <div id="payment-error" class="hidden mt-3 rounded-xl bg-rose-50 border border-rose-200 p-3 text-sm text-rose-700"></div>

            <button id="pay-button" disabled class="mt-4 w-full rounded-xl bg-theme-gradient-r px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-theme-shadow hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                Lanjutkan Pembayaran
            </button>
        </div>
    </div>

    <div id="payment-result" class="hidden">
        <div class="rounded-2xl bg-white p-6 shadow-md shadow-warm-900/5 border border-warm-200/50">
            <div class="flex items-center gap-3 pb-4 border-b border-warm-100 mb-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                </div>
                <h3 class="text-base font-semibold text-warm-900">Instruksi Pembayaran</h3>
            </div>
            <div id="payment-instructions" class="space-y-4">
                <div class="rounded-xl bg-theme-primary/10 p-4 text-sm text-theme-primary border border-theme-primary/20">
                    <p class="font-medium">Menunggu pembayaran...</p>
                    <p class="mt-1">Silakan selesaikan pembayaran melalui halaman yang akan terbuka.</p>
                </div>
            </div>
            <div id="payment-link-area" class="mt-4 hidden">
                <a id="payment-link" href="#" target="_blank" class="block w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white text-center hover:bg-emerald-500 transition-colors shadow-sm">
                    Buka Halaman Pembayaran
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedMethod = null;

document.querySelectorAll('.payment-option').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(b => {
            b.classList.remove('border-theme-primary', 'bg-theme-primary/10');
            b.classList.add('border-warm-200');
        });
        this.classList.remove('border-warm-200');
        this.classList.add('border-theme-primary', 'bg-theme-primary/10');
        selectedMethod = this.dataset.method;
        document.getElementById('pay-button').disabled = false;
    });
});

document.getElementById('pay-button').addEventListener('click', async function() {
    if (!selectedMethod) return;
    this.disabled = true;
    this.textContent = 'Memproses...';

    try {
        const res = await fetch('{{ route("payments.create-invoice", $sale) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ payment_method: selectedMethod }),
        });

        const data = await res.json();

        if (data.invoice_url) {
            document.getElementById('payment-result').classList.remove('hidden');
            document.getElementById('payment-link').href = data.invoice_url;
            document.getElementById('payment-link-area').classList.remove('hidden');

            const instructions = document.getElementById('payment-instructions');
            instructions.innerHTML = `
                <div class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800 border border-emerald-200">
                    <p class="font-semibold flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        Invoice berhasil dibuat
                    </p>
                    <p class="mt-1">Klik tombol di bawah untuk membuka halaman pembayaran.</p>
                </div>
            `;

            window.open(data.invoice_url, '_blank');
        } else {
            throw new Error(data.error || 'Gagal membuat invoice');
        }
    } catch (err) {
        const errorDiv = document.getElementById('payment-error');
        errorDiv.textContent = err.message;
        errorDiv.classList.remove('hidden');
    } finally {
        this.disabled = false;
        this.textContent = 'Lanjutkan Pembayaran';
    }
});
</script>
@endpush
@endsection
