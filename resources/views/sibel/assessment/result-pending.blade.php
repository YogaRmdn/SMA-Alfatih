@extends('sibel.layouts.app')

@section('title', 'Menunggu Publikasi Hasil')
@section('breadcrumb', 'Hasil Kuesioner')

@section('content')
<div class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-sky-100 text-sky-600">
        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    </span>
    <h1 class="mt-4 text-xl font-bold text-slate-800">Jawaban Sudah Dikirim 🎉</h1>
    <p class="mt-2 text-sm text-slate-500">
        Terima kasih! Jawabanmu untuk <span class="font-semibold">{{ $attempt->questionnaire->title }}</span>
        ({{ optional($attempt->completed_at)->format('d/m/Y H:i') }}) sudah kami terima dan sedang ditinjau.
    </p>
    <p class="mt-3 rounded-xl bg-sky-50 p-3 text-sm text-sky-800">
        Hasil akan tampil setelah guru BK mempublikasikannya. Halaman ini akan menampilkan skor, interpretasi, dan rekomendasi belajarmu.
    </p>

    <div class="mt-6 flex justify-center gap-2">
        <a href="{{ route('sibel.assessment.index') }}" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
            Kembali ke Kuesioner Saya
        </a>
        <button type="button" onclick="location.reload()" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
            Periksa Lagi
        </button>
    </div>
</div>
@endsection
