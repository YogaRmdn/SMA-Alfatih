@extends('admin.layouts.app')

@section('title', 'Detail Sesi TOEFL')

@section('content')
@php $result = $attempt->result; @endphp

<x-admin.page-header :title="'Detail Sesi: '.($attempt->student?->full_name ?? '—')" :subtitle="$attempt->exam?->title.' · '.$attempt->statusLabel()">
    <x-slot:button>
        <a href="{{ route('toefl.admin.results.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Info sesi --}}
    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800">Informasi Sesi</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Status</dt><dd class="font-semibold text-slate-800">{{ $attempt->statusLabel() }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Mulai</dt><dd class="font-semibold text-slate-800">{{ $attempt->started_at?->format('d M Y H:i:s') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Deadline (server)</dt><dd class="font-semibold text-slate-800">{{ $attempt->deadline_at?->format('d M Y H:i:s') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Dikumpulkan</dt><dd class="font-semibold text-slate-800">{{ $attempt->submitted_at?->format('d M Y H:i:s') ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Versi Sesi</dt><dd class="font-semibold text-slate-800">{{ $attempt->session_version }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-500">Bagian Terakhir</dt><dd class="font-semibold text-slate-800">{{ $attempt->current_section_type ?? '—' }}</dd></div>
            </dl>
        </div>

        {{-- Pemulihan sesi --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800">Pemulihan Sesi</h3>
            <p class="mt-1 text-xs text-slate-500">Untuk kasus terputus/dierror oleh pengawas.</p>
            <div class="mt-3 flex flex-col gap-2">
                @if ($attempt->isInProgress())
                    <form method="POST" action="{{ route('toefl.admin.results.force-finish', $attempt) }}"
                          onsubmit="return confirm('Selesaikan paksa & hitung skor sekarang?');">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                            Selesaikan Paksa + Hitung Skor
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('toefl.admin.results.reopen', $attempt) }}"
                          onsubmit="return confirm('Buka kembali sesi (tambahan 10 menit) dan cabut hasil lama?');">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-800">
                            Buka Kembali Sesi (+10 menit)
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Hasil --}}
    <div class="space-y-4 lg:col-span-2">
        @if ($result)
            <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-blue-700 p-5 text-white shadow-lg shadow-indigo-900/20">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200">Skor Scaled (Latihan)</p>
                        <p class="text-4xl font-extrabold tabular-nums">{{ $result->scaled_score }}</p>
                    </div>
                    <div class="text-right text-sm">
                        <p class="text-indigo-100">Total Benar</p>
                        <p class="text-xl font-bold tabular-nums">{{ $result->total_correct }}/{{ $result->total_questions }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                @foreach ($result->sectionsBreakdown() as $type => $data)
                    @php
                        $totalSoal = $attempt->exam->sections->firstWhere('section_type', $type)?->questions()->count() ?? 0;
                        $pct = $totalSoal > 0 ? round(($data['raw'] / $totalSoal) * 100) : 0;
                    @endphp
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $data['label'] }}</p>
                        <p class="mt-1 text-2xl font-extrabold tabular-nums text-slate-800">{{ $data['raw'] }}<span class="text-sm font-medium text-slate-400">/{{ $totalSoal }}</span></p>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-indigo-600" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
                Belum ada hasil — sesi ini belum dinilai. Bila sudah lewat deadline, gunakan "Selesaikan Paksa".
            </div>
        @endif

        {{-- Peristiwa browser --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-3">
                <h3 class="text-sm font-bold text-slate-800">Log Peristiwa Browser</h3>
                <p class="text-xs text-slate-500">Catatan pendukung — bukan bukti kecurangan mutlak.</p>
            </div>
            <div class="max-h-56 divide-y divide-slate-100 overflow-y-auto">
                @forelse ($attempt->events as $event)
                    <div class="flex items-center justify-between px-5 py-2 text-xs">
                        <span class="text-slate-700">{{ $event->typeLabel() }}</span>
                        <span class="font-mono text-slate-400">{{ $event->event_timestamp->format('d M H:i:s') }}</span>
                    </div>
                @empty
                    <p class="px-5 py-5 text-sm text-slate-500">Tidak ada peristiwa tercatat.</p>
                @endforelse
            </div>
        </div>

        {{-- Pemutaran audio --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-3">
                <h3 class="text-sm font-bold text-slate-800">Riwayat Pemutaran Audio</h3>
            </div>
            <div class="max-h-48 divide-y divide-slate-100 overflow-y-auto">
                @forelse ($attempt->audioPlayEvents as $play)
                    <div class="flex items-center justify-between px-5 py-2 text-xs">
                        <span class="truncate text-slate-700">{{ $play->audioAsset?->label ?: 'Audio #'.$play->audio_asset_id }}
                            <span class="text-slate-400">· putaran ke-{{ $play->play_number }}</span>
                        </span>
                        <span class="font-mono text-slate-400">{{ $play->started_at?->format('H:i:s') }}</span>
                    </div>
                @empty
                    <p class="px-5 py-5 text-sm text-slate-500">Belum ada pemutaran audio.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
