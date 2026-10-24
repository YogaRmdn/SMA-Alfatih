@extends('admin.layouts.app')

@section('title', 'Edit Ujian TOEFL')

@section('content')
<x-admin.page-header title="Edit Ujian" :subtitle="$exam->title">
    <x-slot:button>
        <a href="{{ route('toefl.admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ route('toefl.admin.exams.update', $exam) }}" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4"><h3 class="font-semibold text-slate-800">Informasi Ujian</h3></div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Ujian" name="title" required>
                <x-admin.input name="title" :value="$exam->title" required />
            </x-admin.field>

            <x-admin.field label="Deskripsi" name="description">
                <x-admin.textarea name="description" rows="3" :value="old('description', $exam->description)" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Mulai" name="start_at">
                    <x-admin.input type="datetime-local" name="start_at" :value="$exam->start_at?->format('Y-m-d\TH:i')" />
                </x-admin.field>
                <x-admin.field label="Selesai" name="end_at">
                    <x-admin.input type="datetime-local" name="end_at" :value="$exam->end_at?->format('Y-m-d\TH:i')" />
                </x-admin.field>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
                    <input type="checkbox" name="show_result_to_student" value="1" {{ old('show_result_to_student', $exam->show_result_to_student) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong>Tampilkan hasil ke siswa</strong></span>
                </label>
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
                    <input type="checkbox" name="allow_review" value="1" {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong>Izinkan tinjauan jawaban</strong></span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('toefl.admin.exams.show', $exam) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Simpan Perubahan</button>
    </div>
</form>
@endsection
