@extends('sibel.layouts.app')

@section('title', 'Detail Hasil')
@section('breadcrumb', 'Detail Hasil')

@section('content')
<div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Detail Hasil Asesmen</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $attempt->questionnaire->title }} • v{{ $result->questionnaire_version ?? $attempt->questionnaire->version }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sibel.results.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">← Daftar Hasil</a>
        <a href="{{ route('sibel.results.print', $result) }}" target="_blank" rel="noopener"
           class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">🖨️ Cetak Laporan</a>
    </div>
</div>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-4 xl:col-span-2">
        {{-- Identitas --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-slate-800">Identitas Siswa</h2>
            <dl class="mt-3 grid gap-x-8 gap-y-2 text-sm sm:grid-cols-2">
                <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
                    <dt class="text-slate-500">Nama</dt>
                    <dd class="font-semibold text-slate-800">{{ $attempt->student->full_name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
                    <dt class="text-slate-500">Kelas</dt>
                    <dd class="font-semibold text-slate-800">{{ $attempt->student->schoolClass->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
                    <dt class="text-slate-500">Nomor Induk</dt>
                    <dd class="font-semibold text-slate-800">{{ $attempt->student->student_number ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-3 border-b border-dashed border-slate-100 pb-2">
                    <dt class="text-slate-500">Tanggal Pengisian</dt>
                    <dd class="font-semibold text-slate-800">{{ optional($attempt->completed_at)->format('d F Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Skor --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-base font-bold text-slate-800">Hasil Skor</h2>
                @if ($result->isTie())
                    <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-sm font-bold text-indigo-700">Seri: {{ $result->dominantLabel() }}</span>
                @else
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-bold text-emerald-700">{{ $result->dominantLabel() }} Dominan</span>
                @endif
            </div>

            <div class="mt-4">
                <canvas id="chartResult" style="display: none" height="200"></canvas>
                <div id="chartResult-fallback">
                    @include('sibel.partials.score-bars', ['result' => $result])
                </div>
            </div>
        </div>

        {{-- Interpretasi & rekomendasi --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-slate-800">Interpretasi Hasil</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-700">{{ $result->interpretation }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-slate-800">Rekomendasi Strategi Belajar</h2>
            <div class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $result->recommendation }}</div>
        </div>

        {{-- Review jawaban --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-bold text-slate-800">Review Jawaban</h2>
                <p class="mt-0.5 text-xs text-slate-500">Jawaban siswa per pertanyaan (kode warna sesuai kategori).</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($attempt->questionnaire->questions as $question)
                    @php $answer = $answersByQuestion->get($question->id); @endphp
                    <div class="px-5 py-3 text-sm">
                        <p class="font-semibold text-slate-800">
                            <span class="mr-1.5 text-slate-400">{{ $question->order_number }}.</span>
                            {{ $question->question_text }}
                        </p>
                        @if ($answer && $answer->option)
                            <p class="mt-1 pl-6 text-slate-600">
                                <span class="mr-1.5 inline-flex h-5 w-5 items-center justify-center rounded-full text-[11px] font-bold text-white"
                                      style="background: {{ config('sibel.categories.' . $answer->option->category_code . '.hex', '#64748b') }}">{{ $answer->option->option_code }}</span>
                                {{ $answer->option->option_text }}
                                <span class="text-xs text-slate-400">({{ $answer->option->categoryLabel() }})</span>
                            </p>
                        @else
                            <p class="mt-1 pl-6 text-xs italic text-slate-400">Tidak dijawab (pertanyaan opsional)</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Kolom kanan: publikasi + riwayat --}}
    <div class="space-y-4">
        <form method="POST" action="{{ route('sibel.results.update', $result) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            @csrf @method('PUT')
            <h2 class="text-base font-bold text-slate-800">Tindak Lanjut Guru BK</h2>

            <label class="mt-4 flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" {{ $result->is_published ? 'checked' : '' }}
                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span class="text-sm text-slate-700">
                    <span class="font-semibold">Publikasikan hasil ke siswa</span>
                    <span class="block text-xs text-slate-500">Siswa baru dapat melihat skor, interpretasi, dan rekomendasi setelah dicentang.</span>
                </span>
            </label>

            <div class="mt-4">
                <x-admin.field label="Catatan Guru BK" name="teacher_note"
                    hint="Opsional. Tindak lanjut, pendampingan, atau catatan refleksi. Ditampilkan pada laporan.">
                    <x-admin.textarea name="teacher_note" rows="5" placeholder="Tulis catatan untuk siswa...">{{ old('teacher_note', $result->teacher_note) }}</x-admin.textarea>
                </x-admin.field>
            </div>

            <button type="submit" class="mt-4 w-full rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                Simpan Tindak Lanjut
            </button>
        </form>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-bold text-slate-800">Riwayat Siswa Ini</h2>
            @if ($otherAttempts->isEmpty())
                <p class="mt-2 text-sm text-slate-500">Ini satu-satunya sesi pengisian siswa ini.</p>
            @else
                <ul class="mt-3 space-y-2">
                    @foreach ($otherAttempts as $other)
                        <li class="flex items-center justify-between gap-2 rounded-xl border border-slate-100 p-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-slate-700">v{{ $other->questionnaire->version ?? '?' }} • {{ optional($other->completed_at)->format('d/m/Y') ?? 'belum selesai' }}</p>
                                <p class="text-xs text-slate-500">{{ $other->statusLabel() }}</p>
                            </div>
                            @if ($other->result)
                                <a href="{{ route('sibel.results.show', $other->result) }}" class="shrink-0 text-xs font-semibold text-emerald-700 hover:underline">lihat →</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <p class="rounded-xl bg-slate-100 p-4 text-xs leading-relaxed text-slate-500">{{ config('sibel.ethics_note') }}</p>
    </div>
</div>

@push('scripts')
    <script>
        window.sibelCharts.push({
            target: 'chartResult',
            type: 'bar',
            data: {
                labels: @json(array_column(config('sibel.categories'), 'label')),
                datasets: [{
                    label: '{{ $attempt->questionnaire->isLikert() ? 'Jumlah Poin Likert' : 'Jumlah jawaban' }}',
                    data: @json([$result->scoreOf('visual'), $result->scoreOf('auditori'), $result->scoreOf('kinestetik')]),
                    backgroundColor: @json(array_column(config('sibel.categories'), 'hex')),
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    </script>
@endpush
@endsection
