@extends('frontend.layouts.app')

@section('title', $newsTitle)
@section('description', $news->excerpt)
@section('og_type', 'article')
@section('og_image', $news->thumbnail ? img_url($news->thumbnail) : '')

@push('head')
@php
    $newsSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $news->title,
        'description' => $news->excerpt,
        'datePublished' => $news->published_at?->toIso8601String(),
        'dateModified' => $news->updated_at?->toIso8601String(),
        'inLanguage' => 'id-ID',
        'image' => $news->thumbnail ? img_url($news->thumbnail) : null,
        'author' => ['@type' => 'Organization', 'name' => $siteName],
        'publisher' => ['@type' => 'EducationalOrganization', 'name' => $siteName],
        'mainEntityOfPage' => url()->current(),
    ];
    $newsSchema = array_filter($newsSchema, fn ($value) => $value !== null);
@endphp
<script type="application/ld+json">
{!! json_encode($newsSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@section('content')

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-4xl px-4 lg:px-6">
        <x-reveal type="fade">
        <nav class="mb-4 flex items-center gap-2 text-sm text-emerald-200">
            <a href="{{ route('home') }}" class="transition hover:text-amber-400">Beranda</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="transition hover:text-amber-400">Berita</a>
            @if ($news->category)
                <span>/</span>
                <a href="{{ route('news.index', ['kategori' => $news->category->slug]) }}" class="transition hover:text-amber-400">{{ $news->category->name }}</a>
            @endif
        </nav>
        @if ($news->category)
            <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">{{ $news->category->name }}</span>
        @endif
        <h1 class="mt-4 text-3xl font-extrabold leading-tight text-white lg:text-4xl">{{ $news->title }}</h1>
        <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-emerald-100/90">
            @if ($news->author)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    {{ $news->author->name }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                {{ $news->published_at?->translatedFormat('d F Y, H:i') }}
            </span>
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                {{ number_format($news->views) }}x dilihat
            </span>
        </div>
        </x-reveal>
    </div>
</section>

{{-- ============ ARTIKEL ============ --}}
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-4xl px-4 lg:px-6">
        <x-reveal type="up" :delay="80">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @if ($news->thumbnail)
                <div class="h-64 w-full overflow-hidden bg-slate-100 sm:h-80 lg:h-96">
                    <img src="{{ img_url($news->thumbnail) }}" alt="{{ $news->title }}" class="h-full w-full object-cover">
                </div>
            @endif
            <article class="p-6 sm:p-10">
                @if ($news->excerpt)
                    <p class="mb-6 border-l-4 border-amber-400 pl-4 text-base font-medium leading-relaxed text-emerald-900 italic">{{ $news->excerpt }}</p>
                @endif
                <div class="prose prose-emerald max-w-none prose-headings:text-emerald-950 prose-a:text-emerald-700 prose-img:rounded-xl">
                    {!! $news->content !!}
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-6">
                    <a href="{{ route('news.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:border-emerald-600 hover:text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Berita
                    </a>
                    <a href="{{ route('ppdb.register') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-bold text-emerald-950 transition hover:bg-amber-400">
                        Daftar PPDB Sekarang
                    </a>
                </div>
            </article>
        </div>
        </x-reveal>

        {{-- ============ KOMENTAR ============ --}}
        <x-reveal type="up" :delay="120">
        <div class="mt-10 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-extrabold text-emerald-950">
                Komentar
                <span class="ml-2 text-sm font-bold text-slate-400">{{ $comments->total() }}</span>
            </h2>

            {{-- Form komentar --}}
            <form method="POST" action="{{ route('news.comment', $news) }}" class="mt-6 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="150"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email <span class="text-xs font-normal text-slate-400">(opsional)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" maxlength="150"
                               class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="content" class="mb-1.5 block text-sm font-semibold text-slate-700">Komentar</label>
                    <textarea id="content" name="content" rows="4" required maxlength="2000"
                              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20"
                              placeholder="Tulis komentar Anda…">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                    Kirim Komentar
                </button>
                <p class="text-xs text-slate-400">Komentar akan tampil setelah disetujui admin.</p>
            </form>

            {{-- Daftar komentar --}}
            <div class="mt-8 space-y-5">
                @forelse ($comments as $comment)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">{{ strtoupper(substr($comment->name, 0, 1)) }}</span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $comment->name }}</p>
                                <p class="text-xs text-slate-400">{{ $comment->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $comment->content }}</p>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-200 py-8 text-center text-sm text-slate-400">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $comments->links() }}
            </div>
        </div>
        </x-reveal>

        {{-- ============ BERITA TERKAIT ============ --}}
        @if ($relatedNews->isNotEmpty())
            <div class="mt-12">
                <x-reveal><h2 class="text-xl font-extrabold text-emerald-950">Berita Terkait</h2></x-reveal>
                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    @foreach ($relatedNews as $item)
                        <x-reveal :delay="$loop->index * 100" class="flex">
                        <a href="{{ route('news.show', $item) }}"
                           class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="relative h-36 overflow-hidden bg-slate-100">
                                @if ($item->thumbnail)
                                    <img src="{{ img_url($item->thumbnail) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-700 to-teal-900 text-white">
                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-4">
                                <p class="text-xs text-slate-400">{{ $item->published_at?->translatedFormat('d F Y') }}</p>
                                <h3 class="mt-1 line-clamp-2 text-sm font-bold leading-snug text-emerald-950 group-hover:text-emerald-700">{{ $item->title }}</h3>
                            </div>
                        </a>
                        </x-reveal>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

@endsection