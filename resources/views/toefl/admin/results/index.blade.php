@extends('admin.layouts.app')

@section('title', 'Hasil & Sesi TOEFL')

@section('content')
<x-admin.page-header title="Hasil & Sesi Ujian" subtitle="Pantau sesi berjalan, tinjau hasil, dan ekspor laporan.">
    <x-slot:button>
        <a href="{{ route('toefl.admin.results.export', array_filter(['exam_id' => $examId ?: null, 'status' => $status ?: null])) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" /></svg>
            Ekspor Excel
        </a>
    </x-slot:button>
</x-admin.page-header>

{{-- Filter --}}
<form method="GET" action="{{ route('toefl.admin.results.index') }}" class="mb-4 flex flex-wrap gap-2">
    <select name="exam_id" class="w-auto rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Semua Ujian</option>
        @foreach ($exams as $exam)
            <option value="{{ $exam->id }}" {{ $examId === $exam->id ? 'selected' : '' }}>{{ $exam->title }}</option>
        @endforeach
    </select>
    <select name="status" class="w-auto rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Semua Status</option>
        @foreach (config('toefl.attempt_statuses') as $key => $label)
            <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Terapkan</button>
    @if ($examId || $status)
        <a href="{{ route('toefl.admin.results.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Reset</a>
    @endif
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
            <tr>
                <th class="px-5 py-3">Siswa</th>
                <th class="px-5 py-3">Ujian</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-center">Benar</th>
                <th class="px-5 py-3 text-center">Scaled</th>
                <th class="px-5 py-3">Selesai</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($attempts as $attempt)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-slate-800">{{ $attempt->student?->full_name ?? '—' }}</p>
                        <p class="text-xs text-slate-500">{{ $attempt->student?->class_name ?: $attempt->student?->student_number ?: '' }}</p>
                    </td>
                    <td class="px-5 py-3 text-slate-600">{{ $attempt->exam?->title }}</td>
                    <td class="px-5 py-3">
                        @php
                            $statusColor = match ($attempt->status) {
                                'submitted' => 'bg-emerald-100 text-emerald-700',
                                'expired' => 'bg-red-100 text-red-700',
                                default => 'bg-sky-100 text-sky-700',
                            };
                        @endphp
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">{{ $attempt->statusLabel() }}</span>
                    </td>
                    <td class="px-5 py-3 text-center tabular-nums text-slate-700">
                        @if ($attempt->result)
                            {{ $attempt->result->total_correct }}/{{ $attempt->result->total_questions }}
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if ($attempt->result)
                            <span class="text-lg font-extrabold tabular-nums text-slate-800">{{ $attempt->result->scaled_score }}</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-slate-500">{{ $attempt->submitted_at?->format('d M H:i') ?? ($attempt->started_at?->format('d M H:i') ?? '—') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('toefl.admin.results.show', $attempt) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Detail</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-slate-500">Tidak ada data sesi/hasil.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $attempts->links() }}</div>
@endsection
