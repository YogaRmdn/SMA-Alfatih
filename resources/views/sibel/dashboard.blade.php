@extends('sibel.layouts.app')

@section('title', 'Dashboard SIBEL')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard SIBEL</h1>
        <p class="mt-1 text-sm text-slate-500">Sistem Informasi Gaya Belajar Siswa — ringkasan aktivitas pengisian kuesioner.</p>
    </div>
    <a href="{{ route('sibel.questionnaires.index') }}"
       class="inline-flex items-center gap-2 self-start rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        Kelola Kuesioner
    </a>
</div>

{{-- Kartu statistik --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-admin.stat-card label="Total Siswa" :value="$totalStudents" color="emerald"
        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    <x-admin.stat-card label="Total Kelas" :value="$totalClasses" color="sky"
        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
    <x-admin.stat-card label="Kuesioner Selesai" :value="$completedAttempts" color="violet"
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    <x-admin.stat-card label="Belum Mengisi" :value="$notCompleted" color="amber"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-5">
    {{-- Grafik distribusi --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
        <h2 class="text-base font-bold text-slate-800">Distribusi Gaya Belajar</h2>
        <p class="mt-0.5 text-xs text-slate-500">Berdasarkan hasil terakhir tiap siswa ({{ $assessed }} siswa sudah mengisi).</p>

        <div class="mt-4">
            <canvas id="chartDistribution" style="display: none" height="240"></canvas>

            {{-- Fallback grafik CSS (tetap tampil bila Chart.js tidak termuat / saat dicetak) --}}
            <div id="chartDistribution-fallback" class="space-y-3">
                @php
                    $maxValue = max(1, ...array_values($chart['values']));
                @endphp
                @foreach ($chart['labels'] as $index => $label)
                    <div class="flex items-center gap-3">
                        <div class="w-20 shrink-0 text-sm font-semibold text-slate-700">{{ $label }}</div>
                        <div class="h-4 flex-1 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full" style="width: {{ ($chart['values'][$index] / $maxValue) * 100 }}%; background: {{ $chart['colors'][$index] }}"></div>
                        </div>
                        <div class="w-16 shrink-0 text-right text-sm font-semibold text-slate-700">{{ $chart['values'][$index] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
            @foreach ($chart['labels'] as $index => $label)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                    <span class="h-2.5 w-2.5 rounded-full" style="background: {{ $chart['colors'][$index] }}"></span>
                    {{ $label }}: {{ $chart['values'][$index] }}
                </span>
            @endforeach
        </div>
        @if ($ties > 0)
            <p class="mt-2 text-xs text-slate-500">Hasil seri dihitung pada kelompok "Seri" dan tidak dipilih secara acak ke salah satu kategori.</p>
        @endif
    </div>

    {{-- Aktivitas terbaru --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-3">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-800">Aktivitas Pengisian Terbaru</h2>
                <p class="mt-0.5 text-xs text-slate-500">Sesi kuesioner paling baru.</p>
            </div>
            <a href="{{ route('sibel.results.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat semua →</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($recentAttempts as $attempt)
                <div class="flex items-center gap-3 px-5 py-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">
                        {{ strtoupper(substr($attempt->student->full_name ?? '?', 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $attempt->student->full_name ?? 'Siswa terhapus' }}</p>
                        <p class="truncate text-xs text-slate-500">
                            {{ $attempt->student->schoolClass->name ?? '-' }} • {{ $attempt->questionnaire->title ?? '-' }}
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $attempt->isCompleted() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $attempt->statusLabel() }}
                    </span>
                    <span class="hidden w-28 shrink-0 text-right text-xs text-slate-400 sm:block">
                        {{ optional($attempt->completed_at ?? $attempt->started_at)->diffForHumans() }}
                    </span>
                </div>
            @empty
                <p class="px-5 py-8 text-center text-sm text-slate-500">Belum ada aktivitas pengisian.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Akses cepat --}}
<div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    @php
        $quickLinks = [
            ['label' => 'Data Siswa', 'route' => 'sibel.students.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Kelas', 'route' => 'sibel.classes.index', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['label' => 'Hasil Asesmen', 'route' => 'sibel.results.index', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'Rekap Kelas', 'route' => 'sibel.recap.index', 'icon' => 'M9 17v-6m4 6V7m4 10v-3M3 21h18M5 21V10l7-6 7 6v11'],
        ]
    @endphp
    @foreach ($quickLinks as $link)
        <a href="{{ route($link['route']) }}"
           class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 transition group-hover:bg-emerald-700 group-hover:text-white">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}" /></svg>
            </span>
            <span class="text-sm font-semibold text-slate-700">{{ $link['label'] }}</span>
        </a>
    @endforeach
</div>

@if (config('sibel.categories'))
    @push('scripts')
        <script>
            window.sibelCharts.push({
                target: 'chartDistribution',
                type: 'doughnut',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [{
                        data: @json($chart['values']),
                        backgroundColor: @json($chart['colors']),
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } } }
                }
            });
        </script>
    @endpush
@endif
@endsection
