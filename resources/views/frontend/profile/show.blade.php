@extends('frontend.layouts.app')

@section('title', $page?->title ?? $meta['label'])

@section('content')

@php
    $profileLinks = [
        ['label' => 'Tentang & Sejarah', 'href' => route('profil.tentang')],
        ['label' => 'Visi Misi', 'href' => route('profil.visi-misi')],
        ['label' => 'Struktur Organisasi', 'href' => route('profil.struktur')],
    ];
@endphp

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <x-reveal type="fade">
        <nav class="mb-4 flex flex-wrap items-center gap-2 text-sm text-emerald-200">
            <a href="{{ route('home') }}" class="transition hover:text-amber-400">Beranda</a>
            <span>/</span>
            <span>Profil Sekolah</span>
            <span>/</span>
            <span class="font-semibold text-white">{{ $page?->title ?? $meta['label'] }}</span>
        </nav>
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            Profil Sekolah
        </span>
        <h1 class="mt-4 text-3xl font-extrabold text-white lg:text-4xl">{{ $page?->title ?? $meta['label'] }}</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-100/90">{{ $meta['description'] }}</p>
        </x-reveal>
    </div>
</section>

{{-- ============ KONTEN ============ --}}
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-5xl px-4 lg:px-6">
        <x-reveal type="fade">
        <div class="mb-8 flex flex-wrap items-center justify-center gap-2">
            @foreach ($profileLinks as $link)
                <a href="{{ $link['href'] }}" class="rounded-lg px-4 py-2 text-sm font-semibold transition {{ url()->current() === $link['href'] ? 'bg-emerald-700 text-white shadow-md shadow-emerald-700/30' : 'border border-slate-200 bg-white text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        </x-reveal>

        <x-reveal type="up">
        @if ($page?->slug === 'tentang-sejarah' && $page?->image)
            <div class="grid items-center gap-14 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:gap-10">
                {{-- KARTU FOTO (trapesium) --}}
                <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
                    <div class="absolute inset-0 translate-x-4 translate-y-4 rounded-xl bg-gradient-to-br from-emerald-800 via-teal-700 to-cyan-600"
                         style="clip-path: polygon(0 0, 100% 0, calc(100% - 2.5rem) 100%, 0 100%);"></div>
                    <div class="relative overflow-hidden rounded-xl bg-slate-200"
                         style="clip-path: polygon(0 0, 100% 0, calc(100% - 2.5rem) 100%, 0 100%); filter: drop-shadow(0 24px 30px rgba(2,44,34,.28));">
                        <img src="{{ img_url($page->image) }}" alt="{{ $page->title }}" class="aspect-[4/3] w-full object-cover" loading="lazy">
                    </div>
                    <span class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-white/90 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-800 shadow-md backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-gradient-to-r from-amber-400 to-orange-500"></span>
                        Profil Sekolah
                    </span>
                </div>

                {{-- KARTU TEKS (trapesium, menjalin ke tengah) --}}
                <div class="relative mx-auto w-full max-w-xl lg:max-w-none">
                    <div class="rounded-xl bg-white pr-6 pb-16 pl-11 pt-6 sm:pr-9 sm:pb-20 sm:pl-11 sm:pt-9 lg:pr-10 lg:pb-24 lg:pl-11 lg:pt-10"
                         style="clip-path: polygon(0 0, 100% 0, 100% 100%, 2.5rem 100%); filter: drop-shadow(0 24px 30px rgba(15,23,42,.10));">
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            {{ $page->title }}
                        </span>
                        <div class="mt-5 lg:mt-6">
                            @include('frontend.profile.partials.content', ['page' => $page])
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if ($page?->image)
                    <div class="bg-slate-100 p-4 lg:p-8">
                        <img src="{{ img_url($page->image) }}" alt="{{ $page->title }}" class="mx-auto max-h-[440px] w-auto max-w-full rounded-xl object-contain">
                    </div>
                @endif
                <div class="p-6 lg:p-10">
                    @include('frontend.profile.partials.content', ['page' => $page])
                </div>
            </div>
        @endif
        </x-reveal>
    </div>
</section>

@endsection