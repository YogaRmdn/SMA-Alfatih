<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasRole(...$roles)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
