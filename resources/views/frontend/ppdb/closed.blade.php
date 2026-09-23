@extends('frontend.layouts.app')

@section('title', 'Pendaftaran Ditutup')

@section('content')
<section class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-xl px-4 text-center lg:px-6">
        <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-lg shadow-amber-500/30">
            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
        </span>
        <h1 class="text-gradient mt-6 text-3xl font-extrabold">Pendaftaran Sedang Ditutup</h1>
        <p class="mt-4 text-sm leading-relaxed text-slate-600">
            Mohon maaf, pendaftaran PPDB {{ $settings['ppdb_tahun_ajaran'] ?? '' }} untuk saat ini belum dibuka /
            telah ditutup. Silakan hubungi kami untuk informasi lebih lanjut.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            @if (!empty($settings['whatsapp']) || !empty($contact?->whatsapp))
                <a href="{{ 'https://wa.me/'.preg_replace('/\D+/', '', $settings['whatsapp'] ?? $contact?->whatsapp) }}" target="_blank" rel="noopener"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-800">
                    Hubungi Kami
                </a>
            @endif
            <a href="{{ route('home') }}#beranda"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</section>
@endsection