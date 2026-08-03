<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dapur - {{ substr($order->order_number ?? $order->invoice_number ?? '', -4) }}</title>
    <style>
        @page { margin: 0; size: 80mm auto; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', 'Lucida Console', monospace;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
            width: 72mm;
            margin: 0 auto;
            padding: 6mm 4mm;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider-dash { border: none; border-top: 1px dashed #555; margin: 5px 0; }
        .divider-solid { border: none; border-top: 2px solid #000; margin: 5px 0; }

        .store-name { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .store-logo { max-height: 36px; margin-bottom: 3px; }
        .branch-name { font-size: 10px; font-weight: bold; margin-top: 1px; }

        .badge-pesanan { font-size: 13px; font-weight: bold; letter-spacing: 3px; color: #c00; }

        .queue-number { font-size: 22px; font-weight: bold; letter-spacing: 2px; margin: 2px 0; }
        .order-number { font-size: 12px; font-weight: bold; letter-spacing: 0.5px; margin-top: 1px; }
        .order-time { font-size: 10px; color: #555; margin-top: 1px; }

        .customer-name { font-size: 12px; font-weight: bold; }

        .cat-label { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: #666; padding: 4px 0 2px; }
        .item-block { padding: 4px 0; border-bottom: 1px dotted #ccc; }
        .item-block:last-child { border-bottom: none; }
        .item-qty { font-size: 14px; font-weight: bold; }
        .item-name { font-size: 13px; font-weight: bold; }

        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #666; margin-bottom: 2px; }
        .note-text { font-size: 11px; font-style: italic; margin-top: 1px; }

        .footer-action { font-size: 14px; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

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

    {{-- Items grouped by category --}}
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
        @if (($settings['receipt_show_prices'] ?? true) && $order->items->sum('subtotal') > 0)
            | Rp{{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}
        @endif
    </div>

    @if ($order->notes)
        <hr class="divider-dash">
        <div class="section-title">Catatan Pesanan</div>
        <div class="note-text">{{ $order->notes }}</div>
    @endif

    @if (!empty($settings['receipt_kitchen_note']))
        <hr class="divider-dash">
        <div class="section-title">Instruksi Dapur</div>
        <div class="note-text">{{ $settings['receipt_kitchen_note'] }}</div>
    @endif

    @if ($branch && $branch->address)
        <hr class="divider-dash">
        <div style="font-size:9px;color:#888;text-align:center;">{{ $branch->address }}</div>
    @endif

    <hr class="divider-solid">

    <div class="center footer-action">SEGERA DIPROSES</div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
            setTimeout(function() { window.close(); }, 3000);
        };
    </script>
</body>
</html>
