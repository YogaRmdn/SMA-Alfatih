@extends('admin.layouts.app')

@section('title', isset($ppdbFormField) ? 'Edit Pertanyaan PPDB' : 'Tambah Pertanyaan PPDB')

@section('content')
@php
    $editing = isset($ppdbFormField);
    $optionsText = old('options_text');
    if ($optionsText === null && $editing) {
        $optionsText = collect($ppdbFormField->optionList())
            ->map(fn ($label, $value) => $value === $label ? $label : $value.' = '.$label)
            ->implode("\n");
    }
@endphp

<x-admin.page-header
    :title="$editing ? 'Edit Pertanyaan' : 'Tambah Pertanyaan'"
    :subtitle="$editing ? 'Perbarui konfigurasi pertanyaan '.$ppdbFormField->key : 'Tambahkan pertanyaan baru ke formulir pendaftaran PPDB'">
    <x-slot:button>
        <a href="{{ route('admin.ppdb-form-fields.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Periksa kembali isian berikut:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
    action="{{ $editing ? route('admin.ppdb-form-fields.update', $ppdbFormField) : route('admin.ppdb-form-fields.store') }}"
    x-data="formFieldEditor(@js($editing ? $ppdbFormField->type : 'text'), @js($editing ? (bool) $ppdbFormField->is_required : true), @js($editing ? (bool) $ppdbFormField->is_active : true))">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800">Identitas Pertanyaan</h2>
                <p class="mt-1 text-sm text-slate-500">Key menentukan lokasi penyimpanan jawaban pendaftar.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.field label="Label Pertanyaan" name="label" required hint="Teks yang dilihat pendaftar.">
                        <x-admin.input name="label" required placeholder="Contoh: Nama Lengkap Siswa" :value="$ppdbFormField->label ?? ''" />
                    </x-admin.field>

                    <x-admin.field label="Key" name="key" required>
                        <x-admin.input name="key" required placeholder="nama_lengkap" :value="$ppdbFormField->key ?? ''" class="font-mono" />
                        <p class="mt-1 text-xs text-slate-400">
                            Huruf kecil, angka, dan garis bawah.
                            @if ($editing)
                                <span class="font-medium text-amber-600">Tidak dapat diubah setelah disimpan.</span>
                            @else
                                Sebaiknya tetapkan selamanya karena jawaban lama terikat ke key ini.
                            @endif
                        </p>
                    </x-admin.field>

                    <x-admin.field label="Tipe Input" name="type" required>
                        <x-admin.select name="type" :options="\App\Models\PpdbFormField::TYPE_LABELS" :value="$ppdbFormField->type ?? 'text'" x-model="type" />
                        @if ($editing)
                            <p class="mt-1 text-xs font-medium text-amber-600">Tipe tidak dapat diubah setelah disimpan.</p>
                        @endif
                    </x-admin.field>

                    <x-admin.field label="Lebar Kolom" name="width" required hint="Setengah berarti dua pertanyaan berdampingan.">
                        <x-admin.select name="width" :options="['full' => 'Penuh', 'half' => 'Setengah']" :value="$ppdbFormField->width ?? 'full'" />
                    </x-admin.field>

                    <div class="sm:col-span-2">
                        <x-admin.field label="Placeholder" name="placeholder" hint="Contoh isian yang diharapkan, misal: Nama sesuai akta kelahiran.">
                            <x-admin.input name="placeholder" :value="$ppdbFormField->placeholder ?? ''" placeholder="Contoh: Nama sesuai akta kelahiran" />
                        </x-admin.field>
                    </div>

                    <div class="sm:col-span-2">
                        <x-admin.field label="Petunjuk Tambahan" name="help_text" hint="Tampil di bawah field, contoh: Upload 1 file yang didukung. Maks 10 MB.">
                            <x-admin.textarea name="help_text" rows="2" :value="$ppdbFormField->help_text ?? ''" placeholder="Contoh: Upload 1 file yang didukung. Maks 10 MB." />
                        </x-admin.field>
                    </div>

                    <div class="sm:col-span-2">
                        <x-admin.field label="Digabung Dengan Pertanyaan Lain" name="group_label" hint="Field dengan nilai sama ditampilkan berdampingan dalam satu kartu. Kosongkan untuk baris tersendiri.">
                            <x-admin.input name="group_label" :value="$ppdbFormField->group_label ?? ''" placeholder="Contoh: Tempat & Tanggal Lahir Siswa" />
                        </x-admin.field>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-show="needsOptions" x-cloak>
                <h2 class="font-semibold text-slate-800">Daftar Pilihan</h2>
                <p class="mt-1 text-sm text-slate-500">Satu opsi per baris.</p>

                <x-admin.textarea name="options_text" rows="7" class="mt-4 font-mono text-sm" :value="$optionsText ?? ''"
                    placeholder="Laki-laki
Perempuan" />

                <p class="mt-2 text-xs text-slate-400">
                    Tulis <code>nilai = Label</code> untuk menyimpan nilai berbeda dari teks yang ditampilkan. Contoh:
                    <code>L = Laki-laki</code> akan menyimpan <code>L</code> untuk "Laki-laki".
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" x-show="isFile" x-cloak>
                <h2 class="font-semibold text-slate-800">Aturan Unggah Berkas</h2>
                <p class="mt-1 text-sm text-slate-500">Hanya berlaku untuk field bertipe unggah berkas.</p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <x-admin.field label="Format yang Diizinkan" name="accept" required hint="Pisahkan dengan koma, tanpa titik. Contoh: jpg,jpeg,png,pdf">
                        <x-admin.input name="accept" class="font-mono" :value="$ppdbFormField->accept ?? 'jpg,jpeg,png,pdf'" placeholder="jpg,jpeg,png,pdf" />
                    </x-admin.field>

                    <x-admin.field label="Batas Ukuran (KB)" name="max_kb" hint="Contoh: 10240 = 10 MB.">
                        <x-admin.input name="max_kb" type="number" min="1" max="256000" :value="$ppdbFormField->max_kb ?? \App\Models\PpdbFormField::DEFAULT_MAX_KB" />
                    </x-admin.field>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800">Pengaturan</h2>

                <div class="mt-4 space-y-4">
                    <label class="flex items-start gap-3">
                        <input type="hidden" name="is_required" value="0">
                        <input type="checkbox" name="is_required" value="1" x-model="required"
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm">
                            <span class="font-medium text-slate-700">Wajib diisi</span>
                            <span class="block text-xs text-slate-500">Pendaftar tidak bisa mengirim tanpa mengisi kolom ini.</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-model="active"
                            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm">
                            <span class="font-medium text-slate-700">Tampilkan di formulir</span>
                            <span class="block text-xs text-slate-500">Matikan untuk menyembunyikan tanpa menghapus.</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-slate-800">Urutan</h2>
                <div class="mt-4">
                    <x-admin.field label="Nomor Urutan" name="sort_order" hint="Semakin kecil, semakin atas. Naikkan 10 per pertanyaan.">
                        <x-admin.input name="sort_order" type="number" min="0" :value="$ppdbFormField->sort_order ?? 0" />
                    </x-admin.field>
                </div>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">
                <p class="font-semibold">Perhatian</p>
                <p class="mt-1">
                    Label, opsi, dan urutan boleh diubah kapan saja. Menghapus field tidak menghapus jawaban pendaftar yang
                    sudah tersimpan, tetapi field tersebut tidak lagi tampil di halaman pendaftaran.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
                    {{ $editing ? 'Simpan Perubahan' : 'Tambah Pertanyaan' }}
                </button>
                <a href="{{ route('admin.ppdb-form-fields.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    function formFieldEditor(type, required, active) {
        return {
            type,
            required,
            active,
            get needsOptions() {
                return this.type === 'select' || this.type === 'radio';
            },
            get isFile() {
                return this.type === 'file';
            },
        };
    }
</script>
@endpush
@endsection
