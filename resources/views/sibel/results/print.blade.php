@extends('sibel.layouts.print')

@section('title', 'Laporan Hasil Gaya Belajar — ' . ($attempt->student->full_name ?? ''))

@section('content')
<div class="doc-title">
    <h2>Laporan Hasil Asesmen Gaya Belajar Siswa</h2>
    <p>{{ $attempt->questionnaire->title }} — Instrumen v{{ $result->questionnaire_version ?? $attempt->questionnaire->version }}</p>
</div>

<table class="meta">
    <tr><td>Nama Siswa</td><td>:</td><td><strong>{{ $attempt->student->full_name ?? '—' }}</strong></td></tr>
    <tr><td>Nomor Induk</td><td>:</td><td>{{ $attempt->student->student_number ?? '—' }}</td></tr>
    <tr><td>Kelas</td><td>:</td><td>{{ $attempt->student->schoolClass?->name ?? '—' }}@if ($attempt->student->schoolClass?->academic_year) ({{ $attempt->student->schoolClass->academic_year }})@endif</td></tr>
    <tr><td>Tanggal Pengisian</td><td>:</td><td>{{ optional($attempt->completed_at)->format('d F Y') ?? '—' }}</td></tr>
    <tr><td>Status Hasil</td><td>:</td><td>{{ $result->is_published ? 'Dipublikasikan' : 'Belum dipublikasikan' }}</td></tr>
</table>

<h3 style="margin: 18px 0 6px; font-size: 13px;">A. Rekap Skor</h3>
@php
    // Metode penilaian menentukan satuan skor (poin Likert vs jumlah jawaban).
    $isLikert = $attempt->questionnaire->isLikert();
    $totalSkor = $result->score_a + $result->score_b + $result->score_c;
    $maksSkor = $isLikert
        ? array_sum(array_map(fn ($key) => $attempt->questionnaire->maxLikertScore($key), array_keys(config('sibel.categories'))))
        : $totalSkor;
@endphp

<table class="grid">
    <thead>
        <tr>
            <th style="width: 40%">Kategori</th>
            <th class="num">{{ $isLikert ? 'Skor (poin)' : 'Skor (jawaban)' }}</th>
            <th class="num">Persentase</th>
        </tr>
    </thead>
    <tbody>
        @foreach (config('sibel.categories') as $key => $category)
            <tr>
                <td>
                    @if ($isLikert)
                        {{ $category['label'] }} (maks {{ $attempt->questionnaire->maxLikertScore($key) }})
                    @else
                        {{ $category['label'] }} (Opsi {{ $category['code'] }})
                    @endif
                    @if (in_array($key, $result->dominant_categories ?? [], true))
                        <span class="badge" style="margin-left: 6px;">{{ $result->isTie() ? 'SERI' : 'DOMINAN' }}</span>
                    @endif
                </td>
                <td class="num">{{ $result->scoreOf($key) }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($result->percentageOf($key), 1, ',', '.'), '0'), ',') }}%</td>
            </tr>
        @endforeach
        <tr>
            <td><strong>{{ $isLikert ? 'Total poin (maks '.$maksSkor.')' : 'Total jawaban valid' }}</strong></td>
            <td class="num"><strong>{{ $totalSkor }}</strong></td>
            <td class="num"><strong>{{ $isLikert ? '—' : '100%' }}</strong></td>
        </tr>
    </tbody>
</table>

<div class="bars">
    @foreach (config('sibel.categories') as $key => $category)
        <div class="bar-row">
            <div class="bar-label">{{ $category['label'] }}</div>
            <div class="bar-track">
                <div class="bar-fill" style="width: {{ min(100, $result->percentageOf($key)) }}%; background: {{ $category['hex'] }};"></div>
            </div>
            <div class="bar-value">
                @if ($isLikert)
                    {{ $result->scoreOf($key) }}/{{ $attempt->questionnaire->maxLikertScore($key) }} poin
                @else
                    {{ $result->scoreOf($key) }} jawaban
                @endif
                • {{ rtrim(rtrim(number_format($result->percentageOf($key), 1, ',', '.'), '0'), ',') }}%
            </div>
        </div>
    @endforeach
</div>

<h3 style="margin: 18px 0 6px; font-size: 13px;">B. Kesimpulan</h3>
<p>
    Berdasarkan hasil kuesioner, siswa menunjukkan kecenderungan
    @if ($result->isTie())
        <span class="badge">SERI: {{ strtoupper($result->dominantLabel()) }}</span>
        — dua kategori memperoleh skor tertinggi yang sama sehingga keduanya sama-sama mendominasi.
    @else
        <span class="badge">{{ strtoupper($result->dominantLabel()) }} DOMINAN</span>
    @endif
</p>

<div class="box">
    <h3>C. Interpretasi Hasil</h3>
    <p>{{ $result->interpretation }}</p>
</div>

<div class="box">
    <h3>D. Rekomendasi Strategi Belajar</h3>
    <p>{{ $result->recommendation }}</p>
</div>

<div class="box">
    <h3>E. Catatan Guru BK</h3>
    <p>{{ $result->teacher_note ?: '—' }}</p>
</div>

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
