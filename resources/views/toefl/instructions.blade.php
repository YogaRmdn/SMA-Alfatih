@extends('toefl.layouts.app')

@section('title', 'Instruksi Ujian')

@section('content')
<div class="mx-auto w-full max-w-2xl px-4 py-10">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Sebelum Memulai</p>
            <h1 class="mt-1 text-xl font-bold text-slate-800">Instruksi Ujian</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $exam->title }}</p>
        </div>

        <div class="space-y-5 px-6 py-5">
            {{-- Identitas --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Nama Peserta</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $attempt->student->full_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total Waktu</p>
                        <p class="text-sm font-semibold text-slate-800">
                            {{ intdiv($exam->duration_seconds ?: $exam->sections->sum('duration_seconds'), 60) }} menit
                        </p>
                    </div>
                </div>
            </div>

            {{-- Daftar instruksi --}}
            <ol class="list-inside list-decimal space-y-2.5 text-sm text-slate-700">
                @foreach ($instructions as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ol>

            {{-- Struktur bagian --}}
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="mb-3 text-sm font-semibold text-slate-800">Susunan Bagian Ujian</p>
                <ol class="space-y-2">
                    @foreach ($exam->sections as $section)
                        <li class="flex items-center justify-between gap-3 text-sm">
                            <span class="flex items-center gap-2.5">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-bold text-indigo-700">{{ $loop->iteration }}</span>
                                <span class="text-slate-700">{{ $section->title }}</span>
                            </span>
                            <span class="shrink-0 text-xs text-slate-500">
                                {{ $section->questions()->count() }} soal · {{ $section->durationLabel() }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Konfirmasi kesiapan --}}
            <form method="POST" action="{{ route('toefl.begin') }}">
                @csrf
                <label class="flex items-start gap-3 rounded-xl border border-indigo-100 bg-indigo-50 p-4 text-sm text-slate-700">
                    <input type="checkbox" required name="ready" value="1"
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Saya sudah membaca instruksi dan siap memulai. Waktu akan mulai dihitung setelah saya menekan tombol ini.</span>
                </label>

                <button type="submit"
                    class="mt-4 w-full rounded-lg bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800"
                    onclick="return confirm('Mulai sekarang? Waktu ujian akan langsung berjalan.');">
                    Mulai Ujian
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
