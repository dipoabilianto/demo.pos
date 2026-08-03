<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Laporan')</title>
    @php $orientation = request('orientation', 'portrait'); @endphp
    <style>
        @page { size: A4 {{ $orientation }}; margin: 15mm 20mm 20mm 20mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: {{ $orientation === 'landscape' ? '8pt' : '10pt' }}; color: #000; line-height: 1.4; margin: 0; padding: 0; }
        body.landscape { font-size: 8pt; }
        body.portrait { font-size: 10pt; }
        .report-header { text-align: center; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #000; }
        .report-header h1 { font-size: 14pt; margin: 4px 0; text-transform: uppercase; letter-spacing: 1px; }
        .report-header h2 { font-size: 11pt; margin: 2px 0; font-weight: normal; }
        .report-header .store-name { font-size: 16pt; font-weight: bold; margin-bottom: 2px; }
        .report-header .period { font-size: 9pt; color: #555; }
        .summary-card { width: 100%; border: 1px solid #000; padding: 10px 14px; margin-bottom: 16px; }
        .summary-card .row { display: flex; justify-content: space-between; padding: 2px 0; font-size: 9pt; }
        .summary-card .total-row { font-weight: bold; font-size: 11pt; border-top: 1px solid #000; padding-top: 4px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 9pt; margin-bottom: 12px; }
        th, td { padding: 5px 6px; border: 1px solid #444; text-align: left; }
        th { background: #e0e0e0; font-weight: 600; font-size: 8pt; text-transform: uppercase; }
        td.right, th.right { text-align: right; }
        td.center, th.center { text-align: center; }
        tfoot td { font-weight: bold; border-top: 2px solid #000; }
        .report-footer { text-align: center; font-size: 8pt; color: #888; margin-top: 20px; padding-top: 8px; border-top: 1px solid #ccc; }
        .page-break { page-break-before: always; }
        .no-print { display: none; }
        .status-badge { padding: 1px 6px; border: 1px solid #000; font-size: 7pt; text-transform: uppercase; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
            tr { page-break-inside: avoid; }
            .page-break { page-break-before: always; }
        }
    </style>
    @stack('styles')
</head>
<body class="{{ $orientation }}">
    <div class="report-header">
        <div class="store-name">{{ $settings['store_name'] ?? 'Oribun Bakery' }}</div>
        <h1>@yield('title', 'LAPORAN')</h1>
        <h2>@yield('subtitle', '')</h2>
        <div class="period">
            @if (request('from') && request('to'))
                Periode: {{ \Carbon\Carbon::parse(request('from'))->format('d/m/Y') }} — {{ \Carbon\Carbon::parse(request('to'))->format('d/m/Y') }}
            @elseif (request('from'))
                Periode: {{ \Carbon\Carbon::parse(request('from'))->format('d/m/Y') }} — sekarang
            @else
                Semua data
            @endif
        </div>
        @php
            $printBranchId = session('branch_id');
            $printBranch = $printBranchId ? \App\Models\Branch::find($printBranchId) : null;
        @endphp
        <div style="font-size:9pt;color:#555;margin-top:2px;">
            {{ $printBranch ? 'Cabang: ' . $printBranch->name : 'Semua Cabang' }}
        </div>
    </div>

    @yield('content')

    <div class="report-footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} | {{ $settings['store_name'] ?? '' }}
    </div>
</body>
</html>
