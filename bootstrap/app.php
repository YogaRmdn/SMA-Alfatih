<?php

use App\Http\Middleware\EnsurePublicRegistrationIsEnabled;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'public-registration' => EnsurePublicRegistrationIsEnabled::class,
        ]);

        $middleware->append(SecurityHeaders::class);

        $trustedProxies = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TRUSTED_PROXIES', '127.0.0.1'))
        )));

        if ($trustedProxies !== []) {
            $middleware->trustProxies(at: $trustedProxies);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
