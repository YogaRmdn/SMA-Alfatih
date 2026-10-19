@extends('sibel.layouts.app')

@section('title', 'Kuesioner')
@section('breadcrumb', 'Kuesioner')

@section('content')
<x-admin.page-header title="Kuesioner Gaya Belajar" subtitle="Kelola instrumen kuesioner, sesi pengisian, dan verifikasi dokumen">
    <x-slot:button>
        <a href="{{ route('sibel.questionnaires.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Kuesioner
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="space-y-4">
    @forelse ($questionnaires as $questionnaire)
        <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-300 hover:shadow-md lg:flex-row lg:items-center">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-base font-bold text-slate-800">{{ $questionnaire->title }}</h3>
                    <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide {{ $questionnaire->statusColor() }}">
                        {{ $questionnaire->statusLabel() }}
                    </span>
                    @if ($questionnaire->needs_verification)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-amber-700">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.34 16a2 2 0 001.73 3z" /></svg>
                            Perlu Verifikasi
                        </span>
                    @endif
                    @if ($questionnaire->allow_retake)
                        <span class="rounded-full bg-sky-100 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-sky-700">Boleh Mengulang</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-slate-500">
                    v{{ $questionnaire->version }} • {{ $questionnaire->questions_count }} pertanyaan •
                    {{ $questionnaire->completed_attempts_count }} selesai / {{ $questionnaire->attempts_count }} sesi total
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="{{ route('sibel.questionnaires.show', $questionnaire) }}"
                   class="rounded-lg bg-emerald-700 px-3.5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Kelola</a>
                <a href="{{ route('sibel.questionnaires.edit', $questionnaire) }}"
                   class="rounded-lg border border-slate-300 px-3.5 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Edit</a>
                <form id="delete-questionnaire-{{ $questionnaire->id }}" method="POST" action="{{ route('sibel.questionnaires.destroy', $questionnaire) }}">
                    @csrf @method('DELETE')
                </form>
                <button type="button"
                        onclick="confirmDelete('delete-questionnaire-{{ $questionnaire->id }}', 'Kuesioner beserta seluruh pertanyaan, sesi pengisian, jawaban, dan hasilnya akan dihapus permanen. Riwayat laporan ikut hilang. Lanjutkan?')"
                        class="rounded-lg border border-red-200 px-3.5 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">Hapus</button>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Belum ada kuesioner. Buat kuesioner baru lalu tambahkan pertanyaannya.
        </div>
    @endforelse
</div>

@if ($questionnaires->hasPages())
    <div class="mt-5">{{ $questionnaires->links() }}</div>
@endif
@endsection
