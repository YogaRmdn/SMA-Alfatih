@extends('sibel.layouts.app')

@section('title', 'Rekap Kelas')
@section('breadcrumb', 'Rekap Kelas')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Rekapitulasi Kelas</h1>
        <p class="mt-1 text-sm text-slate-500">Distribusi gaya belajar berdasarkan hasil terakhir tiap siswa.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('sibel.results.export', request()->query()) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900"
           title="Unduh rekap, distribusi kelas, & detail jawaban (XLSX)">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" /></svg>
            Unduh Excel
        </a>
        <a href="{{ route('sibel.recap.print', request()->query()) }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
            Cetak Rekap
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('sibel.recap.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <x-admin.field label="Cari Siswa" name="q">
                <x-admin.input name="q" :value="request('q')" placeholder="Nama siswa..." />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-48">
            <x-admin.field label="Kelas" name="class_id">
                <x-admin.select name="class_id" :value="request('class_id')" placeholder="Semua kelas"
                    :options="$classes->mapWithKeys(fn ($class) => [$class->id => $class->label()])->all()" />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-48">
            <x-admin.field label="Periode Pengisian" name="period" hint="Bulan penyelesaian.">
                <x-admin.input name="period" type="month" :value="request('period')" />
            </x-admin.field>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('sibel.recap.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>
</div>

{{-- Ringkasan distribusi --}}
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="text-base font-bold text-slate-800">Distribusi Kategori</h2>
        <p class="mt-0.5 text-xs text-slate-500">{{ $total }} siswa memiliki hasil pada filter ini.</p>

        <div class="mt-4">
            <canvas id="chartRecap" style="display: none" height="200"></canvas>
            <div id="chartRecap-fallback" class="space-y-3">
                @php $maxValue = max(1, ...array_values($chart['values'])); @endphp
                @foreach ($chart['labels'] as $index => $label)
                    <div class="flex items-center gap-3">
                        <div class="w-20 shrink-0 text-sm font-semibold text-slate-700">{{ $label }}</div>
                        <div class="h-4 flex-1 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full rounded-full" style="width: {{ ($chart['values'][$index] / $maxValue) * 100 }}%; background: {{ $chart['colors'][$index] }}"></div>
                        </div>
                        <div class="w-24 shrink-0 text-right text-sm text-slate-600">
                            <span class="font-bold text-slate-800">{{ $chart['values'][$index] }}</span>
                            ({{ $total > 0 ? rtrim(rtrim(number_format(($chart['values'][$index] / $total) * 100, 1, ',', '.'), '0'), ',') : '0' }}%)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="space-y-3">
        @foreach (config('sibel.categories') as $key => $category)
            <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <span class="h-10 w-2 shrink-0 rounded-full" style="background: {{ $category['hex'] }}"></span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-800">{{ $category['label'] }}</p>
                    <p class="text-xs text-slate-500">{{ $distribution[$key] ?? 0 }} siswa • {{ $total > 0 ? rtrim(rtrim(number_format($percentages[$key] ?? 0, 1, ',', '.'), '0'), ',') : '0' }}%</p>
                </div>
            </div>
        @endforeach
        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <span class="h-10 w-2 shrink-0 rounded-full bg-slate-400"></span>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-800">Hasil Seri</p>
                <p class="text-xs text-slate-500">{{ $ties }} siswa (skor tertinggi sama pada dua/tiga kategori)</p>
            </div>
        </div>
    </div>
</div>

{{-- Tabel rekap --}}
<div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-base font-bold text-slate-800">Daftar Siswa</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">No.</th>
                    <th class="px-5 py-3">Nama Siswa</th>
                    <th class="px-5 py-3">Kelas</th>
                    <th class="px-5 py-3 text-center">V / A / K</th>
                    <th class="px-5 py-3">Hasil</th>
                    <th class="px-5 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($attempts as $index => $attempt)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="px-5 py-3 text-slate-400">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-slate-800">{{ $attempt->student->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $attempt->student->schoolClass->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-center font-mono text-xs font-semibold text-slate-700">
                            {{ $attempt->result->score_a }} / {{ $attempt->result->score_b }} / {{ $attempt->result->score_c }}
                        </td>
                        <td class="px-5 py-3">
                            @if ($attempt->result->isTie())
                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">Seri: {{ $attempt->result->dominantLabel() }}</span>
                            @else
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                      style="background: {{ config('sibel.categories.' . ($attempt->result->dominant_categories[0] ?? 'visual') . '.hex', '#64748b') }}22; color: {{ config('sibel.categories.' . ($attempt->result->dominant_categories[0] ?? 'visual') . '.hex', '#64748b') }}">
                                    {{ $attempt->result->dominantLabel() }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ optional($attempt->completed_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                            Tidak ada hasil pada filter ini. Pastikan siswa sudah mengisi kuesioner dan sesi sudah dibuka.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
    <script>
        window.sibelCharts.push({
            target: 'chartRecap',
            type: 'bar',
            data: {
                labels: @json($chart['labels']),
                datasets: [{
                    label: 'Jumlah siswa',
                    data: @json($chart['values']),
                    backgroundColor: @json($chart['colors']),
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
