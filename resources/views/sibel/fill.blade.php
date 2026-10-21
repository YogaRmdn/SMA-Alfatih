@extends('sibel.layouts.student')

@section('title', 'Isi Kuesioner Gaya Belajar')

@php
    $questions = $questionnaire->questions->values();
    $totalQuestions = $questions->count();
    $prefillName = old('full_name', $student->full_name ?? '');
    // Jawaban lama (saat validasi gagal) disuntikkan langsung ke state Alpine
    // lewat @js() — aman di atribut HTML (tanpa kutip ganda di hasilnya).
    $initialAnswers = (object) collect(old('answers', []))
        ->mapWithKeys(fn ($optionId, $questionId) => [(string) $questionId => (string) $optionId])
        ->all();
@endphp

@section('content')
<div class="mx-auto max-w-3xl"
     x-data="{
        answers: @js($initialAnswers),
        showConfirm: false,
        showMissing: false,
        get answered() { return Object.keys(this.answers).filter(k => this.answers[k]).length },
        get remaining() { return {{ $totalQuestions }} - this.answered },
        get progress() { return {{ $totalQuestions }} > 0 ? Math.round((this.answered / {{ $totalQuestions }}) * 100) : 0 },
        submitForm() {
            if (! document.getElementById('full_name')?.value.trim()) {
                document.getElementById('full_name')?.focus();
                return;
            }
            if (this.remaining > 0) {
                this.showMissing = true;
                document.getElementById('question-list')?.scrollIntoView({ behavior: 'smooth' });
                return;
            }
            this.showConfirm = true;
        }
     }">

    <form method="POST" action="{{ route('sibel.submit') }}" id="question-list" class="space-y-4"
          @submit.prevent="submitForm">
        @csrf

        {{-- Header kuesioner + identitas: cukup tulis nama --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h1 class="text-lg font-bold text-slate-800">Isi Kuesioner Gaya Belajar 👋</h1>
            <p class="mt-0.5 text-sm text-slate-600">{{ $questionnaire->title }} <span class="text-xs text-slate-400">• v{{ $questionnaire->version }}</span></p>

            @if ($questionnaire->instructions)
                <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50/70 p-4 text-sm text-emerald-900">
                    <p class="mb-1 font-bold">Petunjuk Pengisian</p>
                    <p class="whitespace-pre-line">{{ $questionnaire->instructions }}</p>
                </div>
            @endif

            <div class="mt-4">
                <label for="full_name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Lengkap Anda <span class="text-red-500">*</span></label>
                <input type="text" id="full_name" name="full_name" value="{{ $prefillName }}" required maxlength="120"
                       placeholder="Tulis nama Anda sesuai daftar sekolah"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('full_name')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-slate-400">Nama dipakai untuk mencatat hasil — pastikan ditulis dengan benar.</p>
                @enderror
            </div>

            {{-- Progres --}}
            <div class="mt-4">
                <div class="mb-1.5 flex items-center justify-between text-xs font-semibold text-slate-600">
                    <span>Progres pengisian</span>
                    <span><span x-text="answered"></span>/{{ $totalQuestions }} terjawab (<span x-text="progress"></span>%)</span>
                </div>
                <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-300" :style="'width:' + progress + '%'"></div>
                </div>
                <p x-show="remaining > 0" class="mt-1.5 text-xs font-medium text-amber-600">
                    Masih ada <span x-text="remaining"></span> pertanyaan yang belum dijawab.
                </p>
            </div>
        </div>

        @foreach ($questions as $question)
            <fieldset class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <legend class="sr-only">Pertanyaan {{ $question->order_number }}</legend>

                <div class="flex items-start gap-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-700 text-xs font-bold text-white">{{ $question->order_number }}</span>
                    <p class="pt-0.5 text-sm font-semibold text-slate-800">
                        {{ $question->question_text }}
                        @if ($question->is_required)
                            <span class="text-red-500">*</span>
                        @else
                            <span class="text-xs font-normal text-slate-400">(opsional)</span>
                        @endif
                    </p>
                </div>

                <div class="mt-4 space-y-2 pl-10">
                    @foreach ($question->options as $option)
                        @php
                            $oldValue = old('answers.' . $question->id);
                            $isChecked = $oldValue !== null && (string) $oldValue === (string) $option->id;
                        @endphp
                        <label for="answer-{{ $question->id }}-{{ $option->id }}"
                               class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 transition {{ $isChecked ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-400' : 'border-slate-200 hover:border-emerald-300 hover:bg-slate-50' }}">
                            <input type="radio" id="answer-{{ $question->id }}-{{ $option->id }}"
                                   name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                   x-model="answers['{{ $question->id }}']"
                                   {{ $isChecked ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="flex-1 text-sm text-slate-700">
                                <span class="mr-1.5 font-bold text-slate-800">{{ $option->option_code }}.</span>
                                {{ $option->option_text }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('answers.' . $question->id)
                    <p class="mt-2 pl-10 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </fieldset>
        @endforeach

        @error('answers')
            <p class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm font-medium text-red-700">{{ $message }}</p>
        @enderror

        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">
                Jawaban dikirim ke server untuk dinilai. Setelah selesai, hasil bisa langsung diunduh.
            </p>
            <button type="submit"
                    class="rounded-lg bg-emerald-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="remaining > 0">
                Kirim Jawaban
            </button>
        </div>
    </form>

    {{-- Konfirmasi sebelum pengiriman --}}
    <div x-show="showConfirm" x-cloak x-transition class="fixed inset-0 z-[95] flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" @click="showConfirm = false"></div>
        <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-slate-800">Kirim jawaban sekarang?</h3>
            <p class="mt-2 text-sm text-slate-600">
                Seluruh <span class="font-semibold">{{ $totalQuestions }}</span> pertanyaan sudah terjawab.
                Setelah dikirim, jawaban tidak bisa diubah kecuali guru BK mengizinkan pengisian ulang.
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="showConfirm = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Periksa Lagi</button>
                <button type="button" @click="document.querySelector('form#question-list').submit(); showConfirm = false"
                        class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>

    {{-- Peringatan jawaban kosong --}}
    <div x-show="showMissing" x-cloak x-transition class="fixed bottom-4 left-1/2 z-[95] w-[92%] max-w-md -translate-x-1/2 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm font-medium text-amber-800 shadow-lg"
         x-init="setInterval(() => showMissing = false, 4000)">
        ⚠️ Masih ada pertanyaan yang belum dijawab. Pilih satu jawaban untuk setiap pertanyaan sebelum mengirim.
    </div>
</div>
@endsection
