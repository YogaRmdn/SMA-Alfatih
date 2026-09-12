<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $settings['site_description'] ?? '' }}">
    <meta name="keywords" content="{{ $settings['meta_keywords'] ?? '' }}">
    <link rel="icon" href="{{ img_url($settings['favicon'] ?? null, 'img/Logo SMK fix 4.png') }}">
    <title>@yield('title') — {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee { animation: marquee 30s linear infinite; }
        @media (prefers-reduced-motion: reduce) {
            .animate-marquee { animation: none; }
        }
    </style>
</head>
<body class="min-h-screen bg-white font-sans text-slate-700 antialiased" x-data="{ mobileOpen: false }">

    @php
        $siteName = $settings['site_name'] ?? config('app.name');
        $address = $contact?->address ?? $settings['address'] ?? null;
        $phone = $contact?->phone ?? $settings['phone'] ?? null;
        $whatsapp = $contact?->whatsapp ?? $settings['whatsapp'] ?? null;
        $email = $contact?->email ?? $settings['email'] ?? null;
        $maps = $contact?->maps_embed ?? $settings['maps_embed'] ?? null;
        $waLink = $whatsapp ? 'https://wa.me/'.preg_replace('/\D+/', '', $whatsapp) : '#';
        $nav = [
            ['label' => 'Beranda', 'href' => '#beranda'],
            ['label' => 'Profil', 'href' => '#profil'],
            ['label' => 'Program', 'href' => '#program'],
            ['label' => 'Fasilitas', 'href' => '#fasilitas'],
            ['label' => 'Ekstrakurikuler', 'href' => '#ekskul'],
            ['label' => 'Prestasi', 'href' => '#prestasi'],
            ['label' => 'Berita', 'href' => '#berita'],
            ['label' => 'Kontak', 'href' => '#kontak'],
        ];
    @endphp

    {{-- Topbar --}}
    <div class="hidden bg-emerald-950 text-emerald-100 md:block">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 text-xs lg:px-6">
            <div class="flex items-center gap-6">
                <a href="mailto:{{ $email }}" class="flex items-center gap-1.5 transition hover:text-white">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    {{ $email }}
                </a>
                <a href="tel:{{ $phone }}" class="flex items-center gap-1.5 transition hover:text-white">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    {{ $phone }}
                </a>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $socials = [
                        'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z',
                        'facebook' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
                        'youtube' => 'M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
                        'twitter' => 'M24 4.557a9.83 9.83 0 01-2.828.775 4.932 4.932 0 002.165-2.724 9.864 9.864 0 01-3.127 1.195 4.916 4.916 0 00-8.384 4.482A13.944 13.944 0 011.67 3.149a4.916 4.916 0 001.5 6.562 4.9 4.9 0 01-2.229-.616c-.054 4.203 2.912 7.976 6.92 8.796a4.929 4.929 0 01-2.224.084c.627 1.956 2.445 3.379 4.6 3.419a9.87 9.87 0 01-6.115 2.107c-.397 0-.79-.023-1.175-.068a13.946 13.946 0 007.29 2.137c8.748 0 13.53-7.246 13.53-13.53 0-.207-.004-.415-.013-.621A9.66 9.66 0 0024 4.557z',
                    ];
                @endphp
                @foreach (['instagram', 'facebook', 'youtube', 'twitter'] as $key)
                    @if (!empty($settings[$key]))
                        <a href="{{ $settings[$key] }}" target="_blank" rel="noopener" class="text-emerald-300 transition hover:text-white" aria-label="{{ ucfirst($key) }}">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $socials[$key] }}" /></svg>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Navbar --}}
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 lg:h-20 lg:px-6">
            <a href="#beranda" class="flex shrink-0 items-center gap-3">
                <img src="{{ img_url($settings['logo'] ?? null, 'img/Logo SMK fix 4.png') }}" alt="{{ $siteName }}" class="h-11 w-11 rounded-xl object-contain lg:h-13 lg:w-13">
                <span class="leading-tight">
                    <span class="block text-sm font-extrabold text-emerald-900 lg:text-base">{{ $siteName }}</span>
                    <span class="block text-[11px] font-medium text-amber-600">{{ $settings['site_tagline'] ?? 'Sekolah Islam Terpadu' }}</span>
                </span>
            </a>

            <nav class="ml-auto hidden items-center gap-1 xl:flex">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}" class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-800">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2 xl:ml-4">
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="hidden rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-emerald-950 shadow-sm transition hover:bg-amber-400 sm:inline-flex">
                    Daftar PPDB
                </a>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg border border-emerald-700 px-4 py-2.5 text-sm font-bold text-emerald-800 transition hover:bg-emerald-700 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                    Masuk Admin
                </a>
                <button @click="mobileOpen = !mobileOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 xl:hidden">
                    <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg x-show="mobileOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak x-transition class="border-t border-slate-200 bg-white px-4 py-3 xl:hidden">
            <nav class="grid gap-1">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}" @click="mobileOpen = false" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </div>
    </header>

    @yield('content')

    {{-- Footer --}}
    <footer class="bg-emerald-950 text-emerald-100">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:grid-cols-2 lg:grid-cols-4 lg:px-6">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-3">
                    <img src="{{ img_url($settings['logo'] ?? null, 'img/Logo SMK fix 4.png') }}" alt="{{ $siteName }}" class="h-12 w-12 rounded-xl bg-white object-contain p-1">
                    <span class="text-sm font-bold leading-tight">{{ $siteName }}</span>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-emerald-200/90">{{ $settings['site_description'] ?? '' }}</p>
                <div class="mt-4 flex items-center gap-3">
                    @foreach (['instagram', 'facebook', 'youtube', 'twitter'] as $key)
                        @if (!empty($settings[$key]))
                            <a href="{{ $settings[$key] }}" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-emerald-100 transition hover:bg-amber-500 hover:text-emerald-950" aria-label="{{ ucfirst($key) }}">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $socials[$key] }}" /></svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Menu Cepat</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($nav as $item)
                        <li><a href="{{ $item['href'] }}" class="text-emerald-200/90 transition hover:text-amber-400">{{ $item['label'] }}</a></li>
                    @endforeach
                    <li><a href="{{ route('login') }}" class="text-emerald-200/90 transition hover:text-amber-400">Login Admin</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">Kontak Kami</h4>
                <ul class="mt-4 space-y-3 text-sm text-emerald-200/90">
                    @if ($address)
                        <li class="flex gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>{{ $address }}</span>
                        </li>
                    @endif
                    @if ($phone)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <span>{{ $phone }}</span>
                        </li>
                    @endif
                    @if ($email)
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>{{ $email }}</span>
                        </li>
                    @endif
                    @if (!empty($settings['operational_hours']))
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ $settings['operational_hours'] }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-white">PPDB Tahun Ajaran {{ $settings['ppdb_tahun_ajaran'] ?? '2026/2027' }}</h4>
                <p class="mt-4 text-sm leading-relaxed text-emerald-200/90">
                    Pendaftaran Peserta Didik Baru telah dibuka. Segera daftarkan putra/putri Anda untuk bergabung bersama keluarga besar {{ $siteName }}.
                </p>
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-bold text-emerald-950 transition hover:bg-amber-400">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                    Daftar Sekarang
                </a>
            </div>
        </div>

        <div class="border-t border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-emerald-300/80 sm:flex-row lg:px-6">
                <span>&copy; {{ date('Y') }} {{ $siteName }} — Hak Cipta Dilindungi.</span>
                <span>Dibangun dengan <span class="font-semibold text-amber-400">Laravel</span></span>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp --}}
    @if ($whatsapp)
        <a href="{{ $waLink }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
           class="fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg shadow-emerald-500/40 transition hover:scale-105 hover:bg-emerald-600">
            <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
        </a>
    @endif
</body>
</html>
