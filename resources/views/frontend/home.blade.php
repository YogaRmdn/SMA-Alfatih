@extends('frontend.layouts.app')

@section('title', 'Beranda')

@php
    $siteName = $settings['site_name'] ?? config('app.name');
    $heroTitleLines = preg_split('/\s+(?=Al-)/i', $siteName) ?: [$siteName];
    $tagline = $settings['site_tagline'] ?? '';
    $desc = $settings['site_description'] ?? '';
    $content = fn (string $key, string $default = ''): string => str_replace(
        '{site_name}',
        $siteName,
        $settings[$key] ?? $default
    );
    $waLink = isset($contact?->whatsapp) || isset($settings['whatsapp'])
        ? 'https://wa.me/'.preg_replace('/\D+/', '', $contact?->whatsapp ?? $settings['whatsapp'] ?? '')
        : '#';
    $defaultIcon = 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
    $statItems = [
        ['label' => $settings['stat_1_label'] ?? 'Tahun Berdiri', 'value' => $settings['stat_1_value'] ?? '2020', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => $settings['stat_2_label'] ?? 'Siswa Aktif', 'value' => $settings['stat_2_value'] ?? '160+', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
        ['label' => $settings['stat_3_label'] ?? 'Guru & Staff', 'value' => $settings['stat_3_value'] ?? '29', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
    ];
    $contacts = [
        ['label' => 'Alamat', 'value' => $settings['address'] ?? '-', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'Telepon', 'value' => $settings['phone'] ?? '-', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
        ['label' => 'Email', 'value' => $settings['email'] ?? '-', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['label' => 'Jam Operasional', 'value' => $settings['operational_hours'] ?? '-', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];

    // Slide untuk hero (foto samping): BANNER posisi "hero". Jika kosong, area foto dikosongkan juga.
    $heroSlides = $heroBanners->map(fn ($b) => [
        'url' => img_url($b->image),
        'caption' => $b->title,
    ])->values()->all();
@endphp

@section('content')

{{-- ============ HERO ============ --}}
<section id="beranda" class="relative flex min-h-[88vh] items-center overflow-hidden">
    <div class="absolute inset-0">
        <video src="{{ asset('video/video1.mp4') }}" poster="{{ asset('img/Siang 3.0.png') }}" autoplay muted loop playsinline class="h-full w-full object-cover"></video>
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-950/80 to-emerald-950/30"></div>
        <div class="absolute inset-0 animate-flow-x bg-[linear-gradient(120deg,rgba(5,150,105,0.55),rgba(13,148,136,0.3),rgba(251,191,36,0.22),rgba(8,47,73,0.55))] bg-[length:250%_250%]"></div>
        <div class="aurora -left-24 top-16 h-80 w-80 bg-emerald-400/40"></div>
        <div class="aurora right-0 top-1/3 h-96 w-96 bg-amber-400/30" style="animation-delay:-8s"></div>
        <div class="aurora bottom-0 left-1/3 h-80 w-80 bg-teal-300/30" style="animation-delay:-14s"></div>
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-emerald-950/90 to-transparent"></div>
    </div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-24 lg:px-6">
        <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-amber-300 backdrop-blur">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                {{ $settings['hero_badge'] ?? "Sekolah Islam Terpadu & Tahfizh Al-Qur'an" }}
            </span>
            <h1 class="mt-5 text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
                @foreach ($heroTitleLines as $line)
                    <span class="block">{{ $line }}</span>
                @endforeach
            </h1>
            <p class="mt-3 text-gradient-light text-lg font-semibold sm:text-xl">{{ $tagline }}</p>
            <p class="mt-4 max-w-xl text-sm leading-relaxed text-emerald-100/90 sm:text-base">{{ $desc }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('ppdb.register') }}"
                   class="sheen inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-6 py-3.5 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-500/40 transition hover:brightness-105">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Daftar PPDB {{ $settings['ppdb_tahun_ajaran'] ?? '' }}
                </a>
                <a href="#profil"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 text-sm font-bold text-white backdrop-blur transition hover:border-white/50 hover:bg-white/20">
                    Profil Sekolah
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                </a>
            </div>
        </div>

        @if (count($heroSlides) > 0)
        <div class="relative ml-auto hidden w-full max-w-md lg:block"
                 x-data="{
                     ready: false,
                     current: 0,
                     total: {{ count($heroSlides) }},
                     slides: {{ Js::from($heroSlides) }},
                     timer: null,
                     init() {
                         this.ready = true;
                         if (this.total > 1) {
                             this.timer = setInterval(() => {
                                 this.current = (this.current + 1) % this.total;
                             }, 5200);
                         }
                     },
                     go(i) { this.current = i; },
                     destroy() { if (this.timer) clearInterval(this.timer); }
                 }"
                 style="opacity:0" :style="ready ? 'opacity:1' : 'opacity:0'">
            <div class="relative aspect-square w-full overflow-hidden rounded-3xl bg-emerald-950 shadow-2xl shadow-emerald-950/60">
                <div class="flex h-full w-full transition-transform duration-[1200ms] ease-in-out"
                     :style="'transform: translateX(-' + (current * 100) + '%)'">
                    @foreach ($heroSlides as $i => $slide)
                        <div class="relative h-full w-full shrink-0">
                            <img src="{{ $slide['url'] }}" alt="{{ $slide['caption'] }}" loading="lazy" class="h-full w-full object-cover">
                        </div>
                    @endforeach
                </div>
                <div class="pointer-events-none absolute -left-10 -top-10 h-32 w-32 rounded-full bg-amber-400/25 blur-3xl" aria-hidden="true"></div>
                <div class="absolute inset-x-4 bottom-4 flex items-end justify-between gap-3">
                    <div class="h-7 max-w-[60%] overflow-hidden rounded-full border border-white/20 bg-emerald-950/60 px-3 backdrop-blur"
                          x-show="slides.length && slides[current] && slides[current].caption">
                        <span class="flex h-7 items-center overflow-hidden whitespace-nowrap text-xs font-bold text-white"
                              x-show="slides.length" x-text="slides[current].caption"></span>
                    </div>
                    @if (count($heroSlides) > 1)
                        <div class="flex items-center gap-2 rounded-full border border-white/20 bg-emerald-950/60 px-3 py-2 backdrop-blur">
                            @foreach ($heroSlides as $i => $slide)
                                <button type="button" title="Slide {{ $i + 1 }}" aria-label="Slide {{ $i + 1 }}" @click="go({{ $i }})"
                                        class="h-2 cursor-pointer rounded-full transition-all duration-500"
                                        :class="current === {{ $i }} ? 'w-6 bg-amber-400' : 'w-2 bg-white/50 hover:bg-white/90'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        </div>
    </div>
</section>

{{-- ============ PENGUMUMAN (ticker) ============ --}}
@if ($announcements->isNotEmpty())
    <div class="animate-flow-x border-b border-amber-200 bg-[linear-gradient(90deg,#fffbeb,#fef3c7,#ecfdf5,#f0fdf4)]">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 lg:px-6">
            <span class="flex shrink-0 items-center gap-2 rounded-lg bg-gradient-to-r from-amber-400 to-orange-400 px-3 py-1.5 text-xs font-bold text-emerald-950 shadow-sm shadow-amber-500/30">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                {{ $settings['ticker_label'] ?? 'Pengumuman' }}
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
        <x-reveal type="left">
            <div class="relative">
                <div class="grid grid-cols-2 gap-4">
                    <img src="{{ img_url($settings['profil_photo_1'] ?? null, 'img/Siang 3.0. Kiri.png') }}" alt="Gedung {{ $siteName }}" class="h-64 w-full rounded-2xl object-cover shadow-xl lg:h-80">
                    <img src="{{ img_url($settings['profil_photo_2'] ?? null, 'img/Siang 3.0. Kanan.png') }}" alt="Gedung {{ $siteName }}" class="mt-10 h-64 w-full rounded-2xl object-cover shadow-xl lg:h-80">
                </div>
                <div class="absolute -bottom-6 left-6 flex items-center gap-3 rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 px-5 py-4 text-white shadow-xl">
                    <svg class="h-8 w-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    <span class="leading-tight">
                        <span class="block text-2xl font-extrabold">{{ $settings['profil_badge_1'] ?? "Al-Qur'an" }}</span>
                        <span class="block text-xs font-medium text-emerald-100">{{ $settings['profil_badge_2'] ?? 'Sebagai Jantung Kehidupan' }}</span>
                    </span>
                </div>
            </div>
        </x-reveal>

        <x-reveal type="right" :delay="120">
            <div>
                <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['profil_eyebrow'] ?? 'Tentang Kami' }}</span>
            <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['profil_title_prefix'] ?? 'Selamat Datang di' }} {{ $siteName }}</h2>
            <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                {{ $settings['site_description'] ?? '' }}
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
            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="#program" class="sheen inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:brightness-110">
                    Kenali Program Kami
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </a>
                <a href="{{ route('profil.tentang') }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-700 px-6 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-700 hover:text-white">
                    Baca Selengkapnya
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H8m9 0v9" /></svg>
                </a>
            </div>
            </div>
        </x-reveal>
    </div>
</section>

{{-- ============ SAMBUTAN KEPALA SEKOLAH ============ --}}
<section id="sambutan" class="relative overflow-hidden bg-slate-50 py-20 lg:py-24">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -right-24 top-16 h-80 w-80 rounded-full bg-emerald-300/30 blur-3xl"></div>
        <div class="absolute -left-24 bottom-16 h-72 w-72 rounded-full bg-amber-300/30 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['sambutan_eyebrow'] ?? 'Sambutan' }}</span>
                <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['sambutan_title'] ?? 'Sambutan Kepala Sekolah' }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $settings['sambutan_intro'] ?? 'Sepatah kata dari pimpinan untuk menyambut Anda di' }} {{ $siteName }}.</p>
            </div>
        </x-reveal>

        <div class="mt-14 grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <x-reveal type="left">
                <div class="relative mx-auto w-full max-w-sm sm:max-w-md lg:max-w-lg">
                    <div class="absolute -inset-4 -z-10 rotate-3 rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-500 to-amber-400 opacity-80"></div>
                    <div class="overflow-hidden rounded-3xl border-4 border-white bg-slate-100 shadow-2xl">
                        <img src="{{ img_url($headmaster->photo ?? null, 'img/kepsek.jpeg') }}" alt="{{ $headmaster->name ?? 'Kepala Sekolah' }}" class="aspect-[4/5] w-full object-cover">
                    </div>
                    <div class="absolute -bottom-6 left-1/2 w-max -translate-x-1/2 rounded-2xl bg-gradient-to-br from-emerald-700 to-teal-800 px-6 py-3 text-center text-white shadow-xl">
                        <span class="block text-sm font-extrabold">{{ $headmaster->name ?? 'Kepala Sekolah' }}</span>
                        <span class="block text-xs font-medium text-emerald-200">Kepala {{ $siteName }}</span>
                    </div>
                </div>
            </x-reveal>

            <x-reveal type="right" :delay="120">
                <div class="relative">
                    <svg class="h-12 w-12 text-emerald-600/20" fill="currentColor" viewBox="0 0 24 24"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/></svg>
                    <h3 class="mt-3 text-2xl font-extrabold text-emerald-950 lg:text-3xl">{{ $settings['sambutan_heading'] ?? "Assalamu'alaikum & Selamat Datang" }}</h3>
                    @php
                        $profileSambutan = $profilPages['sambutan-kepala-sekolah'] ?? null;
                    @endphp
                    @if ($profileSambutan?->content)
                        <p class="mt-5 whitespace-pre-line text-sm leading-relaxed text-slate-600 sm:text-base">
                            {{ str_replace('{site_name}', $siteName, $profileSambutan->content) }}
                        </p>
                    @else
                    <p class="mt-5 text-sm leading-relaxed text-slate-600 sm:text-base">
                        {{ $content('sambutan_text_1', "Alhamdulillah, kami bersyukur ke hadirat Allah SWT atas segala nikmat dan karunia-Nya. Kami mengucapkan selamat datang di {site_name} — sekolah menengah atas Islam terpadu tahfizh Al-Qur'an yang berkomitmen mencetak generasi Qur'ani, berprestasi, dan berkarakter.") }}
                    </p>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                        {{ $content('sambutan_text_2', 'Semoga melalui website ini, para orang tua, calon peserta didik, dan seluruh masyarakat dapat mengenal lebih dekat program, kegiatan, serta suasana kekeluargaan di sekolah kami. Mari bersama wujudkan masa depan cerah generasi penerus bangsa.') }}
                    </p>
                    @endif
                    <div class="mt-8 flex items-center gap-4 rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </span>
                        <div>
                            <span class="block text-base font-extrabold text-emerald-950">{{ $headmaster->name ?? 'Kepala Sekolah' }}</span>
                            <span class="block text-sm font-medium text-emerald-700">Kepala {{ $siteName }}</span>
                        </div>
                    </div>
                </div>
            </x-reveal>
        </div>
    </div>
</section>

{{-- ============ DEWAN GURU (MARQUEE) ============ --}}
@if ($teachers->isNotEmpty())
    <section class="relative overflow-hidden bg-[linear-gradient(180deg,#f0fdf4_0%,#e4f6ef_50%,#d8f0e6_100%)] py-16 lg:py-20">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -left-20 top-10 h-64 w-64 rounded-full bg-emerald-300/25 blur-3xl"></div>
            <div class="absolute -right-20 bottom-10 h-72 w-72 rounded-full bg-amber-300/25 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
                <div class="mx-auto max-w-2xl text-center">
                    <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['guru_eyebrow'] ?? 'Dewan Guru' }}</span>
                    <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['guru_title'] ?? 'Guru & Tenaga Pendidik' }}</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $settings['guru_subtitle'] ?? 'Didampingi para pendidik yang amanah, profesional, dan hafizh untuk membimbing peserta didik menjadi generasi Qur\'ani dan berprestasi.' }}</p>
                </div>
            </x-reveal>

            <div class="relative mt-12 overflow-hidden" x-data="teachersCarousel"
                 @mouseenter="paused = true" @mouseleave="paused = false"
                 x-ref="wrap">
                <div class="marquee-track" x-ref="track">
                    <div class="flex shrink-0 items-stretch gap-6 pr-6">
                        @foreach ($teachers as $teacher)
                                <div class="group teacher-card relative shrink-0 overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-emerald-100 transition duration-300 hover:-translate-y-1.5 hover:shadow-2xl hover:ring-emerald-300">
                                    <div class="teacher-photo relative overflow-hidden bg-gradient-to-br from-emerald-700 via-teal-800 to-emerald-900">
                                        @if ($teacher->photo)
                                            <img src="{{ img_url($teacher->photo) }}" alt="{{ $teacher->name }}"
                                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center">
                                                @php
                                                    $initials = collect(preg_split('/\s+/', trim($teacher->name ?? '')))
                                                        ->filter()
                                                        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                                        ->take(2)
                                                        ->join('');
                                                @endphp
                                                <span class="text-4xl font-bold text-white/25 sm:text-5xl">{{ $initials ?: 'G' }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-x-0 bottom-0 rounded-t-2xl bg-gradient-to-t from-emerald-950/95 via-emerald-950/70 to-transparent p-3 pt-8 sm:p-4 sm:pt-10">
                                            <span class="block text-xs font-extrabold leading-snug text-white sm:text-sm">{{ $teacher->name }}</span>
                                            <span class="mt-0.5 block text-[11px] font-medium text-emerald-200 sm:text-xs">{{ $teacher->position ?: ($teacher->subject ?: 'Guru') }}</span>
                                        </div>
                                        <div class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-12 bg-gradient-to-r from-[#eefaf2] to-transparent sm:w-16 lg:w-24" aria-hidden="true"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-12 bg-gradient-to-l from-[#ddf2e8] to-transparent sm:w-16 lg:w-24" aria-hidden="true"></div>
            </div>
        </div>
    </section>
@endif

{{-- ============ STATISTIK ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(110deg,#04331f,#0f766e,#065f46,#134e4a,#6b3a10)] py-14">
    <div class="aurora left-1/4 top-0 h-64 w-64 bg-amber-400/30"></div>
    <div class="aurora bottom-0 right-1/4 h-72 w-72 bg-teal-300/20" style="animation-delay:-11s"></div>
    <div class="relative mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-10 px-4 lg:gap-16 lg:px-6">
        @foreach ($statItems as $stat)
            <x-reveal type="zoom" :delay="$loop->index * 100">
                <div class="flex w-44 flex-col items-center text-center">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400/90 to-orange-500/90 text-emerald-950 shadow-lg shadow-black/20">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" /></svg>
                    </span>
                    <span class="text-gradient-light mt-3 text-3xl font-extrabold">{{ $stat['value'] }}</span>
                    <span class="mt-1 text-xs font-medium uppercase tracking-wider text-emerald-200">{{ $stat['label'] }}</span>
                </div>
            </x-reveal>
        @endforeach
    </div>
</section>

{{-- ============ PROGRAM UNGGULAN ============ --}}
<section id="program" class="relative overflow-hidden bg-slate-50 py-20 lg:py-24">
    {{-- Dekorasi latar --}}
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-28 top-16 h-80 w-80 rounded-full bg-emerald-300/30 blur-3xl"></div>
        <div class="absolute -right-28 bottom-24 h-80 w-80 rounded-full bg-amber-300/30 blur-3xl"></div>
        <div class="absolute left-1/3 top-1/2 h-56 w-56 rounded-full bg-teal-200/30 blur-3xl"></div>
        <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle, rgba(5,150,105,0.12) 1px, transparent 1px); background-size: 26px 26px;"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        @php
            $programPalettes = [
                'tahfizh' => [
                    'bar' => 'from-emerald-600 to-teal-400',
                    'tile' => 'from-emerald-600 via-emerald-500 to-teal-400',
                    'glow' => 'bg-emerald-200/60',
                    'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                    'link' => 'text-emerald-700 group-hover:text-emerald-600',
                    'num' => 'group-hover:text-emerald-600/25',
                    'label' => 'Tahfizh & Tilawah',
                ],
                'akademik' => [
                    'bar' => 'from-sky-600 to-indigo-400',
                    'tile' => 'from-sky-600 via-sky-500 to-indigo-400',
                    'glow' => 'bg-sky-200/60',
                    'chip' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
                    'link' => 'text-sky-700 group-hover:text-sky-600',
                    'num' => 'group-hover:text-sky-600/25',
                    'label' => 'Akademik',
                ],
                'it' => [
                    'bar' => 'from-violet-600 to-fuchsia-400',
                    'tile' => 'from-violet-600 via-violet-500 to-fuchsia-400',
                    'glow' => 'bg-violet-200/60',
                    'chip' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                    'link' => 'text-violet-700 group-hover:text-violet-600',
                    'num' => 'group-hover:text-violet-600/25',
                    'label' => 'Sains & Teknologi',
                ],
                'unggulan' => [
                    'bar' => 'from-amber-500 to-orange-400',
                    'tile' => 'from-amber-500 via-amber-400 to-orange-400',
                    'glow' => 'bg-amber-200/60',
                    'chip' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                    'link' => 'text-amber-700 group-hover:text-amber-600',
                    'num' => 'group-hover:text-amber-600/25',
                    'label' => 'Penguatan Karakter',
                ],
            ];
            $defaultPalette = $programPalettes['tahfizh'];

            $programCards = $programs->map(fn ($p) => [
                'name' => $p->name,
                'excerpt' => $p->excerpt,
                'content' => $p->content,
                'icon' => $p->icon,
                'palette' => $programPalettes[$p->type] ?? $defaultPalette,
                'interactive' => true,
            ]);

            if ($programCards->isEmpty()) {
                $programCards = collect([
                    ['name' => 'Tahfizh Al-Qur\'an', 'excerpt' => 'Konten program akan segera diisi.', 'palette' => $programPalettes['tahfizh'], 'interactive' => false],
                    ['name' => 'Akademik Terpadu', 'excerpt' => 'Konten program akan segera diisi.', 'palette' => $programPalettes['akademik'], 'interactive' => false],
                    ['name' => 'Ekstrakurikuler', 'excerpt' => 'Konten program akan segera diisi.', 'palette' => $programPalettes['unggulan'], 'interactive' => false],
                ]);
            }
        @endphp

        <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200/80 bg-white px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-emerald-700 shadow-sm">
                    <svg class="h-4 w-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.5 2l1.9 4.7a2 2 0 001.2 1.2l4.7 1.9-4.7 1.9a2 2 0 00-1.2 1.2l-1.9 4.7-1.9-4.7a2 2 0 00-1.2-1.2L3.7 9.8l4.7-1.9a2 2 0 001.2-1.2L11.5 2z" /></svg>
                    {{ $settings['program_eyebrow'] ?? 'Keunggulan Kami' }}
                </span>
                <h2 class="mt-5 text-3xl font-extrabold text-emerald-950 lg:text-4xl">
                    {{ $settings['program_title_prefix'] ?? 'Program' }} <span class="bg-gradient-to-r from-emerald-700 to-teal-600 bg-clip-text text-transparent">{{ $settings['program_title_highlight'] ?? 'Unggulan' }}</span>
                </h2>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-slate-600">{{ $settings['program_subtitle'] ?? 'Program-program pilihan yang dirancang untuk mengembangkan potensi akademik dan karakter Islami peserta didik.' }}</p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <span class="h-px w-16 bg-gradient-to-r from-transparent to-emerald-500"></span>
                    <span class="h-2.5 w-2.5 rotate-45 rounded-[3px] bg-amber-500"></span>
                    <span class="h-px w-16 bg-gradient-to-l from-transparent to-emerald-500"></span>
                </div>
            </div>
        </x-reveal>

        {{-- Carousel mobile: geser otomatis ke samping --}}
        <div class="sm:hidden" x-data="{
            timer: null,
            init() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                this.start();
            },
            start() {
                if (this.timer) return;
                const track = this.$refs.track;
                this.timer = setInterval(() => {
                    if (track.scrollWidth <= track.clientWidth + 1) return;
                    const max = track.scrollWidth - track.clientWidth;
                    if (track.scrollLeft >= max - 4) track.scrollTo({ left: 0, behavior: 'smooth' });
                    else track.scrollBy({ left: track.clientWidth, behavior: 'smooth' });
                }, 3200);
            },
            stop() { clearInterval(this.timer); this.timer = null; },
            destroy() { clearInterval(this.timer); },
        }">
            <div x-ref="track"
                 class="-mx-4 mt-12 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2"
                 @mouseenter="stop()" @mouseleave="setTimeout(() => start(), 400)"
                 @touchstart.passive="stop()" @touchend="setTimeout(() => start(), 600)">
                @foreach ($programCards as $card)
                    <div class="w-[82vw] max-w-xs shrink-0 snap-start">
                        @include('frontend.partials.program-card', [
                            'name' => $card['name'],
                            'excerpt' => $card['excerpt'],
                            'content' => $card['content'] ?? null,
                            'icon' => $card['icon'] ?? null,
                            'palette' => $card['palette'],
                            'number' => str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT),
                            'interactive' => $card['interactive'],
                            'defaultIcon' => $defaultIcon,
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-12 hidden gap-6 sm:grid sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($programCards as $card)
                <x-reveal :delay="$loop->index * 90">
                    @include('frontend.partials.program-card', [
                        'name' => $card['name'],
                        'excerpt' => $card['excerpt'],
                        'content' => $card['content'] ?? null,
                        'icon' => $card['icon'] ?? null,
                        'palette' => $card['palette'],
                        'number' => str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT),
                        'interactive' => $card['interactive'],
                        'defaultIcon' => $defaultIcon,
                    ])
                </x-reveal>
            @endforeach
        </div>

        {{-- CTA --}}
        <x-reveal type="zoom">
            <div class="relative mt-14 overflow-hidden rounded-3xl bg-emerald-950 px-8 py-10 text-center shadow-xl sm:px-12 lg:text-left">
                <img src="{{ asset('img/15.png') }}" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full scale-105 object-cover object-center" loading="lazy" />
                <div class="absolute inset-0 bg-[radial-gradient(120%_150%_at_0%_50%,rgba(2,31,22,0.96)_0%,rgba(2,31,22,0.82)_38%,rgba(2,31,22,0.35)_100%)]" aria-hidden="true"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/85 via-transparent to-emerald-950/40" aria-hidden="true"></div>
                <div class="pointer-events-none absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-emerald-400/15 blur-3xl" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-12 h-72 w-72 rounded-full bg-amber-500/10 blur-3xl" aria-hidden="true"></div>
                <div class="relative flex flex-col items-center justify-between gap-6 lg:flex-row">
                    <div class="max-w-xl">
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-[11px] font-bold uppercase tracking-widest text-emerald-200 backdrop-blur-sm">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                            </span>
                            Pendaftaran PPDB Dibuka
                        </span>
                        <h3 class="mt-4 text-2xl font-extrabold text-white drop-shadow-[0_2px_8px_rgba(2,31,22,0.8)] sm:text-3xl">{{ $settings['cta_title'] ?? 'Siap Bergabung dengan Keluarga Besar Kami?' }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-emerald-100/90 drop-shadow-[0_1px_4px_rgba(2,31,22,0.9)]">{{ $settings['cta_text'] ?? 'Daftarkan putra/putri Anda melalui PPDB dan wujudkan impian menjadi generasi hafizh yang berkarakter Islami.' }}</p>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('ppdb.register') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-500/30 transition hover:-translate-y-0.5 hover:bg-amber-400">
                            Daftar PPDB
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </a>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition hover:-translate-y-0.5 hover:brightness-95">
                            Tanya via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </x-reveal>
    </div>
</section>

{{-- ============ FASILITAS ============ --}}
@if ($facilities->isNotEmpty())
    <section id="fasilitas" class="relative overflow-hidden bg-cover bg-center py-20 lg:py-24 lg:bg-fixed" style="background-image: url('{{ asset('img/16.jpeg') }}')">
        <div class="absolute inset-0 bg-gradient-to-b from-emerald-950/90 via-emerald-950/80 to-emerald-950/95"></div>
        <div class="aurora -left-16 top-10 h-72 w-72 bg-emerald-400/20"></div>
        <div class="aurora -right-16 bottom-10 h-80 w-80 bg-amber-400/15" style="animation-delay:-8s"></div>
        <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">{{ $settings['fasilitas_eyebrow'] ?? 'Sarana & Prasarana' }}</span>
                <h2 class="text-gradient-light mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['fasilitas_title'] ?? 'Fasilitas Sekolah' }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-emerald-50/90">{{ $settings['fasilitas_subtitle'] ?? 'Fasilitas lengkap untuk mendukung kenyamanan dan keberhasilan belajar peserta didik.' }}</p>
            </div>
        </x-reveal>
            <div class="mx-auto mt-12 grid max-w-4xl grid-cols-2 gap-4 sm:gap-6">
                @foreach ($facilities as $facility)
                        <x-reveal :delay="$loop->index * 90" class="h-full {{ $loop->last && $facilities->count() % 2 !== 0 ? 'col-span-full' : '' }}">
                            <div class="group relative flex h-full flex-col items-center justify-center overflow-hidden rounded-2xl border border-slate-200 text-center transition duration-300 hover:shadow-xl {{ $facility->image ? 'px-4 py-8 sm:px-8 sm:py-12' : 'glass px-4 py-6 hover:border-emerald-300 hover:bg-white/95 hover:shadow-lg sm:px-8 sm:py-10' }}">
                        @if ($facility->image)
                            <div class="absolute inset-0 scale-105 bg-cover bg-center transition-transform duration-700 ease-out group-hover:scale-110" style="background-image: url('{{ img_url($facility->image) }}')"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/60 to-black/40"></div>
                            <div class="pointer-events-none absolute inset-0 rounded-2xl ring-1 ring-inset ring-white/20"></div>
                        @endif
                        <div class="relative z-10 flex flex-col items-center">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md sm:h-14 sm:w-14 {{ $facility->image ? 'shadow-emerald-900/30 ring-1 ring-white/30 transition group-hover:from-emerald-400 group-hover:to-teal-500' : 'shadow-emerald-600/30 transition group-hover:from-emerald-600 group-hover:to-teal-700' }}">
                                <svg class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $facility->icon ?: $defaultIcon }}" /></svg>
                            </span>
                            <h3 class="mt-3 text-sm font-bold sm:mt-4 sm:text-base {{ $facility->image ? 'text-white' : 'text-emerald-950' }}">{{ $facility->name }}</h3>
                            @if ($facility->description)
                                <p class="mt-2 line-clamp-3 text-xs leading-relaxed {{ $facility->image ? 'text-white/85' : 'text-slate-500' }}">{{ $facility->description }}</p>
                            @endif
                        </div>
                    </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ EKSTRAKURIKULER ============ --}}
@if ($extracurriculars->isNotEmpty())
    <section id="ekskul" class="bg-slate-50 py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['ekskul_eyebrow'] ?? 'Pengembangan Diri' }}</span>
                <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['ekskul_title'] ?? 'Ekstrakurikuler' }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $settings['ekskul_subtitle'] ?? 'Wadah pengembangan bakat, minat, dan soft skill peserta didik di luar jam pelajaran.' }}</p>
            </div>
        </x-reveal>
            @php
                $ekskulCount = $extracurriculars->count();
                $ekskulRem2 = $ekskulCount % 2;
                $ekskulRem3 = $ekskulCount % 3;
            @endphp
            <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-6 lg:gap-5">
                @foreach ($extracurriculars as $ekskul)
                    @php
                        $ekskulLast = $loop->last;
                        $ekskulSecondLast = $loop->index === $ekskulCount - 2;
                        $ekskulSpan = ($ekskulLast && $ekskulRem2 !== 0) ? 'col-span-full ' : '';
                        if ($ekskulRem3 === 1 && $ekskulLast) {
                            $ekskulSpan .= 'md:col-span-full';
                        } elseif ($ekskulRem3 === 2 && ($ekskulLast || $ekskulSecondLast)) {
                            $ekskulSpan .= 'md:col-span-3';
                        } else {
                            $ekskulSpan .= 'md:col-span-2';
                        }
                    @endphp
                    <x-reveal :delay="$loop->index * 70" class="h-full {{ $ekskulSpan }}">
                    <div class="group flex h-full items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-3.5 transition hover:border-amber-400 hover:shadow-md sm:px-4">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30 transition group-hover:from-amber-300 group-hover:to-orange-400 sm:h-10 sm:w-10">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ekskul->icon ?: $defaultIcon }}" /></svg>
                        </span>
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-bold text-emerald-950">{{ $ekskul->name }}</h3>
                            @if ($ekskul->schedule)
                                <p class="mt-0.5 truncate text-[12px] text-slate-500 sm:text-xs">{{ $ekskul->schedule }}</p>
                            @endif
                            @if ($ekskul->advisor)
                                <p class="mt-0.5 truncate text-[12px] font-medium text-emerald-700 sm:text-xs">Pembina: {{ $ekskul->advisor }}</p>
                            @endif
                        </div>
                    </div>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ PRESTASI ============ --}}
@if (! empty($prestasiPhotos))
    <section id="prestasi" class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div x-data="{
                show: false,
                index: 0,
                zoomed: false,
                photos: {{ Js::from($prestasiPhotos) }},
                open(i) { this.index = i; this.zoomed = false; this.show = true; document.body.style.overflow = 'hidden'; },
                close() { this.show = false; this.zoomed = false; document.body.style.overflow = ''; },
                next() { this.index = (this.index + 1) % this.photos.length; this.zoomed = false; },
                prev() { this.index = (this.index - 1 + this.photos.length) % this.photos.length; this.zoomed = false; },
                toggleZoom() { this.zoomed = !this.zoomed; },
            }" @keydown.escape.window="close()"
               @keydown.arrow-right.window="next()" @keydown.arrow-left.window="prev()">
                <x-reveal>
                <div class="text-center">
                    <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['prestasi_eyebrow'] ?? 'Dokumentasi' }}</span>
                    <h3 class="text-gradient mt-2 text-2xl font-extrabold lg:text-3xl">{{ $settings['prestasi_title'] ?? 'Galeri Prestasi' }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $content('prestasi_subtitle', 'Momen kebanggaan siswa-siswi {site_name} dalam berbagai ajang perlombaan.') }}</p>
                </div>
                </x-reveal>

                <div x-data="prestasiSlider({{ Js::from($prestasiPhotos) }})" @mouseenter="paused = true" @mouseleave="paused = false"
                     class="relative mt-10">
                    <div x-ref="viewport" class="overflow-hidden" @touchstart.passive="onTouchStart($event)" @touchend.passive="onTouchEnd($event)">
                        <div x-ref="track" :style="'transform: translateX(-' + offset + 'px)'"
                             class="flex transition-transform duration-700 ease-out">
                            <template x-for="(photo, i) in photos" :key="i">
                                <div class="w-full shrink-0 px-2.5 sm:w-1/2 lg:w-1/3 xl:w-1/4">
                                    <button type="button" @click="open(i)"
                                            class="group relative aspect-square w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm transition hover:-translate-y-1 hover:shadow-lg" :aria-label="photo.caption">
                                        <img :src="photo.url" :alt="photo.caption" loading="lazy"
                                             class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                        <span class="absolute inset-0 grid place-items-center bg-emerald-950/0 transition group-hover:bg-emerald-950/30">
                                            <svg class="h-8 w-8 text-white opacity-0 transition group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m-3-3h6" /></svg>
                                        </span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <template x-if="pages > 1">
                        <div>
                            <button type="button" @click="prev()"
                                    class="absolute -left-4 top-1/2 hidden -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/90 p-2.5 text-slate-600 shadow-lg backdrop-blur transition hover:bg-emerald-600 hover:text-white lg:flex" aria-label="Sebelumnya">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button type="button" @click="next()"
                                    class="absolute -right-4 top-1/2 hidden -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/90 p-2.5 text-slate-600 shadow-lg backdrop-blur transition hover:bg-emerald-600 hover:text-white lg:flex" aria-label="Berikutnya">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                            </button>
                            <div class="mt-6 flex justify-center gap-2">
                                <template x-for="(dot, d) in Array.from({ length: pages })" :key="d">
                                    <button type="button" @click="go(d)"
                                            class="h-2 rounded-full transition-all duration-300"
                                            :class="d === page ? 'w-7 bg-emerald-600' : 'w-2 bg-slate-300 hover:bg-slate-400'" :aria-label="'Halaman ' + (d + 1)"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="show" x-cloak x-transition.opacity.duration.200ms
                     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true">
                    <button type="button" @click="close()" class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/25" aria-label="Tutup">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <button type="button" @click="prev()" x-show="photos.length > 1"
                            class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/25" aria-label="Sebelumnya">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <figure class="flex max-h-full w-full flex-col items-center">
                        <button type="button" @click="toggleZoom()"
                                class="block max-h-full w-full cursor-zoom-in overflow-auto rounded-xl border-0 bg-transparent p-0" aria-label="Perbesar gambar">
                            <img :src="photos[index].url" :alt="photos[index].caption" x-show="photos.length"
                                 :class="zoomed ? 'cursor-zoom-out scale-[1.75]' : 'cursor-zoom-in'"
                                 class="mx-auto max-h-[78vh] w-auto max-w-full rounded-xl object-contain shadow-2xl transition-transform duration-300 will-change-transform"
                                 style="max-width: min(70rem, 100%);">
                        </button>
                        <figcaption class="mt-4 text-center text-sm font-semibold text-white">
                            <span x-text="photos[index].caption"></span>
                            <span class="ml-2 text-white/50" x-text="(index + 1) + ' / ' + photos.length"></span>
                            <span class="mx-auto mt-1.5 block text-xs font-normal text-white/60"
                                  x-text="zoomed ? 'Aktif — klik untuk kembali / geser untuk melihat' : 'Klik gambar untuk memperbesar'"></span>
                        </figcaption>
                    </figure>
                    <button type="button" @click="next()" x-show="photos.length > 1"
                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/25" aria-label="Berikutnya">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- ============ BERITA ============ --}}
@if ($news->isNotEmpty())
    <section id="berita" class="bg-slate-50 py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['berita_eyebrow'] ?? 'Informasi Terbaru' }}</span>
                    <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['berita_title'] ?? 'Berita & Kegiatan' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 sm:block">{{ $content('berita_badge', 'Sumber resmi {site_name}') }}</span>
                    <a href="{{ route('news.index') }}"
                       class="sheen inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-700 px-4 py-2 text-xs font-bold text-white shadow-md shadow-emerald-600/30 transition hover:brightness-110">
                        Semua Berita
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </a>
                </div>
            </div>
        </x-reveal>
            <div class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ($news as $item)
                    <x-reveal :delay="$loop->index * 120">
                    <a href="{{ route('news.show', $item) }}"
                       class="group block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="relative h-52 overflow-hidden bg-slate-100">
                            @if ($item->thumbnail)
                                <img src="{{ img_url($item->thumbnail) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="flex h-full w-full animate-flow-x items-center justify-center bg-[linear-gradient(135deg,#047857,#0f766e,#115e59,#92400e)] text-white">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                                </div>
                            @endif
                            @if ($item->category)
                                <span class="absolute left-3 top-3 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-700 px-3 py-1 text-[12px] font-bold text-white shadow-sm">{{ $item->category->name }}</span>
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
                    </a>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ GALERI ============ --}}
@if ($albums->isNotEmpty() && $albums->contains(fn ($album) => $album->galleries->isNotEmpty()))
    <section id="galeri" class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['galeri_eyebrow'] ?? 'Dokumentasi' }}</span>
                <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['galeri_title'] ?? 'Galeri Kegiatan' }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $settings['galeri_subtitle'] ?? 'Momen-momen berharga dalam kehidupan sekolah kami.' }}</p>
            </div>
        </x-reveal>
            @php
                $gIdx = 0;
                $gTotal = $albums->sum(fn ($album) => $album->galleries->count() > 8 ? 8 : $album->galleries->count());
                $smRem = $gTotal % 2;
                $mdRem = $gTotal % 4;
            @endphp
            <div class="mt-12 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($albums as $album)
                    @foreach ($album->galleries->take($album->galleries->count() > 8 ? 8 : $album->galleries->count()) as $gallery)
                        @php
                            $gIdx++;
                            $span = '';
                            if ($smRem === 1 && $gIdx === $gTotal) {
                                $span .= ' col-span-2';
                            }
                            if ($mdRem > 0 && $gIdx > $gTotal - $mdRem) {
                                $span .= match ($mdRem) {
                                    1 => ' md:col-span-4',
                                    2 => ' md:col-span-2',
                                    3 => $gIdx === $gTotal - 2 ? ' md:col-span-2' : '',
                                    default => '',
                                };
                            }
                        @endphp
                        <x-reveal type="zoom" :delay="$loop->index * 80" class="{{ $span }}">
                        <div class="group relative aspect-video overflow-hidden rounded-2xl">
                            <img src="{{ img_url($gallery->image, 'img/Siang 3.0.png') }}" alt="{{ $gallery->title ?? $album->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 flex items-end bg-gradient-to-t from-emerald-950/80 via-transparent to-transparent p-3 opacity-0 transition group-hover:opacity-100">
                                <span class="text-xs font-semibold text-white">{{ $gallery->title ?? $album->title }}</span>
                            </div>
                        </div>
                        </x-reveal>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ TESTIMONI ============ --}}
@if ($testimonials->isNotEmpty())
    <section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(115deg,#04331f,#065f46,#0f766e,#134e4a,#6b3a10)] py-20 lg:py-24">
        <div class="aurora -left-20 top-16 h-80 w-80 bg-emerald-400/25"></div>
        <div class="aurora right-0 top-1/4 h-96 w-96 bg-amber-400/20" style="animation-delay:-10s"></div>
        <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="mx-auto max-w-2xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">{{ $settings['testimoni_eyebrow'] ?? 'Kata Mereka' }}</span>
                <h2 class="mt-2 text-3xl font-extrabold text-white lg:text-4xl">{{ $settings['testimoni_title'] ?? 'Testimoni Alumni & Wali' }}</h2>
            </div>
        </x-reveal>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <x-reveal :delay="$loop->index * 120">
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
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ PARTNER ============ --}}
@if ($partners->isNotEmpty())
    <section class="border-b border-slate-200 bg-white py-14">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <x-reveal>
            <div class="mx-auto max-w-xl text-center">
                <h3 class="text-lg font-bold text-emerald-950">{{ $settings['partner_title'] ?? 'Mitra & Kerja Sama' }}</h3>
                <p class="mt-2 text-sm text-slate-500">{{ $settings['partner_subtitle'] ?? 'Bersama membangun pendidikan berkualitas.' }}</p>
            </div>
        </x-reveal>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4 sm:gap-5">
                @foreach ($partners as $partner)
                    @if ($partner->logo)
                        <x-reveal type="zoom" :delay="$loop->index * 80">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200 bg-white p-2 shadow-sm transition duration-300 hover:scale-105 hover:border-emerald-300 hover:shadow-md sm:h-20 sm:w-20" title="{{ $partner->name }}">
                            <img src="{{ img_url($partner->logo) }}" alt="{{ $partner->name }}" class="h-full w-full object-contain" loading="lazy">
                        </div>
                        </x-reveal>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ============ KONTAK ============ --}}
<section id="kontak" class="bg-slate-50 py-20 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <x-reveal>
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-gradient text-xs font-bold uppercase tracking-widest">{{ $settings['kontak_eyebrow'] ?? 'Hubungi Kami' }}</span>
            <h2 class="text-gradient mt-2 text-3xl font-extrabold lg:text-4xl">{{ $settings['kontak_title'] ?? 'Kontak Sekolah' }}</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $settings['kontak_subtitle'] ?? 'Silakan hubungi kami untuk informasi lebih lanjut mengenai PPDB dan kegiatan sekolah.' }}</p>
        </div>
        </x-reveal>

        <div class="mt-12 grid gap-8 lg:grid-cols-5">
            <x-reveal type="left" class="space-y-4 lg:col-span-2">
                <div class="space-y-4">
                @foreach ($contacts as $contactItem)
                    <div class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-md shadow-emerald-600/30">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $contactItem['icon'] }}" /></svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-950">{{ $contactItem['label'] }}</h4>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $contactItem['value'] }}</p>
                        </div>
                    </div>
                @endforeach
                <a href="{{ $waLink }}" target="_blank" rel="noopener"
                   class="sheen flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 px-5 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:brightness-110">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat WhatsApp PPDB
                </a>
            </div>
            </x-reveal>

            <x-reveal type="right" :delay="150" class="h-full lg:col-span-3">
            <div class="flex h-full overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                @if (!empty($settings['maps_embed']))
                    <iframe src="{{ $settings['maps_embed'] }}" class="h-full min-h-[400px] w-full" style="border:0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Lokasi {{ $siteName }}"></iframe>
                @else
                    <div class="flex h-full min-h-[400px] w-full flex-col items-center justify-center bg-[linear-gradient(150deg,#04331f,#0f766e,#064e3b,#92400e)] p-8 text-center text-white">
                        <img src="{{ asset('img/Siang 3.0.png') }}" alt="{{ $siteName }}" class="h-28 w-28 rounded-2xl object-cover opacity-90">
                        <h3 class="mt-5 text-lg font-bold">{{ $siteName }}</h3>
                        <p class="mt-2 max-w-sm text-sm text-emerald-100">{{ $settings['address'] ?? '' }}</p>
                    </div>
                @endif
            </div>
            </x-reveal>
        </div>
    </div>
</section>

@endsection
