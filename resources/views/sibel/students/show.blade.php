@extends('sibel.layouts.app')

@section('title', 'Detail Siswa')
@section('breadcrumb', 'Detail Siswa')

@section('content')
<x-admin.page-header :title="$student->full_name"
    :subtitle="($student->schoolClass->name ?? '-') . ' • ' . ($student->student_number ?: 'Tanpa nomor induk')">
    <x-slot:button>
        <a href="{{ route('sibel.students.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid gap-6 lg:grid-cols-3">
    {{-- Kartu identitas --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-bold text-slate-800">Identitas</h2>
        <dl class="mt-3 space-y-3 text-sm">
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Nama Lengkap</dt>
                <dd class="text-right font-semibold text-slate-800">{{ $student->full_name }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Kelas</dt>
                <dd class="text-right font-semibold text-slate-800">{{ $student->schoolClass->name ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Tahun Ajaran</dt>
                <dd class="text-right font-semibold text-slate-800">{{ $student->schoolClass->academic_year ?? '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Nomor Induk</dt>
                <dd class="text-right font-semibold text-slate-800">{{ $student->student_number ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Status</dt>
                <dd>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $student->statusLabel() }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Cara Mengisi</dt>
                <dd class="text-right font-semibold text-slate-800">Tanpa login (tulis nama di /sibel)</dd>
            </div>
        </dl>

        <a href="{{ route('sibel.students.edit', $student) }}"
           class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Edit Data
        </a>
    </div>

    {{-- Riwayat pengisian --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-bold text-slate-800">Riwayat Pengisian Kuesioner</h2>
            <p class="mt-0.5 text-xs text-slate-500">Setiap sesi tersimpan sebagai riwayat — pembaruan instrumen tidak mengubah hasil historis.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Kuesioner</th>
                        <th class="px-5 py-3">Mulai</th>
                        <th class="px-5 py-3">Selesai</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attempts as $attempt)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3">
                                <p class="font-semibold text-slate-800">{{ $attempt->questionnaire->title ?? '—' }}</p>
                                <p class="text-xs text-slate-500">v{{ $attempt->questionnaire->version ?? '?' }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ optional($attempt->started_at)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ optional($attempt->completed_at)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $attempt->isCompleted() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $attempt->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                @if ($attempt->result)
                                    @if ($attempt->result->isTie())
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            Seri: {{ $attempt->result->dominantLabel() }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            {{ $attempt->result->dominantLabel() }}
                                        </span>
                                    @endif
                                    <a href="{{ route('sibel.results.show', $attempt->result) }}" class="ml-2 text-xs font-semibold text-emerald-700 hover:underline">detail →</a>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada sesi pengisian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attempts->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">{{ $attempts->links() }}</div>
        @endif
    </div>
</div>
@endsection
