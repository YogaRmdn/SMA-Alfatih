@extends('sibel.layouts.app')

@section('title', isset($questionnaire) && $questionnaire->exists ? 'Edit Kuesioner' : 'Tambah Kuesioner')
@section('breadcrumb', isset($questionnaire) && $questionnaire->exists ? 'Edit Kuesioner' : 'Tambah Kuesioner')

@section('content')
<x-admin.page-header :title="isset($questionnaire) && $questionnaire->exists ? 'Edit Kuesioner' : 'Tambah Kuesioner'"
    subtitle="Atur judul, petunjuk, versi instrumen, dan aturan sesi pengisian">
    <x-slot:button>
        <a href="{{ route('sibel.questionnaires.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST"
      action="{{ isset($questionnaire) && $questionnaire->exists ? route('sibel.questionnaires.update', $questionnaire) : route('sibel.questionnaires.store') }}"
      class="mx-auto max-w-3xl space-y-6">
    @csrf
    @if (isset($questionnaire) && $questionnaire->exists) @method('PUT') @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Kuesioner</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Kuesioner" name="title" :required="true">
                <x-admin.input name="title" :value="old('title', $questionnaire->title ?? null)" placeholder="Kuesioner Kecenderungan Gaya Belajar Siswa" />
            </x-admin.field>

            <x-admin.field label="Petunjuk Pengisian" name="instructions"
                hint="Ditampilkan di atas daftar pertanyaan. Teks polos; baris baru otomatis menjadi paragraf.">
                <x-admin.textarea name="instructions" rows="6"
                    placeholder="1. Bacakan setiap pertanyaan dengan teliti.&#10;2. Pilih SATU pilihan yang paling menggambarkan kebiasaan belajar Anda.">{{ old('instructions', $questionnaire->instructions ?? null) }}</x-admin.textarea>
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Versi Instrumen" name="version" :required="true"
                    hint="Dicetak pada laporan historis — hasil lama tidak berubah saat instrumen diperbarui.">
                    <x-admin.input name="version" :value="old('version', $questionnaire->version ?? '1.0')" placeholder="1.0" />
                </x-admin.field>

                <x-admin.field label="Status Sesi" name="status" :required="true"
                    hint="Draft: belum bisa diisi. Sesi Dibuka: siswa bisa mulai. Sesi Ditutup: pengisian baru diblokir.">
                    <x-admin.select name="status" :value="old('status', $questionnaire->status ?? 'draft')"
                        :options="\App\Models\SibelQuestionnaire::STATUS_LABELS" />
                </x-admin.field>
            </div>

            @php
                // Metode penilaian dikunci setelah ada riwayat pengisian —
                // hasil historis harus tetap konsisten dengan cara skoringnya.
                $methodLocked = $questionnaire->exists && $questionnaire->attempts()->exists();
            @endphp
            @if ($methodLocked)
                <x-admin.field label="Metode Penilaian" name="scoring_method"
                    hint="Terkunci karena kuesioner ini sudah memiliki riwayat pengisian.">
                    <p class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700">
                        {{ $questionnaire->scoringMethodLabel() }}
                    </p>
                </x-admin.field>
            @else
                <x-admin.field label="Metode Penilaian" name="scoring_method" :required="true"
                    hint="Pilihan Tunggal: siswa memilih A/B/C per pertanyaan. Skala Likert: setiap pernyataan punya satu kategori indikator dan dijawab Sangat Sesuai s.d. Tidak Sesuai (nilai 4-1).">
                    <x-admin.select name="scoring_method" :value="old('scoring_method', $questionnaire->scoring_method ?? 'choice')"
                        :options="config('sibel.scoring_methods')" />
                </x-admin.field>
            @endif

            <div class="space-y-3 rounded-xl bg-slate-50 p-4">
                <label class="flex items-start gap-3">
                    <input type="hidden" name="allow_retake" value="0">
                    <input type="checkbox" name="allow_retake" value="1" {{ old('allow_retake', $questionnaire->allow_retake ?? false) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-sm text-slate-700">
                        <span class="font-semibold">Izinkan pengisian ulang</span>
                        <span class="block text-xs text-slate-500">Siswa yang sudah menyelesaikan kuesioner bisa mengisi ulang (riwayat lama tetap tersimpan).</span>
                    </span>
                </label>

                <label class="flex items-start gap-3">
                    <input type="hidden" name="needs_verification" value="0">
                    <input type="checkbox" name="needs_verification" value="1" {{ old('needs_verification', $questionnaire->needs_verification ?? true) ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                    <span class="text-sm text-slate-700">
                        <span class="font-semibold">Perlu verifikasi dokumen asli</span>
                        <span class="block text-xs text-slate-500">Centang selama bunyi pertanyaan & pemetaan A/B/C belum diverifikasi terhadap dokumen kuesioner resmi. Hapus centang setelah diverifikasi.</span>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('sibel.questionnaires.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ isset($questionnaire) && $questionnaire->exists ? 'Simpan Perubahan' : 'Tambah Kuesioner' }}
        </button>
    </div>
</form>
@endsection
