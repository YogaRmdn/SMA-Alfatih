@extends('frontend.layouts.app')

@section('title', $settings['berita_title'] ?? 'Berita & Kegiatan')
@section('seo_description', 'Kabar terbaru, kegiatan, prestasi, dan pengumuman resmi dari '.($settings['site_name'] ?? config('app.name')).' Pekanbaru.')

@push('head')
{!! breadcrumb_schema([
    ['name' => 'Beranda', 'url' => route('home')],
    ['name' => $settings['berita_title'] ?? 'Berita & Kegiatan'],
]) !!}
@endpush

@section('content')

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <x-reveal type="fade">
        <nav class="mb-4 flex items-center gap-2 text-sm text-emerald-200">
            <a href="{{ route('home') }}" class="transition hover:text-amber-400">Beranda</a>
            <span>/</span>
            <span class="font-semibold text-white">{{ $settings['berita_title'] ?? 'Berita & Kegiatan' }}</span>
        </nav>
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
            {{ $settings['berita_eyebrow'] ?? 'Informasi Terbaru' }}
        </span>
        <h1 class="mt-4 text-3xl font-extrabold text-white lg:text-4xl">{{ $settings['berita_title'] ?? 'Berita & Kegiatan' }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-100/90">
            Sumber informasi resmi seputar kegiatan, prestasi, dan pengumuman {{ $settings['site_name'] ?? config('app.name') }}.
        </p>
        </x-reveal>
    </div>
</section>

{{-- ============ KONTEN ============ --}}
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        <div class="grid gap-8 lg:grid-cols-4">
            {{-- Daftar berita --}}
            <div class="lg:col-span-3">
                @if ($news->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                        <h3 class="mt-4 font-bold text-slate-700">Belum ada berita</h3>
                        <p class="mt-1 text-sm text-slate-500">Belum ada berita yang dipublikasikan pada kategori ini.</p>
                    </div>
                @else
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($news as $item)
                            <x-reveal :delay="$loop->index * 100" class="flex">
                            <a href="{{ route('news.show', $item) }}"
                               class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="relative h-44 overflow-hidden bg-slate-100">
                                    @if ($item->thumbnail)
                                        <img src="{{ img_url($item->thumbnail) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full w-full animate-flow-x items-center justify-center bg-[linear-gradient(135deg,#047857,#0f766e,#115e59,#92400e)] text-white">
                                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                                        </div>
                                    @endif
                                    @if ($item->category)
                                        <span class="absolute left-3 top-3 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-700 px-2.5 py-1 text-[12px] font-bold text-white">{{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <div class="flex items-center gap-2 text-xs text-slate-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        {{ $item->published_at?->translatedFormat('d F Y') ?? $item->created_at->translatedFormat('d F Y') }}
                                        @if ($item->author)
                                            <span class="mx-1 h-3 w-px bg-slate-300"></span>
                                            {{ $item->author->name }}
                                        @endif
                                    </div>
                                    <h3 class="mt-2 line-clamp-2 text-base font-bold leading-snug text-emerald-950 group-hover:text-emerald-700">{{ $item->title }}</h3>
                                    @if ($item->excerpt)
                                        <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-500">{{ $item->excerpt }}</p>
                                    @endif
                                    <span class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 group-hover:text-emerald-800">
                                        Baca Selengkapnya
                                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                    </span>
                                </div>
                            </a>
                            </x-reveal>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $news->links() }}
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6 lg:col-span-1">
                {{-- Pencarian --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-950">Cari Berita</h3>
                    <form method="GET" action="{{ route('news.index') }}" class="mt-3 flex gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci…"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20">
                        <button type="submit" class="shrink-0 rounded-xl bg-emerald-700 px-3.5 text-white transition hover:bg-emerald-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </button>
                    </form>
                </div>

                {{-- Kategori --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-950">Kategori</h3>
                    <ul class="mt-3 space-y-1">
                        <li>
                            <a href="{{ route('news.index') }}"
                               class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition {{ request('kategori') ? 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' : 'bg-emerald-50 font-bold text-emerald-800' }}">
                                Semua Berita
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">{{ $news->total() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('news.index', ['kategori' => $category->slug] + (request('q') ? ['q' => request('q')] : [])) }}"
                                   class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition {{ request('kategori') === $category->slug ? 'bg-emerald-50 font-bold text-emerald-800' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                                    {{ $category->name }}
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">{{ $category->news_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection