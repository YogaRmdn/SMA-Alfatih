<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePublicRegistrationIsEnabled
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless((bool) config('app.allow_public_registration'), 404);

        return $next($request);
    }
}
