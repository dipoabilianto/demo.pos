<!DOCTYPE html>
<html lang="id">
@inject('settings', 'App\Http\Controllers\SettingsController')
@php $siteSettings = $settings::getSettings(); @endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: #faf8f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .bg-shapes { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.5;
        }
        .shape-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(251,191,36,0.12), transparent 70%);
            top: -150px; right: -100px;
            animation: floatShape 8s ease-in-out infinite;
        }
        .shape-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(245,158,11,0.1), transparent 70%);
            bottom: -100px; left: -100px;
            animation: floatShape 10s ease-in-out infinite reverse;
        }
        .shape-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(217,119,6,0.08), transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: pulseShape 6s ease-in-out infinite;
        }
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }
        @keyframes pulseShape {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.15); }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 520px;
        }

        .code {
            font-size: clamp(7rem, 20vw, 12rem);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #d97706, #f59e0b, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: fadeScale 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            position: relative;
            display: inline-block;
        }
        .code::after {
            content: '';
            position: absolute;
            inset: -20px -40px;
            background: radial-gradient(circle, rgba(251,191,36,0.15), transparent 70%);
            border-radius: 50%;
            z-index: -1;
            animation: pulseGlow 3s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.2); opacity: 1; }
        }
        @keyframes fadeScale {
            from { opacity: 0; transform: scale(0.7) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .icon-row {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0 2rem;
        }
        .icon-row svg {
            width: 40px;
            height: 40px;
            color: #d6d3d1;
            animation: bobIcon 2s ease-in-out infinite;
        }
        .icon-row svg:nth-child(1) { animation-delay: 0s; }
        .icon-row svg:nth-child(2) { animation-delay: 0.3s; }
        .icon-row svg:nth-child(3) { animation-delay: 0.6s; }
        .icon-row svg:nth-child(4) { animation-delay: 0.9s; }
        @keyframes bobIcon {
            0%, 100% { transform: translateY(0); opacity: 0.4; }
            50% { transform: translateY(-8px); opacity: 0.8; }
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #292524;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.6s ease-out 0.2s both;
        }
        .desc {
            font-size: 0.95rem;
            color: #78716c;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s ease-out 0.35s both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .footer {
            margin-top: 3rem;
            font-size: 0.8rem;
            color: #a8a29e;
            animation: fadeUp 0.6s ease-out 0.65s both;
        }

        .particle {
            position: fixed;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #fbbf24;
            opacity: 0;
            pointer-events: none;
            animation: particleFloat 4s ease-out infinite;
        }
        @keyframes particleFloat {
            0% { opacity: 0; transform: translateY(0) scale(0); }
            20% { opacity: 0.6; }
            80% { opacity: 0.3; }
            100% { opacity: 0; transform: translateY(-120px) scale(1); }
        }
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="container">
        <div class="code">404</div>

        <div class="icon-row">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/></svg>
        </div>

        <h1 class="title">Halaman Tidak Ditemukan</h1>
        <p class="desc">
            Halaman yang Anda cari mungkin telah dipindahkan, dihapus,<br>
            atau tidak pernah ada. Yuk balik ke tempat yang aman.
        </p>

        <p class="footer" style="margin-top:1.5rem">&copy; {{ date('Y') }} {{ $siteSettings['store_name'] ?? 'Oribun Bakery' }} &mdash; {{ $siteSettings['store_description'] ?? 'Toko roti homemade dengan bahan-bahan berkualitas terbaik.' }}</p>
    </div>

    <script>
        for (let i = 0; i < 12; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = Math.random() * 100 + '%';
            p.style.top = 60 + Math.random() * 40 + '%';
            p.style.animationDelay = Math.random() * 4 + 's';
            p.style.animationDuration = (3 + Math.random() * 3) + 's';
            p.style.width = p.style.height = (2 + Math.random() * 4) + 'px';
            document.body.appendChild(p);
        }
    </script>
</body>
</html>
