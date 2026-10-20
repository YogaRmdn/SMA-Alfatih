@extends('sibel.layouts.student')

@section('title', 'Hasil Kuesioner Saya')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Hasil Kuesioner Gaya Belajar</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $attempt->questionnaire->title }} • v{{ $result->questionnaire_version ?? $attempt->questionnaire->version }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sibel.assessment.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">← Kembali</a>
        <a href="{{ route('sibel.assessment.print', $attempt) }}" target="_blank" rel="noopener"
           class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            🖨️ Cetak / Simpan PDF
        </a>
    </div>
</div>

{{-- Identitas --}}
<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-800">Identitas</h2>
    <dl class="mt-3 grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
        <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
            <dt class="text-slate-500">Nama Siswa</dt>
            <dd class="font-semibold text-slate-800">{{ $attempt->student->full_name }}</dd>
        </div>
        <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
            <dt class="text-slate-500">Kelas</dt>
            <dd class="font-semibold text-slate-800">{{ $attempt->student->schoolClass->name ?? '—' }}</dd>
        </div>
        <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
            <dt class="text-slate-500">Tanggal Pengisian</dt>
            <dd class="font-semibold text-slate-800">{{ optional($result->created_at)->format('d F Y') }}</dd>
        </div>
        <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
            <dt class="text-slate-500">Versi Instrumen</dt>
            <dd class="font-semibold text-slate-800">v{{ $result->questionnaire_version ?? '—' }}</dd>
        </div>
    </dl>
</div>

{{-- Skor + grafik --}}
<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-800">Hasil Skor</h2>

    <div class="mt-4">
        <canvas id="chartResult" style="display: none" height="200"></canvas>
        <div id="chartResult-fallback">
            @include('sibel.partials.score-bars', ['result' => $result])
        </div>
    </div>

    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-emerald-600">Kesimpulan</p>
        @if ($result->isTie())
            <p class="mt-1 text-lg font-bold text-indigo-900">Seri: {{ $result->dominantLabel() }}</p>
            <p class="mt-1 text-sm text-emerald-900">Kedua kategori memperoleh skor tertinggi yang sama — keduanya sama-sama bisa dimanfaatkan.</p>
        @else
            <p class="mt-1 text-lg font-bold text-emerald-900">{{ $result->dominantLabel() }} Dominan</p>
        @endif
    </div>
</div>

{{-- Interpretasi --}}
<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-800">Interpretasi Hasil</h2>
    <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $result->interpretation }}</p>
</div>

{{-- Rekomendasi --}}
<div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="text-base font-bold text-slate-800">Rekomendasi Strategi Belajar</h2>
    <p class="mt-1 text-xs text-slate-500">Coba variasi strategi berikut sesuai kebutuhan materi dan karakteristikmu.</p>
    <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $result->recommendation }}</div>
</div>

{{-- Catatan guru BK --}}
@if ($result->teacher_note)
    <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h2 class="text-base font-bold text-amber-900">Catatan Guru BK</h2>
        <p class="mt-2 text-sm leading-relaxed text-amber-900">{{ $result->teacher_note }}</p>
    </div>
@endif

{{-- Etika --}}
<p class="mt-4 rounded-xl bg-slate-100 p-4 text-xs leading-relaxed text-slate-500">{{ config('sibel.ethics_note') }}</p>

@push('scripts')
    <script>
        window.sibelCharts.push({
            target: 'chartResult',
            type: 'bar',
            data: {
                labels: @json(array_column(config('sibel.categories'), 'label')),
                datasets: [{
                    label: 'Jumlah jawaban',
                    data: @json([$result->scoreOf('visual'), $result->scoreOf('auditori'), $result->scoreOf('kinestetik')]),
                    backgroundColor: @json(array_column(config('sibel.categories'), 'hex')),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    </script>
@endpush
@endsection
