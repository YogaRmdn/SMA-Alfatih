@extends('frontend.layouts.app')

@section('title', 'Pendaftaran Berhasil')

@section('content')
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(150deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-20 lg:py-28">
    <div class="aurora -left-20 top-16 h-80 w-80 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-0 h-80 w-80 bg-teal-300/20" style="animation-delay:-10s"></div>
    <div class="relative mx-auto max-w-2xl px-4 lg:px-6">
        <div class="rounded-3xl border border-white/10 bg-white/10 p-8 text-center backdrop-blur lg:p-12">
            <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-400/20 text-emerald-300">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </span>

            <h1 class="mt-6 text-2xl font-extrabold text-white lg:text-3xl">Pendaftaran Berhasil!</h1>
            <p class="mt-3 text-sm leading-relaxed text-emerald-100/90">
                Terima kasih, <span class="font-semibold text-white">{{ $ppdb->full_name }}</span>.
                Data pendaftaran Anda telah kami terima dan sedang dalam proses verifikasi.
            </p>

            <div class="mt-8 rounded-2xl border border-amber-400/40 bg-amber-400/10 p-6">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-300">No. Registrasi Anda</p>
                <p class="mt-2 break-all text-3xl font-extrabold tracking-wide text-amber-400 sm:text-4xl">{{ $ppdb->registration_number }}</p>

                <div class="mt-4 border-t border-amber-400/20 pt-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-amber-300">Kode Akses Anda</p>
                    <p class="mt-2 font-mono text-2xl font-extrabold tracking-[0.25em] text-white">{{ $ppdb->access_code }}</p>
                </div>

                <p class="mt-2 text-xs text-emerald-200">
                    Tahun Ajaran {{ $ppdb->academic_year }} &middot; Status: {{ $ppdb->statusLabel() }}
                </p>
            </div>

            <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 px-5 py-4 text-left">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-200">Penting</p>
                <ul class="mt-3 space-y-2 text-sm leading-relaxed text-emerald-100/90" x-data="{ copied: false }">
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400">&raquo;</span>
                        <span>Simpan <span class="font-semibold text-white">No. Registrasi</span> dan <span class="font-semibold text-white">Kode Akses</span> di atas untuk memeriksa status pendaftaran.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400">&raquo;</span>
                        <span>Kode akses bersifat rahasia dan hanya diketahui Anda sebagai pendaftar — jangan bagikan kepada orang lain.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400">&raquo;</span>
                        <span>Status pendaftaran dapat dicek kapan saja melalui halaman <a href="{{ route('ppdb.status') }}" class="font-semibold text-amber-300 underline decoration-amber-400/60 underline-offset-2">Cek Status Pendaftaran</a>.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-amber-400">&raquo;</span>
                        <span>Petugas kami dapat menghubungi Anda jika ada data yang perlu dilengkapi.</span>
                    </li>
                </ul>
            </div>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('ppdb.status') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-emerald-950 shadow-lg shadow-amber-500/30 transition hover:bg-amber-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Cek Status Pendaftaran
                </a>
                <a href="{{ route('home') }}#beranda"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>
@endsection