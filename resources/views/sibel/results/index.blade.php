@extends('sibel.layouts.app')

@section('title', 'Hasil Asesmen')
@section('breadcrumb', 'Hasil Asesmen')

@section('content')
<x-admin.page-header title="Hasil Asesmen" subtitle="Riwayat hasil kuesioner gaya belajar seluruh siswa">
    <x-slot:button>
        <div class="flex flex-wrap justify-end gap-2">
            <a href="{{ route('sibel.results.export', request()->query()) }}"
               class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900"
               title="Unduh rekap, distribusi kelas, & detail jawaban (XLSX)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" /></svg>
                Unduh Excel
            </a>
            <a href="{{ route('sibel.recap.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6m4 6V7m4 10v-3M3 21h18M5 21V10l7-6 7 6v11" /></svg>
                Rekap Kelas
            </a>
        </div>
    </x-slot:button>
</x-admin.page-header>

{{-- Filter --}}
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('sibel.results.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <x-admin.field label="Cari Siswa" name="q">
                <x-admin.input name="q" :value="request('q')" placeholder="Nama atau nomor induk..." />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-48">
            <x-admin.field label="Kelas" name="class_id">
                <x-admin.select name="class_id" :value="request('class_id')" placeholder="Semua kelas"
                    :options="$classes->mapWithKeys(fn ($class) => [$class->id => $class->name])->all()" />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-48">
            <x-admin.field label="Status Publikasi" name="published">
                <x-admin.select name="published" :value="request('published')" placeholder="Semua status"
                    :options="['1' => 'Sudah dipublikasikan', '0' => 'Belum dipublikasikan']" />
            </x-admin.field>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('sibel.results.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>
</div>

<div class="space-y-4">
    @forelse ($results as $result)
        @php $attempt = $result->attempt; @endphp
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md lg:flex-row lg:items-center">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">
                {{ strtoupper(substr($attempt->student->full_name ?? '?', 0, 1)) }}
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('sibel.results.show', $result) }}" class="text-base font-bold text-slate-800 hover:text-emerald-700">
                        {{ $attempt->student->full_name ?? 'Siswa terhapus' }}
                    </a>
                    <span class="rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-semibold text-sky-700">{{ $attempt->student->schoolClass->name ?? '—' }}</span>
                    @if ($result->is_published)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            Dipublikasikan
                        </span>
                    @else
                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Belum dipublikasikan</span>
                    @endif
                </div>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $attempt->questionnaire->title ?? '—' }} v{{ $result->questionnaire_version ?? '?' }} •
                    {{ optional($attempt->completed_at)->format('d/m/Y H:i') }}
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">
                @if ($result->isTie())
                    <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700">Seri: {{ $result->dominantLabel() }}</span>
                @else
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700">{{ $result->dominantLabel() }}</span>
                @endif
                <a href="{{ route('sibel.results.show', $result) }}"
                   class="rounded-lg bg-emerald-700 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Detail</a>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Belum ada hasil. Hasil muncul setelah siswa menyelesaikan kuesioner.
        </div>
    @endforelse
</div>

@if ($results->hasPages())
    <div class="mt-5">{{ $results->links() }}</div>
@endif
@endsection
