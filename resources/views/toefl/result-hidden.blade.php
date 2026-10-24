@extends('toefl.layouts.app')

@section('title', 'Hasil Belum Ditampilkan')

@section('content')
<div class="mx-auto w-full max-w-lg px-4 py-16 text-center">
    <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-200 text-slate-600">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
    </span>
    <h1 class="text-xl font-bold text-slate-800">Ujian Selesai</h1>
    <p class="mt-2 text-sm leading-relaxed text-slate-600">
        Terima kasih, <strong>{{ $attempt->student->full_name }}</strong>. Ujian Anda sudah dikumpulkan.
        Hasil belum dapat ditampilkan karena pengaturan pengawas ujian menyembunyikan hasil —
        silakan tunggu pengumuman dari guru/pengawas.
    </p>
    <form method="POST" action="{{ route('toefl.leave') }}" class="mt-6 inline-block">
        @csrf
        <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
            Selesai (Keluar Sesi)
        </button>
    </form>
</div>
@endsection
