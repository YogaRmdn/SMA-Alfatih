<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->useBuiltAssetsForExternalHosts();

        View::composer('*', function ($view) {
            $view->with('settings', cache()->remember('site_settings', 3600, function () {
                return Setting::query()->pluck('value', 'key')->toArray();
            }));
        });

        View::composer('frontend.*', function ($view) {
            $view->with('contact', Contact::first());
        });
    }

    /**
     * Saat diakses lewat tunnel (ngrok) atau host eksternal lain, dev server Vite
     * (http://127.0.0.1:5173) tidak terjangkau sehingga CSS/JS hilang. Jika host
     * bukan localhost, matikan "hot" Vite supaya @vite memakai aset hasil build
     * (public/build) yang URL-nya relatif dan ikut tunnel.
     */
    protected function useBuiltAssetsForExternalHosts(): void
    {
        $host = strtolower((string) request()->header('Host'));

        $isExternal = !in_array(Str::before($host, ':'), ['localhost', '127.0.0.1', '[::1]'], true);

        if ($isExternal && file_exists(public_path('build/manifest.json'))) {
            Vite::useHotFile(storage_path('framework/vite-hot-external'));
        }
    }
}
