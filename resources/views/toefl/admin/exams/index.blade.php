@extends('admin.layouts.app')

@section('title', 'Daftar Ujian TOEFL')

@section('content')
<x-admin.page-header title="Daftar Ujian TOEFL" subtitle="Kelola ujian latihan TOEFL ITP Level 1 — draf, jadwal, dan publikasi.">
    <x-slot:button>
        <a href="{{ route('toefl.admin.exams.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Buat Ujian
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
            <tr>
                <th class="px-5 py-3">Ujian</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-center">Soal</th>
                <th class="px-5 py-3 text-center">Kode</th>
                <th class="px-5 py-3 text-center">Attempt</th>
                <th class="px-5 py-3">Jadwal</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($exams as $exam)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <a href="{{ route('toefl.admin.exams.show', $exam) }}" class="font-semibold text-slate-800 hover:text-indigo-700">{{ $exam->title }}</a>
                        <p class="text-xs text-slate-500">Kode: {{ $exam->exam_code }} · oleh {{ $exam->creator?->name ?? '—' }}</p>
                    </td>
                    <td class="px-5 py-3">
                        @php
                            $statusColor = match ($exam->status) {
                                'published' => 'bg-emerald-100 text-emerald-700',
                                'closed' => 'bg-slate-200 text-slate-600',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">{{ $exam->statusLabel() }}</span>
                    </td>
                    <td class="px-5 py-3 text-center tabular-nums">{{ $exam->sections_count }}</td>
                    <td class="px-5 py-3 text-center tabular-nums">{{ $exam->access_codes_count }}</td>
                    <td class="px-5 py-3 text-center tabular-nums">{{ $exam->attempts_count }}</td>
                    <td class="px-5 py-3 text-xs text-slate-600">
                        @if ($exam->start_at || $exam->end_at)
                            {{ $exam->start_at?->format('d M H:i') ?? '—' }} → {{ $exam->end_at?->format('d M H:i') ?? '—' }}
                        @else
                            <span class="text-slate-400">Tanpa jadwal</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('toefl.admin.questions.index', $exam) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Soal</a>
                            <a href="{{ route('toefl.admin.exams.edit', $exam) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Belum ada ujian. Buat ujian pertama Anda.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $exams->links() }}</div>
@endsection
