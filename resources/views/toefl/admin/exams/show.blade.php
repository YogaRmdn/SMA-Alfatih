@extends('admin.layouts.app')

@section('title', 'Detail Ujian TOEFL')

@section('content')
<x-admin.page-header :title="$exam->title" :subtitle="'Status: '.$exam->statusLabel().' · Total '.$stats['questions'].' soal · '.$stats['codes'].' kode akses'">
    <x-slot:button>
        <a href="{{ route('toefl.admin.questions.index', $exam) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Kelola Soal
        </a>
        <form method="POST" action="{{ route('toefl.admin.exams.toggle-status', $exam) }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg {{ $exam->status === 'published' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-700 hover:bg-emerald-800' }} px-4 py-2 text-sm font-semibold text-white transition">
                {{ $exam->status === 'draft' ? 'Publikasikan' : ($exam->status === 'published' ? 'Tutup Ujian' : 'Buka Lagi (Draf)') }}
            </button>
        </form>
        <a href="{{ route('toefl.admin.exams.edit', $exam) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Edit</a>
    </x-slot:button>
</x-admin.page-header>

{{-- Ringkasan --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-admin.stat-card label="Soal" :value="$stats['questions']" color="indigo" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    <x-admin.stat-card label="Kode Akses" :value="$stats['codes']" color="sky" icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
    <x-admin.stat-card label="Attempt" :value="$stats['attempts']" color="violet" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
    <x-admin.stat-card label="Selesai" :value="$stats['finished']" color="emerald" icon="M5 13l4 4L19 7" />
</div>

{{-- Deskripsi + jadwal --}}
<div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="grid gap-4 text-sm md:grid-cols-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jadwal</p>
            <p class="mt-1 text-slate-700">{{ $exam->start_at?->format('d M Y H:i') ?? 'Tanpa batas mulai' }} → {{ $exam->end_at?->format('d M Y H:i') ?? 'Tanpa batas selesai' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kode Ujian</p>
            <p class="mt-1 font-mono font-semibold text-slate-700">{{ $exam->exam_code }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pengaturan</p>
            <p class="mt-1 text-slate-700">
                Hasil ke siswa: <strong>{{ $exam->show_result_to_student ? 'Ya' : 'Tidak' }}</strong> ·
                Tinjauan: <strong>{{ $exam->allow_review ? 'Ya' : 'Tidak' }}</strong>
            </p>
        </div>
    </div>
    @if ($exam->description)
        <p class="mt-4 border-t border-slate-100 pt-3 text-sm text-slate-600">{{ $exam->description }}</p>
    @endif
</div>

{{-- Bagian + durasi --}}
<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-base font-bold text-slate-800">Bagian Ujian & Durasi</h2>
        <p class="mt-0.5 text-xs text-slate-500">Durasi mempengaruhi deadline server untuk attempt baru.</p>
    </div>

    <div class="divide-y divide-slate-100">
        @foreach ($exam->sections as $section)
            <form method="POST" action="{{ route('toefl.admin.exams.sections.update', [$exam, $section]) }}"
                  class="flex flex-wrap items-end gap-3 px-5 py-4">
                @csrf
                @method('PUT')
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800">Bagian {{ $section->section_order }} · {{ $section->title }}</p>
                    <p class="text-xs text-slate-500">{{ $section->questions()->count() }} soal · {{ $section->passages()->count() }} bacaan · {{ $section->audioAssets()->count() }} audio</p>
                </div>
                <div class="w-64">
                    <input type="hidden" name="title" value="{{ $section->title }}">
                    <label class="mb-1 block text-xs font-medium text-slate-600">Durasi (detik)</label>
                    <div class="flex gap-2">
                        <input type="number" name="duration_seconds" min="60" max="14400" value="{{ $section->duration_seconds }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="shrink-0 rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-900">Simpan</button>
                    </div>
                </div>
            </form>
        @endforeach
    </div>
</div>

{{-- Kode akses ujian --}}
<div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-base font-bold text-slate-800">Kode Akses Ujian</h2>
        <p class="mt-0.5 text-xs text-slate-500">
            Peserta menulis nama + kelas lalu memasukkan kode ini di portal <span class="font-mono">/toefl</span> — tanpa registrasi & login. Satu kode dapat dipakai seluruh peserta ujian ini.
        </p>
    </div>

    @if (session('issued_code'))
        <div class="border-b border-emerald-100 bg-emerald-50 px-5 py-4 text-center">
            <p class="text-xs font-semibold uppercase tracking-widest text-emerald-700">Kode Akses Berhasil Dibuat</p>
            <p class="mt-1 text-sm text-emerald-800">untuk <strong>{{ session('issued_exam') }}</strong></p>
            <p class="mt-3 inline-block rounded-xl bg-white px-6 py-3 font-mono text-2xl font-extrabold tracking-[0.3em] text-slate-900 ring-2 ring-emerald-400">
                {{ session('issued_code') }}
            </p>
            <p class="mt-2 text-xs font-medium text-emerald-700">⚠ Salin & bagikan kode ini sekarang — kode asli TIDAK ditampilkan lagi setelah halaman ditutup.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('toefl.admin.exams.codes.issue', $exam) }}" class="flex flex-wrap items-end gap-3 border-b border-slate-100 px-5 py-4">
        @csrf
        <div class="w-64">
            <label class="mb-1 block text-xs font-medium text-slate-600">Berlaku Sampai (opsional)</label>
            <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            Buat Kode Akses
        </button>
    </form>

    <div class="divide-y divide-slate-100">
        @forelse ($codes as $code)
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-3">
                <div class="min-w-0">
                    <p class="font-mono text-sm font-semibold tracking-[0.25em] text-slate-800">{{ $code->code_prefix }}-••••</p>
                    <p class="text-xs text-slate-500">
                        Prefix: <span class="font-mono">{{ $code->code_prefix }}…</span>
                        @if ($code->expires_at) · s.d. {{ $code->expires_at->format('d M Y H:i') }}@endif
                        @if ($code->used_at) · dipakai {{ $code->used_at->format('d M H:i') }}@endif
                        · {{ $code->created_at->format('d M Y') }}
                    </p>
                </div>
                @if ($code->isRevoked())
                    <span class="shrink-0 rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-600">Dicabut</span>
                @elseif ($code->isExpired())
                    <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">Kedaluwarsa</span>
                @else
                    <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                    <form method="POST" action="{{ route('toefl.admin.exams.codes.revoke', [$exam, $code]) }}"
                          onsubmit="return confirm('Cabut kode ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shrink-0 rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Cabut</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-slate-500">Belum ada kode akses. Buat satu untuk dibagikan ke peserta.</p>
        @endforelse
    </div>
</div>

{{-- Aksi berbahaya --}}
<div class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-5">
    <p class="text-sm font-semibold text-red-800">Zona Berbahaya</p>
    <p class="mt-0.5 text-xs text-red-600">Menghapus ujian akan menghapus seluruh soal, kode akses, attempt, dan hasil terkait.</p>
    <form method="POST" action="{{ route('toefl.admin.exams.destroy', $exam) }}" class="mt-3 inline-block"
          onsubmit="return confirm('Hapus ujian beserta SEMUA data terkait? Tindakan ini tidak bisa dibatalkan.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">
            Hapus Ujian
        </button>
    </form>
</div>
@endsection
