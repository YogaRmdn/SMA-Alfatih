@extends('sibel.layouts.print')

@section('title', 'Rekapitulasi Gaya Belajar Kelas')

@section('content')
<div class="doc-title">
    <h2>Rekapitulasi Hasil Gaya Belajar Siswa</h2>
    <p>
        {{ request('class_id') ? 'Kelas: ' . ($classes->firstWhere('id', request('class_id'))->label() ?? '-') : 'Seluruh Kelas' }}
        @if (request('period'))
            • Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', request('period'))->translatedFormat('F Y') }}
        @endif
        • {{ $total }} siswa
    </p>
</div>

<h3 style="margin: 0 0 6px; font-size: 13px;">A. Distribusi Kategori</h3>
<div class="bars">
    @foreach ($chart['labels'] as $index => $label)
        <div class="bar-row">
            <div class="bar-label">{{ $label }}</div>
            <div class="bar-track">
                <div class="bar-fill" style="width: {{ $total > 0 ? min(100, ($chart['values'][$index] / $total) * 100) : 0 }}%; background: {{ $chart['colors'][$index] }};"></div>
            </div>
            <div class="bar-value">
                {{ $chart['values'][$index] }} siswa •
                {{ $total > 0 ? rtrim(rtrim(number_format(($chart['values'][$index] / $total) * 100, 1, ',', '.'), '0'), ',') : '0' }}%
            </div>
        </div>
    @endforeach
</div>

<h3 style="margin: 18px 0 6px; font-size: 13px;">B. Daftar Siswa</h3>
<table class="grid">
    <thead>
        <tr>
            <th class="num" style="width: 40px">No.</th>
            <th>Nama Siswa</th>
            <th style="width: 90px">Kelas</th>
            <th class="num" style="width: 80px">V/A/K</th>
            <th style="width: 150px">Hasil</th>
            <th style="width: 90px">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attempts as $index => $attempt)
            <tr>
                <td class="num">{{ $index + 1 }}</td>
                <td>{{ $attempt->student->full_name ?? '—' }}</td>
                <td>{{ $attempt->student->schoolClass->name ?? '—' }}</td>
                <td class="num">{{ $attempt->result->score_a }}/{{ $attempt->result->score_b }}/{{ $attempt->result->score_c }}</td>
                <td>{{ $attempt->result->isTie() ? 'Seri ' . $attempt->result->dominantLabel() : $attempt->result->dominantLabel() }}</td>
                <td>{{ optional($attempt->completed_at)->format('d/m/Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align: center;">Tidak ada hasil pada filter ini.</td></tr>
        @endforelse
    </tbody>
</table>

<p class="ethics"><strong>Catatan:</strong> {{ config('sibel.ethics_note') }}</p>

<div class="signature">
    <div class="block">
        <p>Guru Bimbingan dan Konseling,</p>
        <div class="space"></div>
        <p><strong>........................................</strong></p>
        <p style="color: var(--muted);">NIP. ....................................</p>
    </div>
</div>
@endsection
