<?php

namespace Tests\Feature;

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    protected string $hotFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hotFile = sys_get_temp_dir() . '/security-headers-test-hot-' . uniqid();

        config()->set('vite.hotFile', $this->hotFile);
    }

    public function test_default_security_headers_are_present(): void
    {
        Route::get('/security-headers-test', fn () => response('ok'));

        $response = $this->get('/security-headers-test');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
    }

    public function test_csp_does_not_include_vite_dev_origins_when_no_hot_file(): void
    {
        $csp = $this->middlewareCsp(false);

        $this->assertStringNotContainsString('localhost:5173', $csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("'unsafe-eval'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
    }

    public function test_csp_includes_vite_dev_origins_when_hot_file_exists(): void
    {
        $csp = $this->middlewareCsp(true);

        $this->assertStringContainsString('http://localhost:5173', $csp);
        $this->assertStringContainsString('http://127.0.0.1:5173', $csp);
        $this->assertStringContainsString('ws://127.0.0.1:5173', $csp);
        $this->assertStringNotContainsString('[::1]', $csp);
    }

    protected function middlewareCsp(bool $hotExists): string
    {
        if ($hotExists) {
            file_put_contents($this->hotFile, 'http://localhost:5173');
        } else {
            @unlink($this->hotFile);
        }

        $middleware = new SecurityHeaders;
        $response = $middleware->handle(
            Request::create('/', 'GET'),
            fn () => new Response('ok')
        );

        return $response->headers->get('Content-Security-Policy', '');
    }

    protected function tearDown(): void
    {
        @unlink($this->hotFile);

        parent::tearDown();
    }
}