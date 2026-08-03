<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak - {{ config('app.name', 'Oribun Bakery') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 30%, #fffbeb 60%, #fce7f3 100%);
            padding: 1rem;
            overflow: hidden;
            position: relative;
        }

        .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: blobFloat 20s ease-in-out infinite;
            pointer-events: none;
        }
        .bg-blob:nth-child(1) {
            width: 400px; height: 400px;
            background: #f59e0b;
            top: -100px; left: -100px;
            animation-delay: 0s;
        }
        .bg-blob:nth-child(2) {
            width: 300px; height: 300px;
            background: #ec4899;
            bottom: -80px; right: -80px;
            animation-delay: -5s;
        }
        .bg-blob:nth-child(3) {
            width: 250px; height: 250px;
            background: #8b5cf6;
            top: 50%; left: 50%;
            animation-delay: -10s;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(20px, 30px) scale(1.05); }
        }

        .card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 2rem;
            padding: 3rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.15),
                inset 0 1px 2px rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .icon-wrap {
            width: 96px;
            height: 96px;
            margin: 0 auto 1.5rem;
            border-radius: 24px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            animation: iconPulse 2s ease-in-out infinite;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.2);
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(245, 158, 11, 0.2); }
            50% { transform: scale(1.05); box-shadow: 0 12px 32px rgba(245, 158, 11, 0.3); }
        }

        .lock-body {
            width: 28px;
            height: 22px;
            background: #92400e;
            border-radius: 4px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lock-body::before {
            content: '';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 18px;
            height: 14px;
            border: 3px solid #92400e;
            border-radius: 50% 50% 0 0;
            border-bottom: none;
        }
        .lock-keyhole {
            width: 6px;
            height: 6px;
            background: #fff;
            border-radius: 50%;
            position: relative;
        }
        .lock-keyhole::after {
            content: '';
            position: absolute;
            top: 5px;
            left: 50%;
            transform: translateX(-50%);
            width: 3px;
            height: 5px;
            background: #fff;
            border-radius: 0 0 2px 2px;
        }

        .code {
            font-size: 5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1c1917;
            margin-bottom: 0.5rem;
        }

        .message {
            color: #78716c;
            font-size: 0.925rem;
            line-height: 1.625;
            margin-bottom: 0.75rem;
        }

        .hint {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: #fef3c7;
            color: #92400e;
            margin-bottom: 1.5rem;
        }

        .user-info {
            font-size: 0.8rem;
            color: #a8a29e;
            margin-bottom: 1.5rem;
            padding: 0.5rem 1rem;
            background: rgba(0,0,0,0.02);
            border-radius: 12px;
            display: inline-block;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn:active { transform: scale(0.97); }

        .btn-primary {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: rgba(0,0,0,0.04);
            color: #44403c;
            border: 1px solid rgba(0,0,0,0.06);
        }
        .btn-outline:hover {
            background: rgba(0,0,0,0.08);
        }

        .particles {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #f59e0b;
            border-radius: 50%;
            animation: particleFloat 8s linear infinite;
            opacity: 0;
        }
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { left: 30%; animation-delay: -2s; width: 6px; height: 6px; background: #ec4899; }
        .particle:nth-child(3) { left: 50%; animation-delay: -4s; background: #8b5cf6; }
        .particle:nth-child(4) { left: 70%; animation-delay: -1s; width: 3px; height: 3px; }
        .particle:nth-child(5) { left: 90%; animation-delay: -3s; background: #10b981; }
        .particle:nth-child(6) { left: 20%; animation-delay: -6s; width: 5px; height: 5px; }
        .particle:nth-child(7) { left: 60%; animation-delay: -5s; }
        .particle:nth-child(8) { left: 80%; animation-delay: -7s; width: 3px; height: 3px; background: #ec4899; }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        .shimmer {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                105deg,
                transparent 30%,
                rgba(255,255,255,0.3) 45%,
                rgba(255,255,255,0.5) 50%,
                rgba(255,255,255,0.3) 55%,
                transparent 70%
            );
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        @media (max-width: 480px) {
            .card { padding: 2rem 1.5rem; }
            .code { font-size: 3.5rem; }
        }
    </style>
</head>
<body>
    <div class="bg-blob"></div>
    <div class="bg-blob"></div>
    <div class="bg-blob"></div>

    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="card">
        <div class="shimmer"></div>

        <div class="icon-wrap">
            <div class="lock-body">
                <div class="lock-keyhole"></div>
            </div>
        </div>

        <div class="code">403</div>
        <h1>Akses Tidak Diizinkan</h1>
        <p class="message">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>

        @auth
            <div class="user-info">
                {{ auth()->user()->name }} &middot;
                {{ auth()->user()->roles->pluck('label')->implode(', ') ?: 'Pengguna' }}
            </div>
        @endauth

        <div class="hint">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
            Hubungi admin jika Anda perlu akses
        </div>

        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Kembali
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Dashboard
            </a>
        </div>
    </div>
</body>
</html>
