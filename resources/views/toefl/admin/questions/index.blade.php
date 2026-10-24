@extends('admin.layouts.app')

@section('title', 'Kelola Soal — '.$exam->title)

@section('content')
<x-admin.page-header :title="'Kelola Soal: '.$exam->title" :subtitle="'Kunci jawaban hanya tersimpan di server — tidak pernah dikirim ke halaman siswa.'">
    <x-slot:button>
        <a href="{{ route('toefl.admin.exams.show', $exam) }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="space-y-6" x-data="{ tab: 'question' }">
    {{-- Pilih aksi --}}
    <div class="flex flex-wrap gap-2">
        <button @click="tab = 'question'" :class="tab === 'question' ? 'bg-indigo-700 text-white' : 'bg-white text-slate-700 border border-slate-300'"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition">Tambah Soal</button>
        <button @click="tab = 'passage'" :class="tab === 'passage' ? 'bg-indigo-700 text-white' : 'bg-white text-slate-700 border border-slate-300'"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition">Tambah Bacaan</button>
        <button @click="tab = 'audio'" :class="tab === 'audio' ? 'bg-indigo-700 text-white' : 'bg-white text-slate-700 border border-slate-300'"
            class="rounded-lg px-4 py-2 text-sm font-semibold transition">Unggah Audio</button>
    </div>

    {{-- Form tambah soal --}}
    <div x-show="tab === 'question'" x-cloak class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-800">Tambah Soal Baru</h2></div>

        <form method="POST" action="{{ route('toefl.admin.questions.store', $exam) }}" class="space-y-4 p-5" x-data="questionForm()">
            @csrf
            <div class="grid gap-4 md:grid-cols-3">
                <x-admin.field label="Bagian" name="section_id" required>
                    <x-admin.select name="section_id" :options="$exam->sections->mapWithKeys(fn ($s) => [$s->id => $s->title])" :value="old('section_id')" placeholder="Pilih bagian" required />
                </x-admin.field>
                <x-admin.field label="Jenis Soal" name="question_type" required>
                    <x-admin.select name="question_type" :options="$questionTypes" :value="old('question_type', 'multiple_choice')" required />
                </x-admin.field>
                <x-admin.field label="Bacaan (opsional)" name="passage_id" hint="Khusus soal berbasis bacaan.">
                    <x-admin.select name="passage_id" :options="$exam->sections->flatMap->passages->mapWithKeys(fn ($p) => [$p->id => ($p->title ?: 'Bacaan #'.$p->id)])" :value="old('passage_id')" placeholder="Tanpa bacaan" />
                </x-admin.field>
            </div>

            <x-admin.field label="Teks Soal" name="question_text" required>
                <x-admin.textarea name="question_text" rows="3" :value="old('question_text')" placeholder="Tulis pertanyaan…" required />
            </x-admin.field>

            <div>
                <p class="mb-1.5 block text-sm font-medium text-slate-700">Pilihan Jawaban <span class="text-red-500">*</span></p>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach ($answerOptions as $code)
                        <div class="flex items-center gap-2">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-700">{{ $code }}</span>
                            <input type="text" name="options[{{ $code }}]" value="{{ old("options.$code") }}"
                                placeholder="Teks pilihan {{ $code }}"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>
                    @endforeach
                </div>
                @error('options')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <x-admin.field label="Kunci Jawaban" name="answer_key" required>
                    <x-admin.select name="answer_key" :options="array_combine($answerOptions, $answerOptions)" :value="old('answer_key')" placeholder="Pilih" required />
                </x-admin.field>
                <x-admin.field label="Audio (Listening)" name="audio_asset_id" hint="Hanya untuk soal Listening.">
                    <x-admin.select name="audio_asset_id" :options="$exam->sections->flatMap->audioAssets->mapWithKeys(fn ($a) => [$a->id => ($a->label ?: $a->storage_path)])" :value="old('audio_asset_id')" placeholder="Tanpa audio" />
                </x-admin.field>
                <x-admin.field label="Bobot Poin" name="points" hint="Default 1.">
                    <x-admin.input type="number" name="points" :value="old('points', 1)" min="1" max="100" />
                </x-admin.field>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Simpan Soal</button>
            </div>
        </form>
    </div>

    {{-- Form tambah bacaan --}}
    <div x-show="tab === 'passage'" x-cloak class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-800">Tambah Bacaan (Reading)</h2></div>

        <form method="POST" action="{{ route('toefl.admin.passages.store', $exam) }}" class="space-y-4 p-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.field label="Bagian" name="section_id" required>
                    <x-admin.select name="section_id" :options="$exam->sections->mapWithKeys(fn ($s) => [$s->id => $s->title])" placeholder="Pilih bagian" required />
                </x-admin.field>
                <x-admin.field label="Judul Bacaan" name="title">
                    <x-admin.input name="title" :value="old('title')" placeholder="Opsional" />
                </x-admin.field>
            </div>

            <x-admin.field label="Isi Bacaan" name="passage_text" required hint="Mendukung baris baru (enter).">
                <x-admin.textarea name="passage_text" rows="8" :value="old('passage_text')" required />
            </x-admin.field>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Simpan Bacaan</button>
            </div>
        </form>
    </div>

    {{-- Form unggah audio --}}
    <div x-show="tab === 'audio'" x-cloak class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="font-semibold text-slate-800">Unggah Audio Listening</h2>
            <p class="mt-0.5 text-xs text-slate-500">File disimpan pada disk privat (di luar public/) — diakses lewat endpoint bertanda tangan.</p>
        </div>

        <form method="POST" action="{{ route('toefl.admin.audio.store', $exam) }}" enctype="multipart/form-data" class="space-y-4 p-5">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <x-admin.field label="Bagian" name="section_id" required>
                    <x-admin.select name="section_id" :options="$exam->sections->mapWithKeys(fn ($s) => [$s->id => $s->title])" placeholder="Pilih bagian" required />
                </x-admin.field>
                <x-admin.field label="Label Audio" name="label">
                    <x-admin.input name="label" :value="old('label')" placeholder="Contoh: Soal 1–5" />
                </x-admin.field>
            </div>

            <x-admin.field label="Berkas Audio" name="audio" required hint="MP3 / WAV / OGG / M4A, maksimal 50 MB.">
                <input type="file" name="audio" id="audio" accept=".mp3,.wav,.ogg,.m4a,audio/*" required
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </x-admin.field>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Unggah</button>
            </div>
        </form>
    </div>
</div>

{{-- Daftar soal per bagian --}}
@foreach ($exam->sections as $section)
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">{{ $section->title }}</h3>
                <p class="text-xs text-slate-500">{{ $section->questions()->count() }} soal · {{ $section->durationLabel() }}</p>
            </div>
            <span class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $section->section_type }}</span>
        </div>

        <div class="max-h-[32rem] divide-y divide-slate-100 overflow-y-auto">
            @forelse ($section->questions as $question)
                <div class="flex items-start gap-3 px-5 py-3" x-data="{ editing: false }">
                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-slate-100 text-xs font-bold text-slate-600">{{ $question->question_order }}</span>

                    <div class="min-w-0 flex-1">
                        <div x-show="!editing">
                            <p class="text-sm text-slate-800">{{ \Illuminate\Support\Str::limit($question->question_text, 160) }}</p>
                            <p class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500">
                                <span class="rounded bg-slate-100 px-1.5 py-0.5 font-semibold text-slate-600">Jawaban: {{ $question->answer_key }}</span>
                                <span class="rounded bg-slate-100 px-1.5 py-0.5">{{ $question->typeLabel() }}</span>
                                @if ($question->passage_id)<span class="rounded bg-amber-100 px-1.5 py-0.5 text-amber-700">ada bacaan</span>@endif
                                @if ($question->audio_asset_id)<span class="rounded bg-indigo-100 px-1.5 py-0.5 text-indigo-700">ada audio</span>@endif
                            </p>
                        </div>

                        {{-- Form edit ringkas --}}
                        <form x-show="editing" x-cloak method="POST" action="{{ route('toefl.admin.questions.update', [$exam, $question]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="section_id" value="{{ $question->section_id }}">
                            <textarea name="question_text" rows="2" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $question->question_text }}</textarea>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($answerOptions as $code)
                                    <label class="flex items-center gap-1 text-xs text-slate-600">
                                        <input type="text" name="options[{{ $code }}]" value="{{ $question->options[$code] ?? '' }}" required
                                            class="w-44 rounded border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                        <input type="radio" name="answer_key" value="{{ $code }}" {{ $question->answer_key === $code ? 'checked' : '' }} required
                                            class="h-3.5 w-3.5 border-slate-300 text-indigo-600 focus:ring-indigo-500" title="Jadikan kunci">
                                        <span class="font-bold">{{ $code }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <input type="hidden" name="question_type" value="{{ $question->question_type }}">
                            <div class="flex gap-2">
                                <button type="submit" class="rounded-lg bg-indigo-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-800">Simpan</button>
                                <button type="button" @click="editing = false" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                            </div>
                        </form>
                    </div>

                    <div class="flex shrink-0 items-center gap-1.5">
                        <button type="button" @click="editing = !editing"
                            class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs font-medium text-slate-700 hover:bg-slate-50"
                            x-text="editing ? 'Batal' : 'Edit'">Edit</button>
                        <form method="POST" action="{{ route('toefl.admin.questions.destroy', [$exam, $question]) }}"
                              onsubmit="return confirm('Hapus soal ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-slate-500">Belum ada soal pada bagian ini.</p>
            @endforelse
        </div>

        {{-- Audio bagian --}}
        @if ($section->audioAssets->isNotEmpty())
            <div class="border-t border-slate-100 px-5 py-3">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Audio Bagian Ini</p>
                <ul class="space-y-1.5">
                    @foreach ($section->audioAssets as $audio)
                        <li class="flex items-center justify-between gap-3 text-xs text-slate-600">
                            <span class="truncate">{{ $audio->label ?: $audio->storage_path }}
                                <span class="text-slate-400">· {{ $audio->size_bytes ? round($audio->size_bytes / 1024).' KB' : '' }}</span>
                            </span>
                            <form method="POST" action="{{ route('toefl.admin.audio.destroy', [$exam, $audio]) }}"
                                  onsubmit="return confirm('Hapus audio ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="shrink-0 rounded border border-red-200 px-2 py-0.5 text-[11px] font-medium text-red-600 hover:bg-red-50">Hapus</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endforeach

{{-- Bacaan yang tersimpan --}}
@if ($exam->sections->flatMap->passages->isNotEmpty())
    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-3"><h3 class="text-sm font-bold text-slate-800">Bacaan Tersimpan</h3></div>
        <div class="max-h-72 divide-y divide-slate-100 overflow-y-auto">
            @foreach ($exam->sections->flatMap->passages as $passage)
                <div class="flex items-start justify-between gap-3 px-5 py-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $passage->title ?: 'Bacaan #'.$passage->id }}</p>
                        <p class="truncate text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($passage->passage_text, 140) }}</p>
                    </div>
                    <form method="POST" action="{{ route('toefl.admin.passages.destroy', [$exam, $passage]) }}"
                          onsubmit="return confirm('Hapus bacaan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="shrink-0 rounded-lg border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function questionForm() {
        return {};
    }
</script>
@endpush
