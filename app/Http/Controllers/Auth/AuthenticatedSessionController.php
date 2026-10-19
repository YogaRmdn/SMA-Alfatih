<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Pisahkan area: tujuan "intended" yang tidak berhak diakses user ini
        // dibuang, supaya akun siswa tidak pernah dilempar ke /admin atau
        // halaman pengelola SIBEL (dan sebaliknya).
        $intended = (string) session('url.intended');
        if ($intended !== '' && ! $this->mayReach($request->user(), $intended)) {
            session()->forget('url.intended');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Bolehkah user ini diarahkan ke tujuan intended tersebut?
     * Halaman publik selalu boleh; area panel difilter per scope role.
     */
    private function mayReach(User $user, string $intended): bool
    {
        $path = parse_url($intended, PHP_URL_PATH) ?: '/';

        $isPanel = str_starts_with($path, '/admin') || str_starts_with($path, '/sibel');
        if (! $isPanel) {
            return true;
        }

        if ($user->hasRole('super_admin', 'admin')) {
            return true;
        }

        if ($user->hasRole('guru_bk')) {
            return ! str_starts_with($path, '/admin');
        }

        // Siswa: hanya area pengisian miliknya sendiri.
        return (bool) preg_match('#^/sibel/(kuesioner-saya|asesmen)(/|$)#', $path);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
