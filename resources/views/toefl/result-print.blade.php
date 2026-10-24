@extends('toefl.layouts.app')

@section('title', 'Cetak Hasil TOEFL')

@section('content')
<div class="mx-auto w-full max-w-3xl px-4 py-8">
    <div class="no-print mb-4 flex items-center justify-between">
        <button type="button" onclick="window.print()"
            class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
            Cetak / Simpan PDF
        </button>
        <a href="{{ route('toefl.result') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            ← Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-300 bg-white p-8 shadow-sm">
        {{-- Kop --}}
        <div class="border-b-2 border-slate-800 pb-4 text-center">
            <h1 class="text-lg font-extrabold uppercase tracking-wide text-slate-900">{{ $settings['site_name'] ?? config('app.name') }}</h1>
            <p class="text-sm text-slate-600">Laporan Hasil Ujian Latihan TOEFL ITP Level 1</p>
        </div>

        {{-- Identitas --}}
        <div class="mt-5 grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><span class="text-slate-500">Nama Peserta</span><p class="font-semibold text-slate-800">{{ $attempt->student->full_name }}</p></div>
            <div><span class="text-slate-500">Nomor / Kelas</span><p class="font-semibold text-slate-800">{{ $attempt->student->student_number ?: '—' }} {{ $attempt->student->class_name ? '· '.$attempt->student->class_name : '' }}</p></div>
            <div><span class="text-slate-500">Ujian</span><p class="font-semibold text-slate-800">{{ $exam->title }}</p></div>
            <div><span class="text-slate-500">Tanggal</span><p class="font-semibold text-slate-800">{{ ($attempt->submitted_at ?? $attempt->created_at)->format('d-m-Y H:i') }}</p></div>
        </div>

        @if ($result)
            {{-- Skor --}}
            <div class="mt-6 rounded-xl border border-slate-300 p-5 text-center">
                <p class="text-xs uppercase tracking-widest text-slate-500">Skor Scaled (Latihan)</p>
                <p class="text-4xl font-extrabold tabular-nums text-slate-900">{{ $result->scaled_score }}</p>
            </div>

            <table class="mt-5 w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-100 text-left text-slate-700">
                        <th class="border border-slate-300 px-3 py-2">Bagian</th>
                        <th class="border border-slate-300 px-3 py-2 text-center">Benar</th>
                        <th class="border border-slate-300 px-3 py-2 text-center">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result->sectionsBreakdown() as $type => $data)
                        @php
                            $totalSoal = $exam->sections->firstWhere('section_type', $type)?->questions()->count() ?? 0;
                            $pct = $totalSoal > 0 ? round(($data['raw'] / $totalSoal) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="border border-slate-300 px-3 py-2">{{ $data['label'] }}</td>
                            <td class="border border-slate-300 px-3 py-2 text-center">{{ $data['raw'] }}/{{ $totalSoal }}</td>
                            <td class="border border-slate-300 px-3 py-2 text-center">{{ $pct }}%</td>
                        </tr>
                    @endforeach
                    <tr class="bg-slate-50 font-semibold">
                        <td class="border border-slate-300 px-3 py-2">Total</td>
                        <td class="border border-slate-300 px-3 py-2 text-center">{{ $result->total_correct }}/{{ $result->total_questions }}</td>
                        <td class="border border-slate-300 px-3 py-2 text-center">{{ $result->percentage() }}%</td>
                    </tr>
                </tbody>
            </table>
        @endif

            <p class="mt-6 text-[11px] leading-relaxed text-slate-500">
                Catatan: {{ config('toefl.ethics_note') }}
            </p>

        <div class="mt-8 flex justify-between text-sm text-slate-600">
            <div>
                <p>Peserta,</p>
                <p class="mt-10 border-t border-slate-400 pt-1 w-48 text-center font-semibold">{{ $attempt->student->full_name }}</p>
            </div>
            <div class="text-right">
                <p>Pekanbaru, {{ now()->format('d F Y') }}</p>
                <p>Pengawas Ujian,</p>
                <p class="mt-10 border-t border-slate-400 pt-1 w-48 text-center font-semibold">&nbsp;</p>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        // Cetak otomatis hanya bila dibuka lewat tombol print browser kecil —
        // biarkan pengguna memilih (tidak memaksa).
    });
</script>
@endsection
