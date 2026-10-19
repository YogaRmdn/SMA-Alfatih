{{-- Layout autentikasi khusus SIBEL: kartu terpusat di atas gradien emerald.
     Terpisah dari tampilan login website (panel foto + form) agar identitas
     SIBEL terasa profesional dan berbeda. --}}
@props(['title' => 'Masuk'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" href="{{ asset_v('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_v('favicon-32x32.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset_v('apple-touch-icon.png') }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title }} — SIBEL SMA Alfatih</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-950 font-sans text-slate-700 antialiased">

        {{-- Latar dekoratif --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-teal-300/10 blur-3xl"></div>
        </div>

        <main class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10">

            {{-- Kepala branding --}}
            <a href="{{ route('home') }}" class="mb-6 flex flex-col items-center gap-3 text-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-lg shadow-emerald-950/30">
                    <svg class="h-9 w-9 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                </span>
                <span>
                    <span class="block text-2xl font-extrabold tracking-wide text-white">SIBEL</span>
                    <span class="mt-0.5 block text-sm font-medium text-emerald-100/80">Sistem Informasi Gaya Belajar Siswa</span>
                </span>
            </a>

            {{-- Kartu formulir --}}
            <div class="w-full max-w-md rounded-2xl border border-white/10 bg-white p-6 shadow-2xl shadow-emerald-950/40 sm:p-8">
                {{ $slot }}
            </div>

            {{-- Kaki halaman --}}
            <div class="mt-6 flex flex-col items-center gap-2 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-100/80 transition hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Beranda
                </a>
                <p class="text-xs text-emerald-200/60">&copy; {{ date('Y') }} SMA Alfatih</p>
            </div>
        </main>
    </body>
</html>
