@extends('sibel.layouts.app')

@section('title', 'Data Siswa Belum Ada')
@section('breadcrumb', 'Kuesioner Saya')

@section('content')
<div class="mx-auto max-w-xl rounded-2xl border border-amber-200 bg-white p-8 text-center shadow-sm">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-600">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.34 16a2 2 0 001.73 3z" /></svg>
    </span>
    <h1 class="mt-4 text-xl font-bold text-slate-800">Data Siswa Belum Terdaftar</h1>
    <p class="mt-2 text-sm text-slate-500">
        Akun Anda belum terhubung dengan data siswa di SIBEL, sehingga kuesioner belum bisa diisi.
        Silakan hubungi guru BK agar data Anda didaftarkan terlebih dahulu.
    </p>
    <a href="{{ route('home') }}" class="mt-6 inline-flex rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
        Kembali ke Website
    </a>
</div>
@endsection
