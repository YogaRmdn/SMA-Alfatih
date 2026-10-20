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
    <title>@yield('title', 'Dashboard') — SIBEL {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Grafik: Chart.js dari CDN; bila tidak termuat, fallback grafik CSS milik server tetap tampil. --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">
<div x-data="{ sidebarOpen: false, userMenu: false }" @click.outside="userMenu = false" class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-gradient-to-b from-emerald-900 via-emerald-900 to-teal-950 text-white transition-transform duration-300 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : ''">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
            <a href="{{ route('sibel.dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                    <svg class="h-6 w-6 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6m4 6V7m4 10v-3M3 21h18M5 21V10l7-6 7 6v11" /></svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold tracking-wide">SIBEL</span>
                    <span class="block text-[11px] font-normal text-emerald-300">Gaya Belajar Siswa</span>
                </span>
            </a>
            <button @click="sidebarOpen = false" class="rounded-lg p-1 text-emerald-300 hover:bg-white/10 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        @php
            $sibelUser = auth()->user();
            $isManager = $sibelUser && $sibelUser->hasRole('super_admin', 'admin', 'guru_bk');

            $managerMenu = [
                ['label' => 'Dashboard', 'route' => 'sibel.dashboard', 'match' => 'sibel.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10'],
                ['label' => 'Data Siswa', 'route' => 'sibel.students.index', 'match' => 'sibel.students.*', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Kelas', 'route' => 'sibel.classes.index', 'match' => 'sibel.classes.*', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'Kuesioner', 'route' => 'sibel.questionnaires.index', 'match' => 'sibel.questionnaires.*', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label' => 'Hasil Asesmen', 'route' => 'sibel.results.index', 'match' => 'sibel.results.*', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['label' => 'Rekap Kelas', 'route' => 'sibel.recap.index', 'match' => 'sibel.recap.*', 'icon' => 'M9 17v-6m4 6V7m4 10v-3M3 21h18M5 21V10l7-6 7 6v11'],
            ];
        @endphp

        <nav class="h-[calc(100vh-4rem)] overflow-y-auto px-3 py-4 space-y-1">
            @if ($isManager)
                <p class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-emerald-400">Menu Guru BK & Admin</p>
                @foreach ($managerMenu as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($item['match']) ? 'bg-white/15 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif

            <div class="mt-4 border-t border-white/10 pt-3">
                @if ($isManager)
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-200 transition-colors hover:bg-white/10 hover:text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                        Panel Admin
                    </a>
                @endif
                <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-200 transition-colors hover:bg-white/10 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6a1 1 0 011-1h2a1 1 0 011 1v6h4a1 1 0 001-1V10" /></svg>
                    Website Sekolah
                </a>
            </div>
        </nav>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-cloak></div>

    {{-- Main --}}
    <div class="flex min-h-screen w-full min-w-0 flex-1 flex-col lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-6">
            <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>

            <div class="hidden items-center gap-2 text-sm text-slate-500 md:flex">
                <span class="font-semibold text-emerald-700">SIBEL</span>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                <span class="font-medium text-slate-700">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <div class="relative">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-2 rounded-lg p-1.5 transition hover:bg-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-emerald-700 text-sm font-bold text-white">
                            @if (auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            @endif
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="block text-[12px] text-slate-500">{{ auth()->user()->role->name ?? '-' }}</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </button>

                    <div x-show="userMenu" x-cloak x-transition
                         class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                        <div class="flex items-center gap-3 border-b border-slate-100 px-3 py-2">
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-emerald-700 text-sm font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            </span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="mt-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-slate-100 pt-1">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @if (!empty($breadcrumbs))
            <nav class="flex items-center gap-2 border-b border-slate-200 bg-white px-4 py-2.5 text-xs text-slate-500 lg:px-6">
                <a href="{{ route('sibel.dashboard') }}" class="transition hover:text-emerald-700">SIBEL</a>
                @foreach ($breadcrumbs as $crumb)
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    <span class="{{ $loop->last ? 'font-semibold text-emerald-700' : '' }}">{{ $crumb }}</span>
                @endforeach
            </nav>
        @endif

        <main class="w-full min-w-0 flex-1 bg-gradient-to-br from-emerald-100/50 via-slate-50/80 to-amber-100/50 px-4 py-6 lg:px-6">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-500 lg:px-6">
            SIBEL — Sistem Informasi Gaya Belajar Siswa &copy; {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}.
        </footer>
    </div>
</div>

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
<x-admin.confirm-modal />
@stack('scripts')
</body>
</html>
