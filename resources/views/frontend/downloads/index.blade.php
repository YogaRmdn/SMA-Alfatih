@extends('frontend.layouts.app')

@section('title', 'Unduhan')
@section('seo_description', 'Download Berkas '.($settings['site_name'] ?? config('app.name')).': prospectus, formulir pendaftaran, dan dokumen resmi sekolah.')

@push('head')
{!! breadcrumb_schema([
    ['name' => 'Beranda', 'url' => route('home')],
    ['name' => 'Unduhan'],
]) !!}
@endpush

@section('content')

@php
    $grouped = $downloads->getCollection()->groupBy(fn ($d) => $d->category ?: 'Lainnya');
@endphp

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <x-reveal type="fade">
        <nav class="mb-4 flex items-center gap-2 text-sm text-emerald-200">
            <a href="{{ route('home') }}" class="transition hover:text-amber-400">Beranda</a>
            <span>/</span>
            <span class="font-semibold text-white">Unduhan</span>
        </nav>
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
            Berkas Resmi
        </span>
        <h1 class="mt-4 text-3xl font-extrabold text-white lg:text-4xl">Unduhan</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-100/90">
            Kumpulan formulir dan berkas dalam format PDF yang dapat diunduh langsung oleh calon peserta didik dan masyarakat.
        </p>
        </x-reveal>
    </div>
</section>

{{-- ============ DAFTAR FILE ============ --}}
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 lg:px-6">
        @if ($downloads->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                <h3 class="mt-4 font-bold text-slate-700">Belum ada file</h3>
                <p class="mt-1 text-sm text-slate-500">Belum ada berkas yang tersedia untuk diunduh. Silakan cek kembali nanti.</p>
            </div>
        @else
            @foreach ($grouped as $category => $items)
                <div class="mb-10">
                    <div class="mb-4 flex items-center gap-3">
                        <h2 class="text-lg font-extrabold text-emerald-900">{{ $category }}</h2>
                        <span class="h-px flex-1 bg-slate-200"></span>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $items->count() }} file</span>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($items as $download)
                            <x-reveal :delay="$loop->index * 60">
                            <div class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-start gap-4">
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-red-600 to-red-400 text-white shadow-md shadow-red-500/30">
                                        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 14v4l-5-4 5-4v3h4v1h-4zm0-9V3.5L18.5 9H14z" /></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h3 class="font-bold leading-snug text-slate-800">{{ $download->title }}</h3>
                                        @if ($download->description)
                                            <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $download->description }}</p>
                                        @endif
                                        <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-400">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                PDF
                                            </span>
                                            @if ($download->file_size)
                                                <span>&middot; {{ format_bytes($download->file_size) }}</span>
                                            @endif
                                            <span>&middot; {{ number_format($download->downloads) }}x diunduh</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('downloads.download', $download) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                        Unduh PDF
                                    </a>
                                </div>
                            </div>
                            </x-reveal>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="mt-6">
                {{ $downloads->links() }}
            </div>
        @endif
    </div>
</section>

@endsection