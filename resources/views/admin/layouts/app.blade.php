@php
    $menu = [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10', 'route' => 'admin.dashboard', 'slug' => 'dashboard'],
        'content' => ['label' => 'Konten Website', 'children' => [
            ['label' => 'Berita', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z', 'route' => 'admin.news.index', 'slug' => 'news'],
            ['label' => 'Kategori Berita', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'route' => 'admin.categories.index', 'slug' => 'categories'],
            ['label' => 'Pengumuman', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'route' => 'admin.announcements.index', 'slug' => 'announcements'],
            ['label' => 'Galeri & Album', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'route' => 'admin.galleries.index', 'slug' => 'galleries'],
            ['label' => 'Prestasi', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'route' => 'admin.achievements.index', 'slug' => 'achievements'],
            ['label' => 'Guru', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'admin.teachers.index', 'slug' => 'teachers'],
            ['label' => 'Fasilitas', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'route' => 'admin.facilities.index', 'slug' => 'facilities'],
            ['label' => 'Ekstrakurikuler', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'route' => 'admin.extracurriculars.index', 'slug' => 'extracurriculars'],
            ['label' => 'Program', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'route' => 'admin.programs.index', 'slug' => 'programs'],
            ['label' => 'Slider', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm0 0l.5 6 3.5-3.5L11 11l-3 3m14-2l-5-5m0 0L16 7', 'route' => 'admin.sliders.index', 'slug' => 'sliders'],
            ['label' => 'Banner', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'route' => 'admin.banners.index', 'slug' => 'banners'],
            ['label' => 'Testimoni', 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'route' => 'admin.testimonials.index', 'slug' => 'testimonials'],
            ['label' => 'Partner', 'icon' => 'M3 21h18M5 21V7l7-4 7 4v14M9 21v-6a2 2 0 012-2h2a2 2 0 012 2v6', 'route' => 'admin.partners.index', 'slug' => 'partners'],
        ]],
        'ppdb' => ['label' => 'PPDB Online', 'children' => [
            ['label' => 'Data Pendaftar', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'route' => 'admin.ppdb.index', 'slug' => 'ppdb'],
        ]],
        'download' => ['label' => 'Download', 'children' => [
            ['label' => 'File Download', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'route' => 'admin.downloads.index', 'slug' => 'downloads'],
        ]],
        'profile' => ['label' => 'Profil Sekolah', 'children' => [
            ['label' => 'Tentang & Sejarah', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'route' => 'admin.profile.edit', 'slug' => 'school-profile'],
            ['label' => 'Visi Misi', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'route' => 'admin.visi.edit', 'slug' => 'visi'],
            ['label' => 'Struktur Organisasi', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'route' => 'admin.structure.edit', 'slug' => 'structure'],
            ['label' => 'Sambutan Kepala Sekolah', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'route' => 'admin.welcome.edit', 'slug' => 'welcome'],
            ['label' => 'Data Guru & Staff', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'route' => 'admin.teachers.index', 'slug' => 'staff'],
        ]],
        'contact' => ['label' => 'Kontak', 'children' => [
            ['label' => 'Data Kontak', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'route' => 'admin.contact.edit', 'slug' => 'contact'],
        ]],
        'users' => ['label' => 'Pengguna', 'children' => [
            ['label' => 'Kelola User', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'route' => 'admin.users.index', 'slug' => 'users', 'super_admin_only' => true],
        ]],
        'settings' => ['label' => 'Pengaturan', 'children' => [
            ['label' => 'Setting Website', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'route' => 'admin.settings.edit', 'slug' => 'settings'],
        ]],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">
<div x-data="{ sidebarOpen: false, userMenu: false }" @click.outside="userMenu = false" class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full bg-gradient-to-b from-emerald-900 via-emerald-900 to-teal-950 text-white transition-transform duration-300 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : ''">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-400 text-emerald-950">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                </span>
                <span class="text-sm font-bold leading-tight">
                    Al-Fatih <br>
                    <span class="text-[11px] font-normal text-emerald-300">Administrator</span>
                </span>
            </a>
            <button @click="sidebarOpen = false" class="rounded-lg p-1 text-emerald-300 hover:bg-white/10 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <nav class="h-[calc(100vh-4rem)] overflow-y-auto px-3 py-4 space-y-1">
            @foreach ($menu as $key => $item)
                @if (isset($item['route']))
                    <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs($item['slug'].'*') ? 'bg-white/15 text-white' : 'text-emerald-100 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        {{ $item['label'] }}
                    </a>
                @else
                    @php
                        $visibleChildren = collect($item['children'])
                            ->filter(fn ($child) => empty($child['super_admin_only']) || auth()->user()->isSuperAdmin())
                            ->filter(fn ($child) => Route::has($child['route']))
                            ->values();
                        $hasActive = $visibleChildren->contains(fn ($child) => request()->routeIs($child['slug'].'*'));
                    @endphp
                    @if ($visibleChildren->isNotEmpty())
                    <details {{ $hasActive ? 'open' : '' }} class="group">
                        <summary class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-emerald-100 transition-colors hover:bg-white/10 hover:text-white">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            <span class="flex-1">{{ $item['label'] }}</span>
                            <svg class="h-4 w-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </summary>
                        <div class="ml-4 mt-1 space-y-0.5 border-l border-white/10 pl-3">
                            @foreach ($visibleChildren as $child)
                                <a href="{{ route($child['route']) }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-[13px] transition-colors {{ request()->routeIs($child['slug'].'*') ? 'bg-white/15 font-semibold text-white' : 'text-emerald-200 hover:bg-white/10 hover:text-white' }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $child['icon'] }}" /></svg>
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </details>
                    @endif
                @endif
            @endforeach
        </nav>
    </aside>

    @if ($sidebarOverlay ?? false)
    @endif
    {{-- Sidebar overlay mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/50 lg:hidden" x-cloak></div>

    {{-- Main --}}
    <div class="flex min-h-screen flex-1 flex-col lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur lg:px-6">
            <button @click="sidebarOpen = !sidebarOpen" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>

            <div class="hidden items-center gap-2 text-sm text-slate-500 md:flex">
                <span class="font-medium text-slate-700">{{ $breadcrumb ?? 'Dashboard' }}</span>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank"
                   class="hidden items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-emerald-600 hover:text-emerald-700 sm:inline-flex">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                    Lihat Website
                </a>

                {{-- User menu --}}
                <div class="relative">
                    <button @click="userMenu = !userMenu" class="flex items-center gap-2 rounded-lg p-1.5 transition hover:bg-slate-100">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] text-slate-500">{{ auth()->user()->role->name ?? '-' }}</span>
                        </span>
                        <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </button>

                    <div x-show="userMenu" x-cloak x-transition
                         class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                        <div class="border-b border-slate-100 px-3 py-2">
                            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="mt-1 flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('password.change') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                            Ganti Password
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

        {{-- Breadcrumb --}}
        @if (!empty($breadcrumbs))
            <nav class="flex items-center gap-2 border-b border-slate-200 bg-white px-4 py-2.5 text-xs text-slate-500 lg:px-6">
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-emerald-700">Dashboard</a>
                @foreach ($breadcrumbs as $crumb)
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    <span class="{{ $loop->last ? 'font-semibold text-emerald-700' : '' }}">{{ $crumb }}</span>
                @endforeach
            </nav>
        @endif

        {{-- Page content --}}
        <main class="flex-1 px-4 py-6 lg:px-6">
            @if (session('success'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                     x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <svg class="h-5 w-5 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white px-4 py-4 text-center text-xs text-slate-500 lg:px-6">
            &copy; {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }} — Hak Cipta Dilindungi. Dibangun dengan <span class="font-semibold text-emerald-700">Laravel</span>.
        </footer>
    </div>
</div>

<script>
    function confirmDelete(formId) {
        if (confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById(formId).submit();
        }
    }
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" alt="Preview">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@stack('scripts')
</body>
</html>
