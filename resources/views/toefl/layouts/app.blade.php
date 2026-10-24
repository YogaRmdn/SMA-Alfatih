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
    <title>@yield('title', 'TOEFL ITP Level 1') — {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">
<div class="flex min-h-screen flex-col">

    {{-- Header --}}
    <header class="no-print sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-14 w-full max-w-6xl items-center justify-between gap-3 px-4">
            <a href="{{ route('toefl.portal') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white ring-1 ring-indigo-300/40">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold tracking-wide text-slate-800">TOEFL ITP Level 1</span>
                    <span class="block text-[11px] font-normal text-slate-500">Latihan Ujian Online</span>
                </span>
            </a>

            <div class="flex items-center gap-2">
                @hasSection('timer')
                    @yield('timer')
                @endif

                @if (session('toefl_attempt_id') || session('toefl_student_id'))
                    @unless (request()->routeIs('toefl.portal'))
                        <a href="{{ route('toefl.portal') }}"
                           class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                            Portal
                        </a>
                    @endunless

                    @if (request()->routeIs('toefl.exam.*', 'toefl.instructions'))
                        <form method="POST" action="{{ route('toefl.leave') }}" class="hidden sm:block"
                              onsubmit="return confirm('Lepas sesi ujian dari perangkat ini? Jawaban tersimpan tetap aman.');">
                            @csrf
                            <button type="submit" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                                Keluar Sesi
                            </button>
                        </form>
                    @endif
                @elseif (auth()->check() && auth()->user()->hasRole('super_admin', 'admin', 'admin_toefl'))
                    <a href="{{ route('toefl.admin.dashboard') }}"
                       class="rounded-lg bg-indigo-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-800">
                        Panel Pengelola
                    </a>
                @endif
            </div>
        </div>
    </header>

    {{-- Konten --}}
    <main class="w-full flex-1">
        @yield('content')
    </main>

    <footer class="no-print border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-500">
        Ujian Latihan TOEFL ITP Level 1 &copy; {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }} — skor bersifat latihan, bukan skor resmi ETS.
    </footer>
</div>

<x-flash-toasts />
@stack('scripts')
</body>
</html>
