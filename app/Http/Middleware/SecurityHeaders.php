<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    protected const DEV_ORIGINS = [
        'http://localhost:5173',
        'https://localhost:5173',
        'http://127.0.0.1:5173',
        'https://127.0.0.1:5173',
    ];

    protected const DEV_WS_ORIGINS = [
        'ws://localhost:5173',
        'wss://localhost:5173',
        'ws://127.0.0.1:5173',
        'wss://127.0.0.1:5173',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Content-Security-Policy', implode('; ', $this->csp()));

        return $response;
    }

    protected function csp(): array
    {
        $base = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            "img-src 'self' data: blob: https:",
            "connect-src 'self'",
            "frame-src 'self' https:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ];

        if (! app()->environment(['local', 'testing'])) {
            return $base;
        }

        if (! $this->viteDevServerRunning()) {
            return $base;
        }

        foreach ($base as $i => $directive) {
            if (str_starts_with($directive, 'script-src ') || str_starts_with($directive, 'style-src ') || str_starts_with($directive, 'connect-src ')) {
                $base[$i] .= ' ' . implode(' ', self::DEV_ORIGINS);
            }

            if (str_starts_with($directive, 'connect-src ')) {
                $base[$i] .= ' ' . implode(' ', self::DEV_WS_ORIGINS);
            }
        }

        return $base;
    }

    protected function viteDevServerRunning(): bool
    {
        $hotFile = config('vite.hotFile', public_path('hot'));

        return is_file($hotFile);
    }
}
