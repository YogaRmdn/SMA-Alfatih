<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    @if (!empty($settings['favicon']))
        <link rel="icon" href="{{ img_url($settings['favicon'], 'favicon.ico') }}">
    @endif
    <title>@yield('title', 'Dashboard Kuesioner') — SIBEL {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Grafik: Chart.js dari CDN; bila tidak termuat, fallback grafik CSS milik server tetap tampil. --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">

{{-- Topbar siswa --}}
<header class="sticky top-0 z-30 border-b border-white/10 bg-gradient-to-r from-emerald-900 via-teal-800 to-emerald-950">
    <div class="mx-auto flex h-16 w-full max-w-5xl items-center justify-between gap-3 px-4">
        <a href="{{ route('sibel.portal') }}" class="flex items-center gap-2.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                <svg class="h-6 w-6 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
            </span>
            <span class="leading-tight">
                <span class="block text-sm font-extrabold tracking-wide text-white">SIBEL</span>
                <span class="block text-[11px] font-normal text-emerald-300">Gaya Belajar Siswa</span>
            </span>
        </a>

        <div class="flex items-center gap-2">
            @isset ($student)
                <span class="hidden items-center gap-2 rounded-lg bg-white/10 px-3 py-1.5 text-sm text-emerald-100 sm:flex">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span class="font-semibold text-white">{{ $student->full_name }}</span>
                    <span class="text-emerald-300">• {{ $student->schoolClass->name ?? 'Tanpa kelas' }}</span>
                </span>

                <form method="POST" action="{{ route('sibel.forget') }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-white/25 bg-white/5 px-3 py-2 text-xs font-bold text-emerald-100 transition hover:bg-white/15 hover:text-white">
                        Ganti Siswa
                    </button>
                </form>
            @endisset

            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-xs font-semibold text-emerald-200 transition hover:bg-white/10 hover:text-white">
                Website
            </a>
        </div>
    </div>
</header>

{{-- Konten --}}
<main class="mx-auto w-full max-w-5xl px-4 py-6 sm:py-8">
    @yield('content')
</main>

<footer class="border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-500">
    SIBEL — Sistem Informasi Gaya Belajar Siswa &copy; {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}.
</footer>

{{-- Renderer Chart.js dengan fallback aman bila CDN tidak termuat. --}}
<script>
    window.sibelCharts = window.sibelCharts || [];

    window.addEventListener('load', function () {
        if (typeof Chart === 'undefined') {
            return; // Grafik CSS milik server tetap ditampilkan.
        }

        window.sibelCharts.forEach(function (spec) {
            var canvas = document.getElementById(spec.target);
            if (!canvas || canvas.dataset.rendered) return;

            try {
                new Chart(canvas.getContext('2d'), {
                    type: spec.type,
                    data: spec.data,
                    options: spec.options || {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 16 } } }
                    }
                });
                canvas.dataset.rendered = '1';
                canvas.style.display = 'block';

                var fallback = document.getElementById(spec.target + '-fallback');
                if (fallback) fallback.style.display = 'none';
            } catch (e) {
                console.warn('SIBEL chart gagal dirender:', e);
            }
        });
    });
</script>

<x-flash-toasts />
@stack('scripts')
</body>
</html>
