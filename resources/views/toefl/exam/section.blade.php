@extends('toefl.layouts.app')

@section('title', $section->title)

@section('content')
@php
    $questionsPayload = $questionPayload->values();
    $answersMap = [];
    foreach ($answers as $questionId => $answer) {
        $answersMap[$questionId] = $answer->selected_answer;
    }
@endphp

<div class="mx-auto w-full max-w-4xl px-4 py-6"
     x-data="examEngine()"
     x-init="init()">

    {{-- Bar atas: bagian + timer + pengumpulan --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Bagian {{ $section->section_order }} dari {{ $exam->sections->count() }}</p>
            <h1 class="text-lg font-bold text-slate-800">{{ $section->title }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-xl bg-slate-900 px-4 py-2 text-center">
                <p class="text-[10px] font-medium uppercase tracking-wider text-slate-400">Sisa Waktu</p>
                <p class="font-mono text-lg font-bold tabular-nums"
                   :class="remaining <= 60 ? 'text-red-400 animate-pulse' : 'text-white'"
                   x-text="clock">00:00</p>
            </div>

            <button type="button" @click="confirmSubmit()"
                class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                Kumpulkan
            </button>
        </div>
    </div>

    {{-- Penanda terakhir tersimpan --}}
    <div class="mb-4 flex items-center gap-2 text-xs text-slate-500">
        <span class="h-2 w-2 rounded-full" :class="saveState === 'saving' ? 'bg-amber-400 animate-pulse' : (saveState === 'error' ? 'bg-red-500' : 'bg-emerald-500')"></span>
        <span x-text="saveLabel">Jawaban tersimpan otomatis</span>
        <span class="ml-auto">
            Terjawab: <strong class="text-slate-700" x-text="answeredCount"></strong>/{{ $questions->count() }}
        </span>
    </div>

    {{-- Navigasi bagian --}}
    <div class="mb-4 flex items-center justify-between gap-3">
        @if ($prevSection)
            <a href="{{ route('toefl.exam.section', $prevSection->section_type) }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                ← {{ $prevSection->title }}
            </a>
        @else
            <span></span>
        @endif

        @if ($nextSection)
            <a href="{{ route('toefl.exam.section', $nextSection->section_type) }}"
               class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
                {{ $nextSection->title }} →
            </a>
        @else
            <a href="{{ route('toefl.result') }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Menuju Pengumpulan
            </a>
        @endif
    </div>

    @php
        // Kelompokkan soal berdasarkan bacaan (passage) — untuk Reading.
        $groups = $questions->groupBy(fn ($q) => $q->passage_id ?? 'standalone');
    @endphp

    <div class="space-y-6">
        @foreach ($groups as $passageId => $groupQuestions)
            @if ($passageId !== 'standalone')
                @php $passage = $groupQuestions->first()->passage; @endphp
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Bacaan</p>
                        @if ($passage?->title)
                            <h2 class="text-sm font-bold text-slate-800">{{ $passage->title }}</h2>
                        @endif
                    </div>
                    <div class="max-h-72 overflow-y-auto whitespace-pre-wrap px-5 py-4 text-sm leading-relaxed text-slate-700">{{ $passage?->passage_text }}</div>
                </div>
            @endif

            <div class="space-y-4">
                @foreach ($groupQuestions as $question)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                         id="q-{{ $question->id }}"
                         x-data="question({{ $question->id }}, {{ json_encode($answersMap[$question->id] ?? null) }})">
                        <div class="flex items-start gap-3">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-xs font-bold text-indigo-700">
                                {{ $question->question_order }}
                            </span>
                            <div class="min-w-0 flex-1">
                                @if ($question->audio_asset_id)
                                    <div class="mb-3 rounded-xl border border-indigo-100 bg-indigo-50 p-3"
                                         x-data="audioPlayer({{ $question->audio_asset_id }})">
                                        <div class="flex items-center gap-3">
                                            <button type="button" @click="play()" :disabled="!canPlay || loading"
                                                class="shrink-0 rounded-lg px-3 py-2 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-50"
                                                :class="canPlay ? 'bg-indigo-700 hover:bg-indigo-800' : 'bg-slate-400'"
                                                x-text="loading ? 'Memuat…' : (canPlay ? '▶ Putar Audio' : 'Audio Habis')"></button>
                                            <audio x-ref="audio" controls class="hidden w-full"></audio>
                                            <span class="ml-auto shrink-0 text-xs font-medium text-slate-600">
                                                Sisa putar: <strong x-text="remaining"></strong>/<span x-text="max"></span>
                                            </span>
                                        </div>
                                        <p class="mt-2 text-xs text-slate-500">Audio hanya dapat diputar {{ $audioStatus[$question->audio_asset_id]['max'] ?? config('toefl.audio.max_plays') }} kali.</p>
                                    </div>
                                @endif

                                <p class="text-sm font-medium leading-relaxed text-slate-800">{!! nl2br(e($question->question_text)) !!}</p>

                                <div class="mt-3 space-y-2">
                                    @foreach ($question->options ?? [] as $code => $text)
                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-3 text-sm transition"
                                            :class="selected === '{{ $code }}' ? 'border-indigo-500 bg-indigo-50 ring-1 ring-indigo-500' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'">
                                            <input type="radio" name="answer_{{ $question->id }}" value="{{ $code }}"
                                                x-model="selected" @change="save()"
                                                class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-semibold text-slate-600">{{ $code }}.</span>
                                            <span class="text-slate-700">{!! nl2br(e($text)) !!}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Navigasi bawah --}}
    <div class="mt-6 flex items-center justify-between gap-3">
        @if ($prevSection)
            <a href="{{ route('toefl.exam.section', $prevSection->section_type) }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                ← {{ $prevSection->title }}
            </a>
        @else
            <span></span>
        @endif

        @if ($nextSection)
            <a href="{{ route('toefl.exam.section', $nextSection->section_type) }}"
               class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
                {{ $nextSection->title }} →
            </a>
        @else
            <button type="button" @click="confirmSubmit()"
                class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                Selesai & Kumpulkan
            </button>
        @endif
    </div>
</div>

{{-- Form submit tersembunyi --}}
<form method="POST" action="{{ route('toefl.exam.submit') }}" id="submitExamForm" class="hidden">
    @csrf
</form>
@endsection

@push('scripts')
<script>
window.TOEFL_CONFIG = {
    csrf: document.querySelector('meta[name="csrf-token"]').content,
    answerUrl: @json(route('toefl.exam.answer')),
    eventUrl: @json(route('toefl.exam.event')),
    audioPlayUrlBase: @json(route('toefl.audio.play', ['audio' => '__ID__'])),
    resultUrl: @json(route('toefl.result')),
    audioLimit: @json((int) config('toefl.audio.max_plays', 2)),
};

function examEngine() {
    return {
        remaining: {{ max(0, $secondsRemaining) }},
        clock: '00:00',
        saveState: 'saved',
        saveLabel: 'Jawaban tersimpan otomatis',
        answeredCount: @json($answers->whereNotNull('selected_answer')->count()),
        tick: null,

        init() {
            this.renderClock();
            this.tick = setInterval(() => {
                this.remaining = Math.max(0, this.remaining - 1);
                this.renderClock();
                if (this.remaining === 0) {
                    clearInterval(this.tick);
                    // Waktu habis -> server yang memutuskan: buka halaman hasil
                    // (route ini akan auto-finalize bila deadline terlampaui).
                    window.location.href = window.TOEFL_CONFIG.resultUrl;
                }
            }, 1000);

            // Penanda auto-save (dipicu dari tiap question component).
            window.addEventListener('toefl:saved', () => {
                this.saveState = 'saved';
                this.saveLabel = 'Jawaban tersimpan ' + new Date().toLocaleTimeString('id-ID');
                this.answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
            });
            window.addEventListener('toefl:save-error', () => {
                this.saveState = 'error';
                this.saveLabel = 'Gagal menyimpan — periksa koneksi Anda';
            });

            // Pemantauan browser: visibility + fullscreen (catatan pengawasan).
            document.addEventListener('visibilitychange', () => {
                this.postEvent(document.hidden ? 'tab_hidden' : 'tab_visible');
            });
            document.addEventListener('fullscreenchange', () => {
                if (!document.fullscreenElement) this.postEvent('fullscreen_exit');
            });
        },

        renderClock() {
            const h = Math.floor(this.remaining / 3600);
            const m = Math.floor((this.remaining % 3600) / 60);
            const s = this.remaining % 60;
            const pad = (n) => String(n).padStart(2, '0');
            this.clock = h > 0 ? `${pad(h)}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
        },

        postEvent(type) {
            fetch(window.TOEFL_CONFIG.eventUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.TOEFL_CONFIG.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ event_type: type }),
                keepalive: true,
            }).catch(() => {});
        },

        confirmSubmit() {
            if (confirm('Kumpulkan ujian sekarang? Jawaban yang sudah tersimpan akan dinilai.')) {
                document.getElementById('submitExamForm').submit();
            }
        },
    };
}

function question(questionId, initial) {
    return {
        selected: initial || null,
        save() {
            this.$dispatch('answer-saved', { questionId: questionId });
            fetch(window.TOEFL_CONFIG.answerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.TOEFL_CONFIG.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    question_id: questionId,
                    selected_answer: this.selected,
                }),
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (data.expired) {
                    alert(data.message || 'Waktu ujian telah habis.');
                    window.location.href = data.redirect || window.TOEFL_CONFIG.resultUrl;
                    return;
                }
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan');
                window.dispatchEvent(new CustomEvent('toefl:saved'));
            })
            .catch(() => window.dispatchEvent(new CustomEvent('toefl:save-error')));
        },
    };
}

function audioPlayer(audioId) {
    return {
        canPlay: true,
        loading: false,
        remaining: {{ json_encode($audioStatus) }}[audioId]?.remaining ?? window.TOEFL_CONFIG.audioLimit,
        max: window.TOEFL_CONFIG.audioLimit,
        play() {
            if (!this.canPlay || this.loading) return;
            this.loading = true;

            const url = window.TOEFL_CONFIG.audioPlayUrlBase.replace('__ID__', audioId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.TOEFL_CONFIG.csrf,
                    'Accept': 'application/json',
                },
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({}));
                if (res.status === 429 || data.limit_exceeded) {
                    this.canPlay = false;
                    this.remaining = 0;
                    alert(data.message || 'Batas pemutaran audio sudah tercapai.');
                    return;
                }
                if (!res.ok) throw new Error(data.message || 'Gagal memutar audio');

                this.remaining = data.remaining;
                if (data.remaining <= 0) this.canPlay = false;

                const audio = this.$refs.audio;
                audio.src = data.url;
                audio.play().catch(() => {});
            })
            .catch((e) => alert(e.message || 'Terjadi kesalahan saat memuat audio.'))
            .finally(() => { this.loading = false; });
        },
    };
}
</script>
@endpush
