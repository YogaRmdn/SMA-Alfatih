@extends('sibel.layouts.student')

@section('title', 'Dashboard Kuesioner Saya')

@php
    $completedCount = $attempts->flatten(1)->where('status', \App\Models\SibelAttempt::STATUS_COMPLETED)->count();
    $totalQuestionnaires = $questionnaires->count();
@endphp

@section('content')
{{-- Sapaan + ringkasan --}}
<div class="mb-6 rounded-2xl border border-white/10 bg-gradient-to-r from-emerald-900 via-teal-800 to-emerald-950 p-6 text-white shadow-lg">
    <h1 class="text-2xl font-extrabold">Halo, {{ explode(' ', $student->full_name)[0] }} 👋</h1>
    <p class="mt-1 text-sm text-emerald-100/90">
        Kelas {{ $student->schoolClass->name ?? '—' }}{{ $student->student_number ? ' • No. Induk '.$student->student_number : '' }}
        — pilih kuesioner di bawah, lalu jawab dengan jujur. Tidak ada jawaban yang benar atau salah.
    </p>

    <div class="mt-4 flex flex-wrap gap-3">
        <span class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm ring-1 ring-white/15">
            <svg class="h-4 w-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            <span><span class="font-bold">{{ $completedCount }}</span> dari {{ $totalQuestionnaires }} kuesioner selesai</span>
        </span>
        <form method="POST" action="{{ route('sibel.forget') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2 text-sm ring-1 ring-white/15 transition hover:bg-white/20">
                <svg class="h-4 w-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                Ganti Siswa
            </button>
        </form>
    </div>
</div>

{{-- Aturan main --}}
<div class="mb-6 flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <ul class="ml-1 list-inside list-disc space-y-1">
        <li>Setiap pertanyaan dijawab dengan memilih <strong>satu</strong> pilihan (A, B, atau C).</li>
        <li>Pastikan seluruh pertanyaan terjawab — pengiriman diblokir bila masih ada jawaban kosong.</li>
        <li>Setelah dikirim, <strong>hasil & rekomendasi belajar langsung tampil</strong> dan bisa diunduh / dicetak (PDF).</li>
    </ul>
</div>

<div class="grid gap-4 lg:grid-cols-2">
    @forelse ($questionnaires as $questionnaire)
        @php
            $attemptList = $attempts->get($questionnaire->id, collect());
            $inProgress = $attemptList->firstWhere('status', \App\Models\SibelAttempt::STATUS_IN_PROGRESS);
            $completed = $attemptList->where('status', \App\Models\SibelAttempt::STATUS_COMPLETED)->first();
        @endphp

        <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </span>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $questionnaire->statusColor() }}">{{ $questionnaire->statusLabel() }}</span>
            </div>

            <h3 class="mt-3 text-base font-bold text-slate-800">{{ $questionnaire->title }}</h3>
            <p class="mt-1 text-xs text-slate-500">v{{ $questionnaire->version }} • {{ $questionnaire->questions_count }} pertanyaan</p>

            <div class="mt-4 flex flex-1 items-end justify-between gap-2">
                <div>
                    @if ($inProgress)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span> Sedang dikerjakan
                        </span>
                    @elseif ($completed)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Selesai {{ optional($completed->completed_at)->format('d/m/Y') }}
                        </span>
                    @else
                        <span class="text-xs text-slate-400">Belum mengisi</span>
                    @endif
                </div>

                <div class="flex shrink-0 gap-2">
                    @if ($inProgress)
                        <a href="{{ route('sibel.assessment.show', $inProgress) }}"
                           class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600">Lanjutkan</a>
                    @elseif ($completed)
                        <a href="{{ route('sibel.assessment.result', $completed) }}"
                           class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Lihat Hasil &amp; Unduh</a>
                    @elseif ($questionnaire->isActive())
                        <form method="POST" action="{{ route('sibel.assessment.start', $questionnaire) }}">
                            @csrf
                            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Mulai Mengisi</button>
                        </form>
                    @else
                        <span class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-400">Sesi ditutup</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Belum ada kuesioner yang tersedia. Tunggu pengumuman dari guru BK.
        </div>
    @endforelse
</div>
@endsection
