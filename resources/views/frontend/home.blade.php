@extends('frontend.layouts.app')

@section('title', 'Beranda')

@php
    $siteName = $settings['site_name'] ?? config('app.name');
    $tagline = $settings['site_tagline'] ?? '';
    $desc = $settings['site_description'] ?? '';
    $waLink = isset($contact?->whatsapp) || isset($settings['whatsapp'])
        ? 'https://wa.me/'.preg_replace('/\D+/', '', $contact?->whatsapp ?? $settings['whatsapp'] ?? '')
        : '#';
    $defaultIcon = 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
    $statItems = [
        ['label' => 'Tahun Berdiri', 'value' => '1998', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Siswa Aktif', 'value' => number_format($stats['students'] ?? 0), 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
        ['label' => 'Guru & Staff', 'value' => number_format($stats['teachers'] ?? 0), 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Prestasi', 'value' => number_format($stats['achievements'] ?? 0), 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ];
    $contacts = [
        ['label' => 'Alamat', 'value' => $settings['address'] ?? '-', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Telepon', 'value' => $settings['phone'] ?? '-', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
        ['label' => 'Email', 'value' => $settings['email'] ?? '-', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['label' => 'Jam Operasional', 'value' => $settings['operational_hours'] ?? '-', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
@endphp

@section('content')

{{-- ============ HERO ============ --}}
<section id="beranda" class="relative flex min-h-[88vh] items-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('img/Siang 3.0.png') }}" alt="{{ $siteName }}" class="h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/80 to-emerald-950/30"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-emerald-950/90 to-transparent"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-24 lg:px-6">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                Sekolah Islam Terpadu & Tahfizh Al-Qur'an
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                {{ $siteName }}
            </h1>
            <p class="mt-3 text-lg font-semibold text-amber-300">{{ $tagline }}</p>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-emerald-100/90 sm:text-base">{{ $desc }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-6 py-3.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-500/30 transition hover:bg-amber-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Daftar PPDB {{ $settings['ppdb_tahun_ajaran'] ?? '' }}
                </a>
                <a href="#profil"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                    Profil Sekolah
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ============ PENGUMUMAN (ticker) ============ --}}
@if ($announcements->isNotEmpty())
    <div class="border-b border-amber-200 bg-amber-50">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 lg:px-6">
            <span class="flex shrink-0 items-center gap-2 rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-emerald-950">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                Pengumuman
            </span>
            <div class="relative flex-1 overflow-hidden">
                <div class="animate-marquee flex whitespace-nowrap">
                    <span class="pr-16 text-sm text-slate-700">
                        @foreach ($announcements as $announcement)
                            <span class="inline-flex items-center gap-2">
                                <span class="h-1 w-1 rounded-full bg-amber-500"></span>
                                {{ $announcement->title }}
                            </span>
                            <span class="mx-6 h-4 w-px bg-slate-300"></span>
                        @endforeach
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- ============ PROFIL / SAMBUTAN ============ --}}
<section id="profil" class="bg-white py-20 lg:py-24">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 lg:grid-cols-2 lg:px-6">
        <div class="relative">
            <div class="grid grid-cols-2 gap-4">
                <img src="{{ asset('img/Siang 3.0. Kiri.png') }}" alt="Gedung {{ $siteName }}" class="h-64 w-full rounded-2xl object-cover shadow-xl lg:h-80">
                <img src="{{ asset('img/Siang 3.0. Kanan.png') }}" alt="Gedung {{ $siteName }}" class="mt-10 h-64 w-full rounded-2xl object-cover shadow-xl lg:h-80">
            </div>
            <div class="absolute -bottom-6 left-6 flex items-center gap-3 rounded-2xl bg-emerald-700 px-5 py-4 text-white shadow-xl">
                <svg class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                <span class="leading-tight">
                    <span class="block text-2xl font-extrabold">Al-Qur'an</span>
                    <span class="block text-xs font-medium text-emerald-100">Sebagai Jantung Kehidupan</span>
                </span>
            </div>
        </div>

        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Tentang Kami</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Selamat Datang di {{ $siteName }}</h2>
            <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                {{ $settings['site_description'] ?? '' }}
            </p>
            <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                Kami berkomitmen mencetak generasi Qur'ani yang berprestasi, berkarakter, dan siap menghadapi tantangan zaman. Dengan perpaduan kurikulum nasional dan pendidikan tahfizh Al-Qur'an, setiap peserta didik dibina secara holistik — intelektual, spiritual, dan sosial.
            </p>
            <ul class="mt-6 space-y-3">
                @foreach ([
                    'Pembinaan Tahfizh Al-Qur\'an intensif',
                    'Kurikulum nasional terpadu dengan nilai Islami',
                    'Tenaga pendidik profesional & hafizh',
                    'Lingkungan belajar yang aman, nyaman, dan Islami',
                ] as $point)
                    <li class="flex items-start gap-3 text-sm text-slate-700">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </span>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="#program" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                Kenali Program Kami
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
            </a>
        </div>
    </div>
</section>

{{-- ============ STATISTIK ============ --}}
<section class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 py-14">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 lg:grid-cols-4 lg:px-6">
        @foreach ($statItems as $stat)
            <div class="flex flex-col items-center text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-amber-400">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" /></svg>
                </span>
                <span class="mt-3 text-3xl font-extrabold text-white">{{ $stat['value'] }}</span>
                <span class="mt-1 text-xs font-medium uppercase tracking-wider text-emerald-200">{{ $stat['label'] }}</span>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ PROGRAM UNGGULAN ============ --}}
<section id="program" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Keunggulan</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Program Unggulan</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">Program-program pilihan yang dirancang untuk mengembangkan potensi akademik dan karakter Islami peserta didik.</p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($programs as $program)
                <article class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-700 text-white transition group-hover:bg-amber-500 group-hover:text-emerald-950">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $program->icon ?: $defaultIcon }}" /></svg>
                    </span>
                    <h3 class="mt-5 text-lg font-bold text-emerald-950">{{ $program->name }}</h3>
                    @if ($program->excerpt)
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $program->excerpt }}</p>
                    @endif
                    <span class="mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700">
                        Pelajari
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </span>
                </article>
            @empty
                @foreach (['Tahfizh Al-Qur\'an', 'Akademik Terpadu', 'Ekstrakurikuler'] as $fallback)
                    <article class="rounded-2xl border border-dashed border-slate-300 bg-white p-7 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $defaultIcon }}" /></svg>
                        </span>
                        <h3 class="mt-5 text-lg font-bold text-emerald-950">{{ $fallback }}</h3>
                        <p class="mt-2 text-sm text-slate-500">Konten program akan segera diisi.</p>
                    </article>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

{{-- ============ FASILITAS ============ --}}
@if ($facilities->isNotEmpty())
    <section id="fasilitas" class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Sarana & Prasarana</span>
                <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Fasilitas Sekolah</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">Fasilitas lengkap untuk mendukung kenyamanan dan keberhasilan belajar peserta didik.</p>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($facilities as $facility)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center transition hover:border-emerald-600 hover:bg-white hover:shadow-lg">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-emerald-700 shadow-sm">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $facility->icon ?: $defaultIcon }}" /></svg>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-emerald-950">{{ $facility->name }}</h3>
                        @if ($facility->description)
                            <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ $facility->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ EKSTRAKURIKULER ============ --}}
@if ($extracurriculars->isNotEmpty())
    <section id="ekskul" class="bg-slate-50 py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Pengembangan Diri</span>
                <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Ekstrakurikuler</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">Wadah pengembangan bakat, minat, dan soft skill peserta didik di luar jam pelajaran.</p>
            </div>
            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($extracurriculars as $ekskul)
                    <div class="group flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 transition hover:border-amber-400 hover:shadow-md">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 transition group-hover:bg-amber-500 group-hover:text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ekskul->icon ?: $defaultIcon }}" /></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-emerald-950">{{ $ekskul->name }}</h3>
                            @if ($ekskul->schedule)
                                <p class="mt-1 text-xs text-slate-500">{{ $ekskul->schedule }}</p>
                            @endif
                            @if ($ekskul->advisor)
                                <p class="mt-1 text-xs font-medium text-emerald-700">Pembina: {{ $ekskul->advisor }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ PRESTASI ============ --}}
@if ($achievements->isNotEmpty())
    <section id="prestasi" class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Kebanggaan Kami</span>
                <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Prestasi Siswa</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">Berbagai pencapaian membanggakan yang diraih siswa-siswi {{ $siteName }}.</p>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($achievements as $achievement)
                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg">
                        <span class="absolute right-4 top-4 rounded-full bg-amber-500 px-3 py-1 text-xs font-bold text-emerald-950">{{ $achievement->year }}</span>
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-700 text-white">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </span>
                        @if ($achievement->category)
                            <span class="mt-4 inline-block rounded bg-emerald-50 px-2 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700">{{ $achievement->category }}</span>
                        @endif
                        <h3 class="mt-2 text-base font-bold leading-snug text-emerald-950">{{ $achievement->title }}</h3>
                        @if ($achievement->rank)
                            <p class="mt-1 text-xs font-semibold text-amber-600">{{ $achievement->rank }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ BERITA ============ --}}
@if ($news->isNotEmpty())
    <section id="berita" class="bg-slate-50 py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Informasi Terbaru</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Berita & Kegiatan</h2>
                </div>
                <span class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700">Sumber resmi {{ $siteName }}</span>
            </div>
            <div class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ($news as $item)
                    <article class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative h-52 overflow-hidden bg-slate-100">
                            @if ($item->thumbnail)
                                <img src="{{ img_url($item->thumbnail) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-700 to-teal-900 text-white">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                                </div>
                            @endif
                            @if ($item->category)
                                <span class="absolute left-3 top-3 rounded-lg bg-emerald-700 px-3 py-1 text-[11px] font-bold text-white">{{ $item->category->name }}</span>
                            @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-2 text-xs text-slate-400">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                {{ $item->published_at?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y') }}
                            </div>
                            <h3 class="mt-2 line-clamp-2 text-base font-bold leading-snug text-emerald-950 group-hover:text-emerald-700">{{ $item->title }}</h3>
                            @if ($item->excerpt)
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500">{{ $item->excerpt }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ GALERI ============ --}}
@if ($albums->isNotEmpty() && $albums->contains(fn ($album) => $album->galleries->isNotEmpty()))
    <section id="galeri" class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Dokumentasi</span>
                <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Galeri Kegiatan</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">Momen-momen berharga dalam kehidupan sekolah kami.</p>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($albums as $album)
                    @foreach ($album->galleries->take($album->galleries->count() > 8 ? 8 : $album->galleries->count()) as $gallery)
                        <div class="group relative aspect-square overflow-hidden rounded-2xl">
                            <img src="{{ img_url($gallery->image, 'img/Siang 3.0.png') }}" alt="{{ $gallery->title ?? $album->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 flex items-end bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent p-3 opacity-0 transition group-hover:opacity-100">
                                <span class="text-xs font-semibold text-white">{{ $gallery->title ?? $album->title }}</span>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ TESTIMONI ============ --}}
@if ($testimonials->isNotEmpty())
    <section class="bg-gradient-to-br from-emerald-900 to-teal-950 py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">Kata Mereka</span>
                <h2 class="mt-2 text-3xl font-extrabold text-white lg:text-4xl">Testimoni Alumni & Wali</h2>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <figure class="rounded-2xl bg-white/10 p-7 backdrop-blur">
                        <svg class="h-8 w-8 text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg>
                        <blockquote class="mt-4 text-sm leading-relaxed text-emerald-50/95">"{{ $testimonial->content }}"</blockquote>
                        <figcaption class="mt-6 flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-500 text-sm font-bold text-emerald-950">
                                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                            </span>
                            <span>
                                <span class="block text-sm font-bold text-white">{{ $testimonial->name }}</span>
                                <span class="block text-xs text-emerald-200">{{ $testimonial->position }} @if ($testimonial->alumni_year)· Angkatan {{ $testimonial->alumni_year }}@endif</span>
                            </span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ PARTNER ============ --}}
@if ($partners->isNotEmpty())
    <section class="border-b border-slate-200 bg-white py-14">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div class="mx-auto max-w-xl text-center">
                <h3 class="text-lg font-bold text-emerald-950">Mitra & Kerja Sama</h3>
                <p class="mt-2 text-sm text-slate-500">Bersama membangun pendidikan berkualitas.</p>
            </div>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-6">
                @foreach ($partners as $partner)
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-6 py-3">
                        @if ($partner->logo)
                            <img src="{{ img_url($partner->logo) }}" alt="{{ $partner->name }}" class="h-8 w-8 object-contain">
                        @endif
                        <span class="text-sm font-bold text-slate-600">{{ $partner->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ KONTAK ============ --}}
<section id="kontak" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-amber-600">Hubungi Kami</span>
            <h2 class="mt-2 text-3xl font-extrabold text-emerald-950 lg:text-4xl">Kontak Sekolah</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">Silakan hubungi kami untuk informasi lebih lanjut mengenai PPDB dan kegiatan sekolah.</p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-5">
            <div class="space-y-4 lg:col-span-2">
                @foreach ($contacts as $contactItem)
                    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-700 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $contactItem['icon'] }}" /></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-950">{{ $contactItem['label'] }}</h4>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $contactItem['value'] }}</p>
                        </div>
                    </div>
                @endforeach
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-bold text-white transition hover:bg-emerald-800">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat WhatsApp PPDB
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm lg:col-span-3">
                @if (!empty($settings['maps_embed']))
                    <iframe src="{{ $settings['maps_embed'] }}" class="h-full min-h-[400px] w-full" style="border:0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi {{ $siteName }}"></iframe>
                @else
                    <div class="flex h-full min-h-[400px] w-full flex-col items-center justify-center bg-emerald-800 p-8 text-center text-white">
                        <img src="{{ asset('img/Siang 3.0.png') }}" alt="{{ $siteName }}" class="h-28 w-28 rounded-2xl object-cover opacity-90">
                        <h3 class="mt-5 text-lg font-bold">{{ $siteName }}</h3>
                        <p class="mt-2 max-w-sm text-sm text-emerald-100">{{ $settings['address'] ?? '' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
