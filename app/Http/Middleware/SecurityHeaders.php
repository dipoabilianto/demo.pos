<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        $csp = "default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; img-src 'self' data: https://images.unsplash.com; font-src 'self'; connect-src 'self' https://checkout.xendit.co https://checkout-staging.xendit.co; frame-src https://checkout.xendit.co https://checkout-staging.xendit.co; object-src 'none'; base-uri 'self'";

        if (file_exists(public_path('hot'))) {
            $hotUrl = trim(file_get_contents(public_path('hot')));
            $parsed = parse_url($hotUrl);
            $viteOrigin = $parsed['scheme'] . '://' . $parsed['host'] . ':' . $parsed['port'];
            $viteWsOrigin = 'wss://' . $parsed['host'] . ':' . $parsed['port'];
            $csp = str_replace(
                ["script-src 'self'", "style-src 'self'", "connect-src 'self'"],
                [
                    "script-src 'self' $viteOrigin",
                    "style-src 'self' $viteOrigin",
                    "connect-src 'self' $viteOrigin $viteWsOrigin",
                ],
                $csp
            );
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
