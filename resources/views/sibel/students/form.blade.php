@extends('sibel.layouts.app')

@section('title', isset($student) ? 'Edit Siswa' : 'Tambah Siswa')
@section('breadcrumb', isset($student) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<x-admin.page-header :title="isset($student) ? 'Edit Siswa' : 'Tambah Siswa'"
    subtitle="Data identitas siswa — dipakai sebagai pengisi kuesioner di halaman /sibel (tanpa akun login)">
    <x-slot:button>
        <a href="{{ route('sibel.students.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST"
      action="{{ isset($student) ? route('sibel.students.update', $student) : route('sibel.students.store') }}"
      class="mx-auto max-w-3xl space-y-6">
    @csrf
    @isset($student) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Siswa</h3>
            <p class="mt-0.5 text-xs text-slate-500">Wajib diisi. Digunakan untuk identitas laporan hasil kuesioner.</p>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Lengkap" name="full_name" :required="true">
                    <x-admin.input name="full_name" :value="old('full_name', $student->full_name ?? null)" placeholder="Nama lengkap siswa" />
                </x-admin.field>

                <x-admin.field label="Nomor Induk" name="student_number"
                    hint="Opsional. Harus unik bila diisi.">
                    <x-admin.input name="student_number" :value="old('student_number', $student->student_number ?? null)" placeholder="Mis. 2026001" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Kelas" name="class_id" :required="true">
                    <x-admin.select name="class_id" :value="old('class_id', $student->class_id ?? null)" placeholder="Pilih kelas"
                        :options="$classes->mapWithKeys(fn ($class) => [$class->id => $class->label()])->all()" />
                </x-admin.field>

                <x-admin.field label="Status" name="status" :required="true">
                    <x-admin.select name="status" :value="old('status', $student->status ?? 'active')"
                        :options="\App\Models\SibelStudent::STATUS_LABELS" />
                </x-admin.field>
            </div>
        </div>
    </div>

    @if (isset($student) && $student->user_id)
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            Siswa ini tercatat punya akun login website lama (<span class="font-semibold">{{ $student->user->email ?? '-' }}</span>).
            Pengisian kuesioner kini <strong>tanpa login</strong> — cukup tulis nama di halaman <a href="{{ route('sibel.portal') }}" class="font-semibold underline">SIBEL</a>.
        </div>
    @endif

    <div class="flex justify-end gap-2">
        <a href="{{ route('sibel.students.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ isset($student) ? 'Simpan Perubahan' : 'Tambah Siswa' }}
        </button>
    </div>
</form>
@endsection
