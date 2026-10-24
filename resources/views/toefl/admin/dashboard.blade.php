@extends('admin.layouts.app')

@section('title', 'Dashboard TOEFL')
@section('breadcrumb', 'Dashboard TOEFL')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard TOEFL ITP</h1>
        <p class="mt-1 text-sm text-slate-500">Sistem Ujian Online TOEFL ITP Level 1 — ringkasan ujian, sesi, dan hasil.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('toefl.admin.results.export') }}"
           class="inline-flex items-center gap-2 self-start rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900"
           title="Unduh rekap hasil (XLSX)">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" /></svg>
            Unduh Excel
        </a>
        <a href="{{ route('toefl.admin.exams.create') }}"
           class="inline-flex items-center gap-2 self-start rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Buat Ujian
        </a>
    </div>
</div>

{{-- Kartu statistik --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-admin.stat-card label="Total Ujian" :value="$totalExams" color="indigo"
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
    <x-admin.stat-card label="Ujian Dibuka" :value="$publishedExams" color="emerald"
        icon="M5 11l7-7 7 7M5 19l7-7 7 7" />
    <x-admin.stat-card label="Peserta" :value="$totalStudents" color="sky"
        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    <x-admin.stat-card label="Sedang Berlangsung" :value="$activeAttempts" color="rose"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-admin.stat-card label="Ujian Selesai" :value="$finishedAttempts" color="teal"
        icon="M5 13l4 4L19 7" />
    <x-admin.stat-card label="Hasil Terekam" :value="$totalResults" color="violet"
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    <x-admin.stat-card label="Rata-rata Skor Scaled" :value="$avgScaled" color="amber"
        icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-5">
    {{-- Grafik distribusi skor --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
        <h2 class="text-base font-bold text-slate-800">Distribusi Skor Scaled</h2>
        <p class="mt-0.5 text-xs text-slate-500">Sebaran hasil seluruh percobaan ({{ $totalResults }} hasil).</p>

        <div class="mt-4">
            <canvas id="chartToeflScores" style="display: none" height="240"></canvas>

            {{-- Fallback grafik CSS --}}
            <div id="chartToeflScores-fallback" class="space-y-3">
                @php $maxValue = max(1, ...array_values($chart['values'])); @endphp
                @foreach ($chart['labels'] as $index => $label)
                    <div class="flex items-center gap-3">
                        <div class="w-20 shrink-0 text-sm font-semibold text-slate-700">{{ $label }}</div>
                        <div class="h-4 flex-1 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full" style="width: {{ ($chart['values'][$index] / $maxValue) * 100 }}%; background: {{ $chart['colors'][$index] }}"></div>
                        </div>
                        <div class="w-8 shrink-0 text-right text-sm font-semibold text-slate-700">{{ $chart['values'][$index] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="mt-4 border-t border-slate-100 pt-3 text-xs text-slate-500">
            Skor scaled memakai aproksimasi linear (310–680) — bukan tabel konversi resmi ETS.
        </p>
    </div>

    {{-- Sesi berjalan --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-3">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-800">Sesi Sedang Berjalan</h2>
                <p class="mt-0.5 text-xs text-slate-500">Pantauan langsung attempt aktif.</p>
            </div>
            <a href="{{ route('toefl.admin.results.index', ['status' => 'in_progress']) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800">Lihat semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($liveAttempts as $attempt)
                <div class="flex items-center gap-3 px-5 py-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-rose-100 text-xs font-bold text-rose-700">
                        {{ strtoupper(substr($attempt->student?->full_name ?? '?', 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $attempt->student?->full_name ?? '—' }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $attempt->exam?->title }} · {{ $attempt->current_section_type ?: 'belum mulai' }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        @if ($attempt->deadline_at)
                            <p class="text-xs font-semibold text-slate-700 tabular-nums">{{ $attempt->deadline_at->diffForHumans(null, true) }}</p>
                            <p class="text-[11px] text-slate-400">menuju deadline</p>
                        @endif
                        <a href="{{ route('toefl.admin.results.show', $attempt) }}" class="text-xs font-semibold text-indigo-700 hover:text-indigo-800">Detail</a>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-slate-500">Tidak ada sesi yang sedang berlangsung.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Hasil terbaru --}}
<div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div>
            <h2 class="text-base font-bold text-slate-800">Hasil Terbaru</h2>
            <p class="mt-0.5 text-xs text-slate-500">Percobaan yang baru dinilai.</p>
        </div>
        <a href="{{ route('toefl.admin.results.index') }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-800">Semua hasil →</a>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse ($recentResults as $result)
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">
                    {{ strtoupper(substr($result->attempt?->student?->full_name ?? '?', 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $result->attempt?->student?->full_name ?? '—' }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $result->attempt?->exam?->title }} · {{ $result->completed_at?->format('d M H:i') }}</p>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-lg font-extrabold tabular-nums text-slate-800">{{ $result->scaled_score }}</p>
                    <a href="{{ route('toefl.admin.results.show', $result->attempt_id) }}" class="text-xs font-semibold text-indigo-700 hover:text-indigo-800">Detail</a>
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-slate-500">Belum ada hasil.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.toeflCharts = window.toeflCharts || [];
    window.toeflCharts.push({
        target: 'chartToeflScores',
        type: 'bar',
        data: {
            labels: @json($chart['labels']),
            datasets: [{ label: 'Jumlah Siswa', data: @json($chart['values']), backgroundColor: @json($chart['colors']), borderRadius: 6 }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    window.addEventListener('load', function () {
        if (typeof Chart === 'undefined') return;
        window.toeflCharts.forEach(function (spec) {
            var canvas = document.getElementById(spec.target);
            if (!canvas || canvas.dataset.rendered) return;
            try {
                new Chart(canvas.getContext('2d'), { type: spec.type, data: spec.data, options: spec.options });
                canvas.dataset.rendered = '1';
                canvas.style.display = 'block';
                var fallback = document.getElementById(spec.target + '-fallback');
                if (fallback) fallback.style.display = 'none';
            } catch (e) { console.warn('TOEFL chart gagal dirender:', e); }
        });
    });
</script>
@endpush
