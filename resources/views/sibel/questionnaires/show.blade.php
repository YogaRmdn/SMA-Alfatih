@extends('sibel.layouts.app')

@section('title', 'Kelola Kuesioner')
@section('breadcrumb', 'Kelola Kuesioner')

@section('content')
<x-admin.page-header :title="$questionnaire->title"
    subtitle="Kelola pertanyaan, buka/tutup sesi, dan pantau status pengisian siswa">
    <x-slot:titleExtra>
        <span class="ml-2 inline-flex items-center rounded-full bg-sky-100 px-2.5 py-0.5 align-middle text-xs font-bold text-sky-700"
              title="Metode penilaian kuesioner ini">
            {{ $questionnaire->scoringMethodLabel() }}
        </span>
    </x-slot:titleExtra>
    <x-slot:button>
        <div class="flex flex-wrap justify-end gap-2">
            @if ($questionnaire->needs_verification)
                <form method="POST" action="{{ route('sibel.questionnaires.toggle-verification', $questionnaire) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600"
                        title="Tandai bahwa instrumen sudah cocok dengan dokumen asli">
                        Tandai Terverifikasi
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('sibel.questionnaires.toggle-verification', $questionnaire) }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                        Tandai Perlu Verifikasi
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('sibel.questionnaires.toggle-status', $questionnaire) }}">
                @csrf
                <button type="submit" class="rounded-lg {{ $questionnaire->isActive() ? 'border border-white/30 bg-white/10 text-white hover:bg-white/20' : 'bg-emerald-700 text-white hover:bg-emerald-800' }} px-4 py-2 text-sm font-semibold transition">
                    {{ $questionnaire->isActive() ? 'Tutup Sesi' : ($questionnaire->status === 'closed' ? 'Buka Lagi' : 'Buka Sesi Pengisian') }}
                </button>
            </form>

            <a href="{{ route('sibel.questionnaires.edit', $questionnaire) }}"
               class="rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Edit</a>
        </div>
    </x-slot:button>
</x-admin.page-header>

@if ($questionnaire->needs_verification)
    <div class="mb-5 flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.34 16a2 2 0 001.73 3z" /></svg>
        <p>
            <span class="font-bold">Instrumen ini masih perlu diverifikasi.</span>
            Bunyi pertanyaan dan pemetaan A=Visual, B=Auditori, C=Kinestetik adalah konfigurasi awal.
            Verifikasi terhadap dokumen kuesioner asli sebelum digunakan resmi, lalu klik "Tandai Terverifikasi".
            Jangan mengubah bunyi pertanyaan tanpa persetujuan.
        </p>
    </div>
@endif

{{-- Ringkasan --}}
<div class="mb-5 grid grid-cols-2 gap-4 sm:grid-cols-4">
    <x-admin.stat-card label="Pertanyaan" :value="$totalQuestions" color="violet"
        icon="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    <x-admin.stat-card label="Sedang Mengerjakan" :value="$progressCount" color="amber"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
    <x-admin.stat-card label="Sudah Selesai" :value="$completedCount" color="emerald"
        icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    <x-admin.stat-card label="Status Sesi" :value="$questionnaire->statusLabel()"
        :color="$questionnaire->isActive() ? 'teal' : 'sky'"
        icon="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
</div>

<div class="grid gap-6 xl:grid-cols-5">
    {{-- Builder pertanyaan --}}
    <div class="space-y-6 xl:col-span-3">
        {{-- Form tambah pertanyaan --}}
        <form method="POST" action="{{ route('sibel.questionnaires.questions.store', $questionnaire) }}" x-data="{ open: false }"
              class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            <button type="button" @click="open = !open" class="flex w-full items-center justify-between px-5 py-4 text-left">
                <span>
                    <span class="block text-base font-bold text-slate-800">Tambah Pertanyaan</span>
                    @if ($questionnaire->isLikert())
                        <span class="block text-xs text-slate-500">Setiap pernyataan dimiliki satu kategori indikator; 4 opsi skala Likert dibuat otomatis.</span>
                    @else
                        <span class="block text-xs text-slate-500">Setiap pertanyaan memiliki tepat 3 pilihan: A (Visual), B (Auditori), C (Kinestetik).</span>
                    @endif
                </span>
                <svg class="h-5 w-5 shrink-0 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            </button>

            <div x-show="open" x-cloak x-transition class="border-t border-slate-100 p-5">
                @if ($questionnaire->isLikert())
                    <x-admin.field label="Pernyataan" name="question_text" :required="true">
                        <x-admin.textarea name="question_text" rows="2" placeholder="Saya lebih mudah memahami materi ketika disertai gambar, diagram, atau skema." />
                    </x-admin.field>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-admin.field label="Kategori Indikator" name="category_code" :required="true"
                            hint="Kategori yang dinilai dari pernyataan ini.">
                            <x-admin.select name="category_code" :value="old('category_code')"
                                placeholder="Pilih kategori..."
                                :options="collect(config('sibel.categories'))->mapWithKeys(fn ($c, $key) => [$key => $c['label']])->all()" />
                        </x-admin.field>

                        <x-admin.field label="Nomor Urut" name="order_number" hint="Kosongkan untuk menempatkan di akhir.">
                            <x-admin.input name="order_number" type="number" :value="old('order_number')" placeholder="Otomatis" />
                        </x-admin.field>
                    </div>

                    <div class="mt-4 rounded-xl border border-sky-100 bg-sky-50/70 p-4">
                        <p class="mb-2 text-sm font-semibold text-sky-900">Opsi skala (dibuat otomatis)</p>
                        <ul class="space-y-1.5">
                            @foreach (config('sibel.likert.levels') as $level)
                                <li class="flex items-center gap-2 text-sm text-sky-900">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-sky-700 text-[11px] font-bold text-white">{{ $level['code'] }}</span>
                                    {{ $level['label'] }}
                                    <span class="text-xs text-sky-600">(nilai {{ $level['value'] }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <x-admin.field label="Teks Pertanyaan" name="question_text" :required="true">
                        <x-admin.textarea name="question_text" rows="2" placeholder="Ketika mempelajari hal baru, saya lebih suka..." />
                    </x-admin.field>

                    <div class="mt-4 space-y-3">
                        @foreach (config('sibel.categories') as $key => $category)
                            <x-admin.field :label="'Pilihan ' . $category['code'] . ' — ' . $category['label']" :name="'options.' . $category['code']" :required="true">
                                <x-admin.input :name="'options.' . $category['code']" placeholder="Teks pilihan jawaban..." />
                            </x-admin.field>
                        @endforeach
                    </div>

                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <x-admin.field label="Nomor Urut" name="order_number" hint="Kosongkan untuk menempatkan di akhir.">
                            <x-admin.input name="order_number" type="number" :value="old('order_number')" placeholder="Otomatis" />
                        </x-admin.field>
                    </div>
                @endif

                <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    Pertanyaan wajib dijawab
                </label>

                <button type="submit" class="mt-5 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    Simpan Pertanyaan
                </button>
            </div>
        </form>

        {{-- Daftar pertanyaan --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-bold text-slate-800">Daftar Pertanyaan</h2>
                <p class="mt-0.5 text-xs text-slate-500">Pertanyaan yang sudah memiliki jawaban riwayat tidak dapat dihapus agar laporan historis tetap utuh.</p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse ($questionnaire->questions as $question)
                    <div class="p-5" x-data="{ editing: false }">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="mr-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-700 text-xs font-bold text-white">{{ $question->order_number }}</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $question->question_text }}</span>
                                @if ($questionnaire->isLikert() && $question->category_code)
                                    <span class="ml-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-bold text-white"
                                          style="background: {{ config('sibel.categories.' . $question->category_code . '.hex', '#64748b') }}">{{ $question->categoryLabel() }}</span>
                                @endif
                                @unless ($question->is_required)
                                    <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-500">opsional</span>
                                @endunless
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <button type="button" @click="editing = !editing"
                                        class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-amber-600" title="Edit">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <form id="delete-question-{{ $question->id }}" method="POST" action="{{ route('sibel.questionnaires.questions.destroy', $question) }}">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        onclick="confirmDelete('delete-question-{{ $question->id }}', 'Hapus pertanyaan ini beserta pilihan jawabannya?')"
                                        class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <ul class="mt-3 space-y-1.5 pl-8">
                            @foreach ($question->options as $option)
                                <li class="text-sm text-slate-600">
                                    @if ($questionnaire->isLikert())
                                        <span class="mr-2 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-600 text-[11px] font-bold text-white">{{ $option->option_code }}</span>
                                        {{ $option->option_text }}
                                        <span class="ml-1 text-xs font-semibold text-slate-400">(nilai {{ $option->value ?? '—' }})</span>
                                    @else
                                        <span class="mr-2 inline-flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-bold text-white"
                                              style="background: {{ config('sibel.categories.' . $option->category_code . '.hex', '#64748b') }}">{{ $option->option_code }}</span>
                                        {{ $option->option_text }}
                                        <span class="ml-1 text-xs text-slate-400">({{ $option->categoryLabel() }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        {{-- Form edit --}}
                        <form x-show="editing" x-cloak method="POST" action="{{ route('sibel.questionnaires.questions.update', $question) }}" class="mt-4 rounded-xl border border-amber-200 bg-amber-50/60 p-4">
                            @csrf @method('PUT')
                            @if ($questionnaire->isLikert())
                                <x-admin.field label="Pernyataan" name="question_text" :required="true">
                                    <x-admin.textarea name="question_text" rows="2" :value="$question->question_text" />
                                </x-admin.field>

                                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                    <x-admin.field label="Kategori Indikator" name="category_code" :required="true">
                                        <x-admin.select name="category_code" :value="old('category_code', $question->category_code)"
                                            placeholder="Pilih kategori..."
                                            :options="collect(config('sibel.categories'))->mapWithKeys(fn ($c, $key) => [$key => $c['label']])->all()" />
                                    </x-admin.field>
                                    <x-admin.field label="Nomor Urut" name="order_number">
                                        <x-admin.input name="order_number" type="number" :value="$question->order_number" />
                                    </x-admin.field>
                                </div>
                            @else
                                <x-admin.field label="Teks Pertanyaan" name="question_text" :required="true">
                                    <x-admin.textarea name="question_text" rows="2" :value="$question->question_text" />
                                </x-admin.field>

                                <div class="mt-3 space-y-3">
                                    @foreach ($question->options as $option)
                                        <x-admin.field :label="'Pilihan ' . $option->option_code . ' — ' . $option->categoryLabel()" :name="'options.' . $option->option_code" :required="true">
                                            <x-admin.input :name="'options.' . $option->option_code" :value="$option->option_text" />
                                        </x-admin.field>
                                    @endforeach
                                </div>

                                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                                    <x-admin.field label="Nomor Urut" name="order_number">
                                        <x-admin.input name="order_number" type="number" :value="$question->order_number" />
                                    </x-admin.field>
                                </div>
                            @endif

                            <label class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                                <input type="hidden" name="is_required" value="0">
                                <input type="checkbox" name="is_required" value="1" {{ $question->is_required ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                Pertanyaan wajib dijawab
                            </label>

                            <div class="mt-4 flex gap-2">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Perubahan</button>
                                <button type="button" @click="editing = false" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Batal</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="px-5 py-10 text-center text-sm text-slate-500">
                        Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk menyusun instrumen.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Status pengisian --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
        <div class="border-b border-slate-100 px-5 py-4">
            <h2 class="text-base font-bold text-slate-800">Status Pengisian Siswa</h2>
            <p class="mt-0.5 text-xs text-slate-500">Siswa aktif dan partisipasi mereka pada kuesioner ini.</p>
        </div>

        <div class="max-h-[640px] divide-y divide-slate-100 overflow-y-auto">
            @forelse ($students as $student)
                @php $attempt = $statusMap[$student->id] ?? null; @endphp
                <div class="flex items-center gap-3 px-5 py-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $student->full_name }}</p>
                        <p class="truncate text-[11px] text-slate-500">{{ $student->schoolClass->name ?? '-' }}</p>
                    </div>
                    @if (! $attempt)
                        <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-500">Belum mulai</span>
                    @elseif ($attempt->isInProgress())
                        <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">Mengerjakan</span>
                    @else
                        <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Selesai</span>
                    @endif
                </div>
            @empty
                <p class="px-5 py-10 text-center text-sm text-slate-500">Belum ada siswa aktif. Tambahkan siswa terlebih dahulu.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
