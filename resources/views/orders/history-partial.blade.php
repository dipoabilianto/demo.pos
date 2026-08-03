@if ($orders->total() > 0 || request()->anyFilled(['search', 'date_from', 'date_to', 'payment_method', 'payment_status', 'order_status']))
    <div class="text-xs text-stone-400 mb-3">
        @if ($orders->total() > 0)
            Menampilkan {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
        @else
            Tidak ada pesanan yang cocok dengan filter
        @endif
    </div>
@endif

@forelse ($orders as $order)
    <div class="animate-stagger bg-white rounded-2xl shadow-sm ring-1 ring-stone-100 mb-3 sm:mb-4 overflow-hidden" data-order-id="{{ $order->id }}" style="animation-delay: {{ $loop->index * 0.04 }}s;">
        <button class="w-full px-5 py-3.5 flex items-center justify-between gap-3 hover:bg-stone-50/50 transition-colors text-left toggle-trigger">
            <div class="flex items-center gap-3 min-w-0">
                <div class="shrink-0 flex h-8 w-8 items-center justify-center rounded-lg bg-theme-primary/10 text-theme-primary">
                    <svg class="h-4 w-4 transition-transform duration-200" :class="{'rotate-90': open}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        @if ($order->queue_number)
                            <span class="inline-flex items-center justify-center min-w-[32px] h-[24px] rounded-md bg-stone-800 text-white text-[10px] font-bold px-1.5">#{{ str_pad($order->queue_number, 3, '0', STR_PAD_LEFT) }}</span>
                        @endif
                        <div class="text-sm font-semibold text-stone-900 truncate">
                            {{ $order->order_number }}
                            @if (str_starts_with($order->order_number, 'ORDON-') && !$order->seen_at)
                                <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 text-[10px] font-bold px-1.5 py-0.5 ml-1 align-middle">Baru</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-xs font-bold italic text-stone-700">{{ $order->customer_name }}</div>
                    <div class="text-xs text-stone-400">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-sm font-bold text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                @php
                    $ps = $order->payment_status;
                    $pColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'success' => 'bg-emerald-100 text-emerald-800', 'expired' => 'bg-red-100 text-red-800', 'failed' => 'bg-red-100 text-red-800', 'paid' => 'bg-emerald-100 text-emerald-800'];
                    $pLabels = ['pending' => 'Belum Dibayar', 'success' => 'Lunas', 'expired' => 'Kadaluwarsa', 'failed' => 'Gagal', 'paid' => 'Lunas'];
                @endphp
                @if (in_array($ps, ['success', 'paid']))
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold italic {{ $pColors[$ps] ?? 'bg-stone-100 text-stone-600' }}">{{ $pLabels[$ps] ?? $ps }} · {{ $order->payment_method === 'cash' ? 'Tunai' : ($order->payment_method === 'transfer' ? 'Transfer' : 'Online') }}</span>
                @else
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $pColors[$ps] ?? 'bg-stone-100 text-stone-600' }}">{{ $pLabels[$ps] ?? $ps }}</span>
                @endif
            </div>
        </button>

        <div class="border-t border-stone-100 overflow-hidden transition-all duration-200" style="max-height: 0;">
            <div class="px-5 py-4 space-y-3">
                <div class="flex items-center gap-2 text-xs">
                    @php
                        $oColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'processing' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-red-100 text-red-800', 'confirmed' => 'bg-blue-100 text-blue-800'];
                        $oLabels = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'completed' => 'Selesai', 'cancelled' => 'Batal', 'confirmed' => 'Dikonfirmasi'];
                    @endphp
                    <span class="font-medium text-stone-500">Status:</span>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 font-semibold {{ $oColors[$order->order_status] ?? 'bg-stone-100 text-stone-600' }}">{{ $oLabels[$order->order_status] ?? $order->order_status }}</span>

                    @if (!$order->processed_by && $order->order_status !== 'cancelled' && $order->order_status !== 'completed' && !in_array($ps, ['pending']))
                        <form action="{{ route('orders.process', $order) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg bg-theme-primary px-3.5 py-1.5 text-xs font-bold text-white hover:opacity-90 active:scale-95 shadow-sm transition-all">Proses</button>
                        </form>
                    @endif

                    @if ($order->processed_by && $order->order_status !== 'completed' && $order->order_status !== 'cancelled')
                        <form action="{{ route('orders.complete', $order) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg ring-1 ring-inset ring-theme-primary text-theme-primary bg-white px-3.5 py-1.5 text-xs font-bold hover:bg-theme-primary/10 active:scale-95 transition-all">Selesai</button>
                        </form>
                    @endif

                    @if (!in_array($ps, ['paid', 'success']) && $order->order_status !== 'cancelled' && $order->order_status !== 'completed')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                            @csrf
                            <button type="submit" class="rounded-lg ring-1 ring-inset ring-rose-300 text-rose-600 bg-white px-3.5 py-1.5 text-xs font-bold hover:bg-rose-50 active:scale-95 transition-all">Batal</button>
                        </form>
                    @endif
                </div>

                <div class="space-y-1">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-600">{{ $item->product_name }} <span class="text-stone-400">×{{ $item->quantity }}</span></span>
                            <span class="font-medium text-stone-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <hr class="border-stone-100">

                <div class="space-y-0.5 text-sm">
                    <div class="flex justify-between text-stone-500">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-stone-500">
                        <span>Diskon</span>
                        <span>{{ $order->discount > 0 ? 'Rp ' . number_format($order->discount, 0, ',', '.') : 'Rp 0' }}</span>
                    </div>
                    @php $vc = $order->voucher->code ?? $order->voucher_code ?? null; @endphp
                    @if ($vc)
                        <div class="flex justify-between text-stone-400 text-[11px]">
                            <span>Voucher</span>
                            <span class="font-mono">{{ $vc }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-stone-900 pt-1 border-t border-stone-200">
                        <span>Total</span>
                        <span class="text-theme-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="text-xs text-stone-400">
                    {{ $order->customer_name }}@if($order->customer_phone) · {{ $order->customer_phone }}@endif
                </div>

                <div class="flex items-center justify-between pt-1 gap-2">
                    <a href="{{ route('orders.show', $order) }}" class="text-sm font-medium text-theme-primary hover:text-theme-primary transition-colors">Detail &rarr;</a>
                    <div class="flex items-center gap-2">
                        @if (!in_array($ps, ['success', 'paid', 'failed', 'expired']) && $order->order_status !== 'cancelled')
                            <a href="{{ route('orders.payment', $order) }}" class="rounded-lg bg-theme-primary px-4 py-1.5 text-xs font-semibold text-white hover:opacity-90 transition-colors">Bayar</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="text-center py-20 animate-fadeUp">
        <div class="inline-flex h-24 w-24 items-center justify-center rounded-full bg-theme-primary/10 mb-6">
            <svg class="h-12 w-12 text-theme-primary" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.801 0a2.25 2.25 0 01-1.545-1.578A2.25 2.25 0 0012 2.25h-1.5a2.25 2.25 0 00-2.18 2.622 2.25 2.25 0 01-1.545 2.578 2.25 2.25 0 000 1.5A2.25 2.25 0 007.5 10.5h1.5a2.25 2.25 0 002.18-2.622 2.25 2.25 0 011.545-1.28M12 12.75h3.75m-3.75 3h3.75m-4.473-7.5H8.25" /></svg>
        </div>
        <h2 class="text-xl font-bold text-stone-900 mb-2">Belum Ada Pesanan</h2>
        <p class="text-sm text-stone-500 max-w-xs mx-auto mb-6">Kamu belum melakukan pemesanan apapun. Yuk, pesan roti favoritmu sekarang!</p>
        <a href="{{ route('orders.catalog') }}" class="inline-flex items-center gap-2 rounded-xl bg-theme-gradient-r px-6 py-3 text-sm font-bold text-white shadow-lg shadow-theme-shadow hover:shadow-xl hover:shadow-theme-shadow hover:opacity-90 active:scale-95 transition-all duration-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.25v2.5c0 .777.845 1.74 1.976 1.834a30.633 30.633 0 00-1.614 4.481m0 0A2.25 2.25 0 007.5 21h9a2.25 2.25 0 002.138-2.935M6.362 17.25l-1.388 4.149m11.026-4.149l1.388 4.149" /></svg>
            Mulai Pesan
        </a>
    </div>
@endforelse

@if ($orders->hasPages())
    <div class="mt-8">
        {{ $orders->links() }}
    </div>
@endif
