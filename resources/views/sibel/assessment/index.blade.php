@extends('sibel.layouts.app')

@section('title', 'Kuesioner Saya')
@section('breadcrumb', 'Kuesioner Saya')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Halo, {{ explode(' ', $student->full_name)[0] }} 👋</h1>
    <p class="mt-1 text-sm text-slate-500">
        Kelas {{ $student->schoolClass->name ?? '-' }} — pilih kuesioner yang tersedia, lalu jawab dengan jujur. Tidak ada jawaban yang benar atau salah.
    </p>
</div>

{{-- Aturan main --}}
<div class="mb-6 flex items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-800">
    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <ul class="ml-1 list-inside list-disc space-y-1">
        <li>Setiap pertanyaan dijawab dengan memilih <strong>satu</strong> pilihan (A, B, atau C).</li>
        <li>Pastikan seluruh pertanyaan terjawab — pengiriman diblokir bila masih ada jawaban kosong.</li>
        <li>Hasil dapat dilihat setelah guru BK mempublikasikannya.</li>
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
                        @unless ($completed->result?->is_published)
                            <span class="ml-1 inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Menunggu publikasi</span>
                        @endunless
                    @else
                        <span class="text-xs text-slate-400">Belum mengisi</span>
                    @endif
                </div>

                <div class="flex shrink-0 gap-2">
                    @if ($inProgress)
                        <a href="{{ route('sibel.assessment.show', $inProgress) }}"
                           class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600">Lanjutkan</a>
                    @elseif ($completed && $completed->result?->is_published)
                        <a href="{{ route('sibel.assessment.result', $completed) }}"
                           class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Lihat Hasil</a>
                    @elseif ($completed)
                        <a href="{{ route('sibel.assessment.result', $completed) }}"
                           class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Status Hasil</a>
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
