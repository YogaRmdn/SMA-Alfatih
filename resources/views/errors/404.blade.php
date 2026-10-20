@extends('frontend.layouts.app')

@section('title', 'Halaman Tidak Ditemukan (404)')
@section('robots', 'noindex, nofollow')
@section('seo_description', 'Halaman yang Anda tuju tidak ditemukan. Kembali ke beranda untuk menjelajahi konten lain.')

@section('content')
@php
    $requestedPath = '/'.request()->path();
    $ppdbOpen = ($settings['ppdb_open'] ?? '0') === '1';
    $waNumber = preg_replace('/\D+/', '', $settings['whatsapp'] ?? $contact?->whatsapp ?? '');
    $waLink = $waNumber ? 'https://wa.me/'.(str_starts_with($waNumber, '0') ? '62'.substr($waNumber, 1) : $waNumber) : null;

    $quickLinks = [
        ['label' => 'Beranda', 'href' => route('home').'#beranda', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Profil Sekolah', 'href' => route('profil.tentang'), 'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222'],
        ['label' => 'Berita', 'href' => route('news.index'), 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
        ['label' => 'Unduhan', 'href' => route('downloads.index'), 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
        ['label' => 'Kontak', 'href' => route('home').'#kontak', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
    ];
@endphp

<section class="relative overflow-hidden bg-[linear-gradient(160deg,#04271a,#0b4f3a_30%,#115e59_55%,#052e16_80%,#3d2308)] py-16 sm:py-20 lg:py-24">
    <div class="aurora -left-24 top-10 h-80 w-80 bg-teal-400/20"></div>
    <div class="aurora right-0 top-1/3 h-96 w-96 bg-amber-400/15" style="animation-delay:-9s"></div>
    <div class="aurora bottom-0 left-1/3 h-72 w-72 bg-emerald-400/20" style="animation-delay:-15s"></div>

    <div class="relative mx-auto max-w-2xl px-4 sm:px-6">
        {{-- Kartu utama --}}
        <div class="glass rounded-3xl border border-white/60 p-7 text-center shadow-2xl shadow-emerald-950/40 sm:p-10">
            {{-- Ikon --}}
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-lg shadow-amber-500/30">
                <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>

            <p class="text-gradient mt-5 text-6xl font-extrabold tracking-tight sm:text-7xl">404</p>

            <h1 class="mt-2 text-2xl font-extrabold text-emerald-950 sm:text-3xl">Halaman Tidak Ditemukan</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-relaxed text-slate-600">
                Maaf, halaman yang Anda tuju tidak ada atau sudah dipindahkan.
                Mungkin tautannya salah ketik, kedaluwarsa, atau sudah tidak tersedia lagi.
            </p>

            @if ($requestedPath !== '/')
                <p class="mt-4 inline-flex max-w-full items-center gap-2 rounded-lg bg-emerald-950/90 px-3 py-2 text-left text-xs font-semibold text-emerald-200">
                    <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    <code class="break-all">{{ $requestedPath }}</code>
                </p>
            @endif

            {{-- Aksi utama --}}
            <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                @auth
                    <a href="{{ route('dashboard') }}" class="sheen inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-md shadow-amber-500/30 transition hover:brightness-105">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        Buka Dashboard
                    </a>
                    <a href="{{ route('home') }}#beranda" class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-900/15 bg-white/80 px-6 py-3 text-sm font-bold text-emerald-950 transition hover:bg-white">
                        Kembali ke Beranda
                    </a>
                @else
                    <a href="{{ route('home') }}#beranda" class="sheen inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-md shadow-amber-500/30 transition hover:brightness-105">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        Kembali ke Beranda
                    </a>
                    <button type="button" onclick="history.length > 1 ? history.back() : window.location.assign('{{ route('home') }}#beranda')"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-emerald-900/15 bg-white/80 px-6 py-3 text-sm font-bold text-emerald-950 transition hover:bg-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" /></svg>
                        Halaman Sebelumnya
                    </button>
                @endauth
            </div>

            {{-- Pencarian berita --}}
            <form method="GET" action="{{ route('news.index') }}" class="mx-auto mt-7 flex max-w-md gap-2">
                <label for="q-404" class="sr-only">Cari informasi</label>
                <input type="text" id="q-404" name="q" value="{{ request('q') }}" placeholder="Cari informasi di berita…"
                       class="min-w-0 flex-1 rounded-xl border border-emerald-900/15 bg-white/90 px-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-600 focus:ring-emerald-600">
                <button type="submit" class="shrink-0 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                    Cari
                </button>
            </form>
        </div>

        {{-- Menu cepat --}}
        <div class="mt-8">
            <p class="text-center text-xs font-bold uppercase tracking-widest text-emerald-300/90">Atau kunjungi</p>
            <div class="mt-4 flex flex-wrap justify-center gap-2.5">
                @foreach ($quickLinks as $link)
                    <a href="{{ $link['href'] }}" class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white hover:text-emerald-950">
                        <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" /></svg>
                        {{ $link['label'] }}
                    </a>
                @endforeach
                @if ($ppdbOpen)
                    <a href="{{ route('ppdb.register') }}" class="sheen inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-2.5 text-sm font-bold text-emerald-950 shadow-md shadow-amber-500/30 transition hover:brightness-105">
                        Daftar PPDB
                    </a>
                @endif
            </div>
        </div>

        {{-- Bantuan --}}
        <div class="mt-8 flex flex-col items-center justify-center gap-3 text-center text-sm text-emerald-200/90 sm:flex-row">
            <span>Ada halaman yang seharusnya ada? Beri tahu kami.</span>
            @if ($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/20">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.149-.744.149-.273.273-1.056.966-1.295 1.164-.239.199-.477.224-.75.075-.273-.149-1.155-.428-2.2-.75-.819-.251-1.476-.563-2.095-1.138-.619-.575-1.043-1.284-1.167-1.493-.124-.209-.013-.323.088-.422.09-.09.199-.228.299-.342.099-.114.133-.193.199-.322.066-.129.033-.239-.017-.338-.049-.099-.744-1.795-1.021-2.457-.27-.648-.543-.56-.744-.569-.194-.009-.42-.014-.645-.014-.225 0-.585.084-.891.419-.306.334-1.167 1.142-1.167 2.785s1.196 3.243 1.362 3.468c.166.225 2.353 3.592 5.698 5.034.794.32 1.416.511 1.899.656.796.241 1.523.207 2.097.126.639-.09 1.955-.797 2.23-1.567.275-.77.275-1.43.192-1.567-.083-.137-.306-.225-.612-.388M12.05 21.785h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884a9.82 9.82 0 016.988 2.898 9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                    Hubungi Sekolah
                </a>
            @endif
        </div>
    </div>
</section>
@endsection
