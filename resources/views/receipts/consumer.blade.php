<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk - {{ substr($order->order_number ?? $order->invoice_number ?? '', -8) }}</title>
    @php $paperSize = ($settings['printer_paper_size'] ?? '58') === '80' ? 512 : 384; @endphp
    <style>
        @page { margin: 0; size: {{ $paperSize }}px auto; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', 'Lucida Console', monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            width: {{ $paperSize }}px;
            padding: 12px {{ $paperSize === 384 ? '8' : '16' }}px;
            margin: 0 auto;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .divider-dash { border: none; border-top: 1px dashed #000; margin: 6px 0; }
        .divider-solid { border: none; border-top: 2px solid #000; margin: 6px 0; }
        .divider-thin { border: none; border-top: 1px solid #888; margin: 4px 0; }

        .store-logo { max-height: 48px; margin-bottom: 4px; }
        .store-name { font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .store-info { font-size: 10px; color: #555; }

        .order-number { font-size: 12px; font-weight: bold; letter-spacing: 0.5px; }
        .order-time { font-size: 10px; color: #555; }

        .meta-row { display: flex; justify-content: space-between; font-size: 10px; padding: 1px 0; }
        .meta-label { color: #888; }
        .meta-value { font-weight: 600; text-align: right; max-width: 65%; }

        .item-header { font-size: 9px; font-weight: bold; text-transform: uppercase; color: #555; padding: 2px 0; }
        .item-row { padding: 3px 0; }
        .item-name { font-size: 11px; font-weight: 600; }
        .item-meta { font-size: 10px; color: #666; }
        .item-total { font-size: 11px; font-weight: bold; text-align: right; white-space: nowrap; }
        .item-col-name { width: 65%; vertical-align: top; padding-right: 4px; }
        .item-col-qty { width: 15%; text-align: center; vertical-align: top; font-weight: 600; }
        .item-col-price { width: 20%; text-align: right; vertical-align: top; font-weight: 600; white-space: nowrap; }

        .summary-row { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; }
        .summary-row.total { font-size: 14px; font-weight: bold; padding-top: 4px; }
        .summary-row.total .summary-value { color: #000; }
        .summary-label { color: #444; }
        .summary-value { font-weight: 600; }
        .summary-value.discount { color: #c00; }
        .summary-value.paid { color: #2a7; }

        .payment-row { display: flex; justify-content: space-between; font-size: 10px; padding: 1px 0; }

        .footer { text-align: center; font-size: 10px; color: #666; padding-top: 4px; }
        .footer .thanks { font-size: 11px; font-weight: bold; color: #333; }
        .footer-icon { font-size: 9px; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="center">
        @if (!empty($settings['receipt_logo']))
            <img src="{{ asset('storage/' . $settings['receipt_logo']) }}" class="store-logo" alt="">
        @endif
        <div class="store-name">{{ $settings['store_name'] ?? 'Oribun Bakery' }}</div>
        @if (!empty($settings['store_address']))
            <div class="store-info">{{ $settings['store_address'] }}</div>
        @endif
        @if (!empty($settings['store_phone']))
            <div class="store-info">Telp: {{ $settings['store_phone'] }}</div>
        @endif
    </div>

    <hr class="divider-solid">

    {{-- Order Info --}}
    <div class="center">
        <div class="order-number">{{ $order->order_number ?? $order->invoice_number ?? '' }}</div>
        @if ($order->queue_number ?? false)
            <div style="font-size:13px;font-weight:bold;margin-top:2px;">Antrian #{{ $order->queue_number }}</div>
        @endif
        <div class="order-time">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
    </div>

    <hr class="divider-dash">

    {{-- Customer & Cashier --}}
    @if ($order->customer_name || $order->customer_phone || $order->user)
    <div style="padding:2px 0;">
        @if ($order->customer_name)
            <div class="meta-row">
                <span class="meta-label">Pelanggan</span>
                <span class="meta-value">{{ $order->customer_name }}</span>
            </div>
        @endif
        @if ($order->customer_phone)
            <div class="meta-row">
                <span class="meta-label">Telepon</span>
                <span class="meta-value">{{ $order->customer_phone }}</span>
            </div>
        @endif
        @if ($order->user)
            <div class="meta-row">
                <span class="meta-label">Kasir</span>
                <span class="meta-value">{{ $order->user->name }}</span>
            </div>
        @endif
    </div>
    <hr class="divider-dash">
    @endif

    {{-- Items --}}
    <table style="width:100%;border-collapse:collapse;">
        <tr class="item-header">
            <td class="item-col-name">Menu</td>
            <td class="item-col-qty">Qty</td>
            <td class="item-col-price">Total</td>
        </tr>
        @foreach ($order->items as $item)
        <tr class="item-row">
            <td class="item-col-name">
                <div class="item-name">{{ $item->product_name }}</div>
                <div class="item-meta">@ Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            </td>
            <td class="item-col-qty">{{ $item->quantity }}</td>
            <td class="item-col-price">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <hr class="divider-thin">

    {{-- Summary --}}
    <div>
        <div class="summary-row">
            <span class="summary-label">Subtotal</span>
            <span class="summary-value">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
        </div>

        @if (($order->discount ?? 0) > 0)
        <div class="summary-row">
            <span class="summary-label">
                Diskon
                @if ($order->voucher_code)
                    <span style="font-size:9px;color:#888;">({{ $order->voucher_code }})</span>
                @endif
            </span>
            <span class="summary-value discount">-Rp{{ number_format($order->discount, 0, ',', '.') }}</span>
        </div>
        @endif

        @if (($settings['tax_enabled'] ?? false) && ($order->tax ?? 0) > 0)
        <div class="summary-row">
            <span class="summary-label">
                {{ $settings['tax_name'] ?? 'PPN' }}
                @if (!empty($settings['tax_rate']))
                    <span style="font-size:9px;color:#888;">({{ $settings['tax_rate'] }}%)</span>
                @endif
            </span>
            <span class="summary-value">Rp{{ number_format($order->tax, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="summary-row total">
            <span>TOTAL</span>
            <span class="summary-value paid">Rp{{ number_format($order->total, 0, ',', '.') }}</span>
        </div>


        {{-- Payment Method --}}
        <hr class="divider-thin">
        <div class="payment-row">
            <span class="meta-label">Pembayaran</span>
            <span class="summary-value" style="text-transform:uppercase;font-size:10px;">
                @php
                    $payLabel = match($order->payment_method) {
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer Bank',
                        default => $order->transaction?->payment_channel
                            ? \Illuminate\Support\Str::upper($order->transaction->payment_channel)
                            : \Illuminate\Support\Str::upper($order->payment_method),
                    };
                @endphp
                {{ $payLabel }}
            </span>
        </div>
    </div>

    {{-- Notes --}}
    @if ($order->notes)
    <hr class="divider-dash">
    <div style="font-size:10px;color:#555;">
        <span class="bold">Catatan:</span>
        <p style="margin-top:1px;font-style:italic;">{{ $order->notes }}</p>
    </div>
    @endif

    <hr class="divider-solid">

    {{-- Footer --}}
    <div class="footer">
        <div class="thanks">
            {{ !empty($settings['receipt_footer_note']) ? $settings['receipt_footer_note'] : 'Terima kasih telah berbelanja.' }}
        </div>

        @if (!empty($settings['store_hours']))
            <div class="footer-icon">🕐 {{ $settings['store_hours'] }}</div>
        @endif
        @if (!empty($settings['store_instagram']))
            <div class="footer-icon">📷 {{ $settings['store_instagram'] }}</div>
        @endif
        @if (!empty($settings['store_whatsapp']))
            <div style="margin-top:2px;">WA: {{ $settings['store_whatsapp'] }}</div>
        @endif
        @if (!empty($settings['store_email']))
            <div>{{ $settings['store_email'] }}</div>
        @endif
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
            setTimeout(function() { window.close(); }, 5000);
        };
    </script>
</body>
</html>
