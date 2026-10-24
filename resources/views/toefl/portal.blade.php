@extends('toefl.layouts.app')

@section('title', 'Portal Ujian TOEFL')

@section('content')
<div class="mx-auto w-full max-w-5xl px-4 py-8">

    {{-- Sambutan & identitas --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Portal Ujian TOEFL ITP Level 1</h1>
            <p class="mt-1 text-sm text-slate-500">
                Tanpa registrasi & login — cukup tulis identitas Anda, lalu masukkan kode akses dari pengawas.
            </p>
        </div>

        @if ($student)
            <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 shadow-sm">
                <p><span class="font-semibold text-slate-700">Nama:</span> {{ $student->full_name }}</p>
                <p><span class="font-semibold text-slate-700">Kelas:</span> {{ $student->class_name ?: '—' }}</p>
            </div>
        @endif
    </div>

    {{-- Sesi yang sedang berlangsung --}}
    @if ($activeAttempt)
        <div class="mb-6 rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700">Sesi Sedang Berlangsung</p>
                    <p class="mt-1 text-lg font-bold text-slate-800">{{ $activeAttempt->exam->title }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">
                        @if ($activeAttempt->started_at)
                            Mulai {{ $activeAttempt->started_at->format('d M Y H:i') }}
                            @if ($activeAttempt->deadline_at)
                                — batas waktu {{ $activeAttempt->deadline_at->format('H:i') }} WIB
                            @endif
                        @else
                            Belum dimulai — konfirmasi "Mulai Ujian" untuk mengaktifkan timer.
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ $activeAttempt->started_at
                        ? ($activeAttempt->isFinished()
                            ? route('toefl.result')
                            : route('toefl.exam.section', $activeAttempt->current_section_type ?: 'listening'))
                        : route('toefl.instructions') }}"
                       class="w-full rounded-lg bg-indigo-700 px-5 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-indigo-800 sm:w-auto">
                        {{ $activeAttempt->started_at ? 'Lanjutkan Ujian' : 'Lanjutkan ke Instruksi' }}
                    </a>
                    <form method="POST" action="{{ route('toefl.leave') }}"
                          onsubmit="return confirm('Lepas sesi ini dari perangkat?');">
                        @csrf
                        <button type="submit" class="rounded-lg border border-indigo-300 px-4 py-2.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100">
                            Keluar Sesi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        {{-- Identitas + kode akses --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-1 text-base font-bold text-slate-800">Mulai Ujian</h2>
                <p class="mb-4 text-xs text-slate-500">Tulis identitas Anda sesuai data, lalu masukkan kode akses dari pengawas.</p>

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                        {{ $errors->first('code') ?? $errors->first() }}
                    </div>
                @endif

                @if ($exams->isEmpty())
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-800">
                        Belum ada ujian TOEFL yang sedang dibuka. Silakan tunggu pengumuman pengawas ujian.
                    </div>
                @else
                    <form method="POST" action="{{ route('toefl.enter') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="full_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $student?->full_name) }}" required
                                maxlength="120" autocomplete="name" placeholder="Nama sesuai data peserta"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('full_name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="class_name" class="mb-1.5 block text-sm font-medium text-slate-700">Kelas <span class="text-red-500">*</span></label>
                            <input type="text" name="class_name" id="class_name" value="{{ old('class_name', $student?->class_name) }}" required
                                maxlength="60" placeholder="Contoh: XII-1"
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1.5 text-xs text-slate-500">Nama + kelas yang sama akan melanjutkan sesi Anda sebelumnya.</p>
                            @error('class_name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="exam_id" class="mb-1.5 block text-sm font-medium text-slate-700">Pilih Ujian <span class="text-red-500">*</span></label>
                            <select name="exam_id" id="exam_id" required
                                class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ (string) old('exam_id') === (string) $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }}{{ $exam->start_at ? ' — mulai '.$exam->start_at->format('d M Y H:i') : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exam_id')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="code" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Akses <span class="text-red-500">*</span></label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                maxlength="32" autocomplete="off" autocapitalize="characters" spellcheck="false"
                                placeholder="XXXX-XXXX"
                                class="w-full rounded-lg border-slate-300 text-center font-mono text-lg uppercase tracking-[0.3em] shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1.5 text-xs text-slate-500">8 karakter, contoh: <span class="font-mono font-semibold">AB12-CD34</span>. Berlaku untuk peserta ujian ini.</p>
                            @error('code')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit"
                            class="w-full rounded-lg bg-indigo-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-800">
                            Masuk Ujian
                        </button>
                    </form>

                    {{-- Jalur cepat khusus pengembangan: melompati kode akses. --}}
                    @if (config('app.debug'))
                        <form method="POST" action="{{ route('toefl.demo') }}" class="mt-4">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-lg border border-dashed border-indigo-300 bg-indigo-50 px-5 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                Masuk sebagai Peserta Demo
                            </button>
                            <p class="mt-2 text-xs text-slate-500">
                                Hanya tampil saat <code class="rounded bg-slate-100 px-1">APP_DEBUG=true</code>.
                                Memakai identitas "Peserta Demo" dan ujian yang sedang dibuka, tanpa kode akses.
                            </p>
                        </form>
                    @endif
                @endif
            </div>

            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-xs leading-relaxed text-slate-600">
                <p class="mb-2 font-semibold text-slate-700">Perhatian:</p>
                <ul class="list-inside list-disc space-y-1">
                    <li>Tidak perlu registrasi atau login — identitas cukup ditulis di form ini.</li>
                    <li>Waktu ujian dihitung di server dan tidak dapat dihentikan setelah dimulai.</li>
                    <li>Audio Listening hanya boleh diputar {{ config('toefl.audio.max_plays', 2) }} kali per audio.</li>
                    <li>Jawaban tersimpan otomatis — pastikan koneksi internet stabil.</li>
                    <li>Bila koneksi terputus, kembali ke portal lalu isi <strong>nama & kelas yang sama</strong> dan kode yang sama untuk melanjutkan.</li>
                </ul>
            </div>
        </div>

        {{-- Riwayat --}}
        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-slate-800">Riwayat Ujian</h2>

                @if ($attempts->isEmpty())
                    <p class="rounded-lg border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
                        @if ($student)
                            Belum ada ujian yang pernah Anda kerjakan.
                        @else
                            Riwayat muncul setelah Anda masuk dengan nama & kelas yang sama.
                        @endif
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-500">
                                    <th class="py-2 pr-3">Ujian</th>
                                    <th class="py-2 pr-3">Tanggal</th>
                                    <th class="py-2 pr-3">Status</th>
                                    <th class="py-2 pr-3 text-right">Skor</th>
                                    <th class="py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    @php
                                        $finished = ! $attempt->isInProgress();
                                        $showScore = $finished && $attempt->exam->show_result_to_student && $attempt->result;
                                    @endphp
                                    <tr class="border-b border-slate-100 last:border-0">
                                        <td class="py-3 pr-3 font-medium text-slate-700">{{ $attempt->exam->title }}</td>
                                        <td class="py-3 pr-3 text-xs text-slate-500">
                                            {{ $attempt->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-3 pr-3">
                                            <span @class([
                                                'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                                'bg-amber-100 text-amber-700' => $attempt->status === \App\Models\ToeflAttempt::STATUS_IN_PROGRESS,
                                                'bg-emerald-100 text-emerald-700' => $attempt->status === \App\Models\ToeflAttempt::STATUS_SUBMITTED,
                                                'bg-red-100 text-red-700' => $attempt->status === \App\Models\ToeflAttempt::STATUS_EXPIRED,
                                            ])>{{ $attempt->statusLabel() }}</span>
                                        </td>
                                        <td class="py-3 pr-3 text-right font-semibold text-slate-800">
                                            @if ($showScore)
                                                {{ $attempt->result->scaled_score }}
                                                <span class="text-[10px] font-normal text-slate-400">(latihan)</span>
                                            @else
                                                <span class="font-normal text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <button type="button"
                                                    onclick='prefillIdentity(@json($attempt->student->full_name), @json((string) $attempt->student->class_name), {{ $attempt->exam_id }})'
                                                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">
                                                {{ $finished ? 'Lihat Hasil' : 'Lanjutkan' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        Untuk melanjutkan atau melihat hasil, isi kembali nama, kelas, ujian, dan kode akses yang sama pada form di samping.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function prefillIdentity(name, className, examId) {
        var nameInput = document.getElementById('full_name');
        var classInput = document.getElementById('class_name');
        var examInput = document.getElementById('exam_id');

        if (nameInput) nameInput.value = name;
        if (classInput) classInput.value = className;
        if (examInput) examInput.value = String(examId);

        document.getElementById('code')?.focus();
        document.querySelector('form[action="{{ route('toefl.enter') }}"]')?.scrollIntoView({behavior: 'smooth', block: 'center'});
    }
</script>
@endpush
