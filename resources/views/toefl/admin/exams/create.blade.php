@extends('admin.layouts.app')

@section('title', 'Buat Ujian TOEFL')

@section('content')
<x-admin.page-header title="Buat Ujian TOEFL ITP Level 1" subtitle="Ujian baru dibuat sebagai draf dengan 3 bagian baku — lalu tambahkan soal.">
    <x-slot:button>
        <a href="{{ route('toefl.admin.exams.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ route('toefl.admin.exams.store') }}" class="mx-auto max-w-3xl space-y-6">
    @csrf

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4"><h3 class="font-semibold text-slate-800">Informasi Ujian</h3></div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Ujian" name="title" required>
                <x-admin.input name="title" :value="old('title')" placeholder="Contoh: Try Out TOEFL ITP — Kelas XII" required />
            </x-admin.field>

            <x-admin.field label="Deskripsi" name="description" hint="Opsional — ditampilkan pada portal siswa.">
                <x-admin.textarea name="description" rows="3" :value="old('description')" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Mulai (opsional)" name="start_at">
                    <x-admin.input type="datetime-local" name="start_at" :value="old('start_at')" />
                </x-admin.field>
                <x-admin.field label="Selesai (opsional)" name="end_at">
                    <x-admin.input type="datetime-local" name="end_at" :value="old('end_at')" />
                </x-admin.field>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
                    <input type="checkbox" name="show_result_to_student" value="1" {{ old('show_result_to_student', 1) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong>Tampilkan hasil ke siswa</strong><br><span class="text-xs text-slate-500">Bila mati, siswa hanya melihat layar "selesai".</span></span>
                </label>
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 text-sm text-slate-700">
                    <input type="checkbox" name="allow_review" value="1" {{ old('allow_review', 1) ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong>Izinkan tinjauan jawaban</strong><br><span class="text-xs text-slate-500">Siswa bisa berpindah bagian sebelum mengumpulkan.</span></span>
                </label>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Bagian & Durasi</h3>
            <p class="mt-0.5 text-xs text-slate-500">Tiga bagian baku dibuat otomatis. Durasi bisa diubah per bagian.</p>
        </div>
        <div class="space-y-4 p-6">
            @foreach ($sectionDefaults as $type => $def)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 p-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $def['label'] }}</p>
                        <p class="text-xs text-slate-500">{{ $def['description'] }} · {{ $def['default_questions'] }} soal baku</p>
                    </div>
                    <div class="w-44">
                        <label class="mb-1 block text-xs font-medium text-slate-600">Durasi (detik)</label>
                        <input type="number" name="section_duration[{{ $type }}]" min="60" max="14400"
                            value="{{ old("section_duration.$type", $def['default_duration']) }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('toefl.admin.exams.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Simpan Ujian</button>
    </div>
</form>
@endsection
