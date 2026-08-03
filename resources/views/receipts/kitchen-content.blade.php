<div class="center">
    @if (!empty($settings['receipt_logo']))
        <img src="{{ asset('storage/' . $settings['receipt_logo']) }}" class="store-logo" alt="">
    @endif
    <div class="store-name">{{ $settings['store_name'] ?? 'Oribun Bakery' }}</div>
    @if ($branch ?? false)
        <div class="branch-name">{{ strtoupper($branch->name) }}</div>
    @endif
</div>

<hr class="divider-solid">

<div class="center" style="margin:2px 0;">
    <div class="badge-pesanan">✦ PESANAN BARU ✦</div>
</div>

<hr class="divider-dash">

<div class="center">
    @if ($order->queue_number ?? false)
        <div class="queue-number">#{{ $order->queue_number }}</div>
    @endif
    <div class="order-number">{{ $order->order_number ?? $order->invoice_number ?? '' }}</div>
    <div class="order-time">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
</div>

<hr class="divider-dash">

@if ($order->customer_name && $order->customer_name !== 'Walk-in Customer')
    <div class="center customer-name">
        Pemesan: {{ $order->customer_name }}
    </div>
    <hr class="divider-dash">
@endif

@php
    $grouped = $order->items->groupBy(fn($item) => $item->product?->category?->name ?? 'Lainnya');
@endphp

@if ($grouped->count() > 1)
    @foreach ($grouped as $category => $items)
        <div class="cat-label">── {{ strtoupper($category) }} ──</div>
        @foreach ($items as $item)
            <div class="item-block">
                <span class="item-qty">{{ $item->quantity }}x</span>
                <span class="item-name">{{ $item->product_name }}</span>
            </div>
        @endforeach
    @endforeach
@else
    @foreach ($order->items as $item)
        <div class="item-block">
            <span class="item-qty">{{ $item->quantity }}x</span>
            <span class="item-name">{{ $item->product_name }}</span>
        </div>
    @endforeach
@endif

<hr class="divider-dash">

<div style="font-size:11px;">
    <span class="bold">Total:</span>
    {{ $order->items->sum('quantity') }} item
</div>

@if ($order->notes)
    <hr class="divider-dash">
    <div class="section-title">Catatan Pesanan</div>
    <div class="note-text">{{ $order->notes }}</div>
@endif

@if ($branch && $branch->address)
    <hr class="divider-dash">
    <div style="font-size:9px;color:#888;text-align:center;">{{ $branch->address }}</div>
@endif

<hr class="divider-solid">

<div class="center footer-action">SEGERA DIPROSES</div>
