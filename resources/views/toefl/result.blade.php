@extends('toefl.layouts.app')

@section('title', 'Hasil Ujian TOEFL')

@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-8">
    {{-- Sapaan + status --}}
    <div class="mb-6 text-center">
        <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
        </span>
        <h1 class="text-2xl font-bold text-slate-800">Hasil Ujian TOEFL ITP</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $exam->title }} — {{ $attempt->student->full_name }}</p>
        <span class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            {{ $attempt->statusLabel() }}
            @if ($attempt->submitted_at)
                · {{ $attempt->submitted_at->format('d M Y H:i') }}
            @endif
        </span>
    </div>

    @if ($result)
        {{-- Skor scaled --}}
        <div class="mb-5 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-blue-700 p-6 text-center text-white shadow-lg shadow-indigo-900/20">
            <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Skor Scaled (Latihan)</p>
            <p class="mt-1 text-5xl font-extrabold tabular-nums">{{ $result->scaled_score }}</p>
            <p class="mt-1 text-xs text-indigo-100">Rentang 310–680 · aproksimasi, bukan skor resmi ETS</p>
        </div>

        {{-- Rincian per bagian --}}
        <div class="mb-5 grid gap-4 sm:grid-cols-3">
            @foreach ($result->sectionsBreakdown() as $type => $data)
                @php
                    $totalSoal = $exam->sections->firstWhere('section_type', $type)?->questions()->count() ?? 0;
                    $pct = $totalSoal > 0 ? round(($data['raw'] / $totalSoal) * 100) : 0;
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $data['label'] }}</p>
                    <p class="mt-1 text-2xl font-extrabold tabular-nums text-slate-800">
                        {{ $data['raw'] }}<span class="text-sm font-medium text-slate-400">/{{ $totalSoal }}</span>
                    </p>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-indigo-600" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $pct }}% benar</p>
                </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Total Jawaban Benar</p>
                    <p class="text-xs text-slate-500">dari {{ $result->total_questions }} soal</p>
                </div>
                <p class="text-3xl font-extrabold tabular-nums text-slate-800">
                    {{ $result->total_correct }}<span class="text-base font-medium text-slate-400">/{{ $result->total_questions }}</span>
                </p>
            </div>
            <div class="mt-3 h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-emerald-600" style="width: {{ $result->percentage() }}%"></div>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            Hasil sedang diproses. Muat ulang halaman bila belum muncul dalam beberapa saat.
        </div>
    @endif

    {{-- Catatan pengawasan (peristiwa browser) --}}
    @if ($events->isNotEmpty())
        <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-semibold text-slate-800">Catatan Selama Ujian</p>
            <p class="mt-0.5 text-xs text-slate-500">Perpindahan tab & layar penuh tercatat sebagai catatan — bukan bukti kecurangan.</p>
            <ul class="mt-3 space-y-1.5">
                @foreach ($events->take(10) as $event)
                    <li class="flex items-center justify-between text-xs text-slate-600">
                        <span>{{ $event->typeLabel() }}</span>
                        <span class="font-mono text-slate-400">{{ $event->event_timestamp->format('H:i:s') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600">
        {{ config('toefl.ethics_note') }}
    </div>

    <div class="mt-6 flex flex-wrap justify-center gap-3">
        <a href="{{ route('toefl.result.print') }}"
           class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
           target="_blank">
            Cetak Hasil
        </a>
        <form method="POST" action="{{ route('toefl.leave') }}">
            @csrf
            <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-800">
                Selesai (Keluar Sesi)
            </button>
        </form>
    </div>
</div>
@endsection
