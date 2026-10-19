@extends('sibel.layouts.app')

@section('title', isset($class) ? 'Edit Kelas' : 'Tambah Kelas')
@section('breadcrumb', isset($class) ? 'Edit Kelas' : 'Tambah Kelas')

@section('content')
<x-admin.page-header :title="isset($class) ? 'Edit Kelas' : 'Tambah Kelas'"
    subtitle="Nama kelas bersama tahun ajaran harus unik">
    <x-slot:button>
        <a href="{{ route('sibel.classes.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/30 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST"
      action="{{ isset($class) ? route('sibel.classes.update', $class) : route('sibel.classes.store') }}"
      class="mx-auto max-w-xl space-y-6">
    @csrf
    @isset($class) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Kelas</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Nama Kelas" name="name" :required="true" hint="Mis. X-A, XI-IPA-1">
                <x-admin.input name="name" :value="old('name', $class->name ?? null)" placeholder="Nama kelas" />
            </x-admin.field>

            <x-admin.field label="Tahun Ajaran" name="academic_year"
                hint="Opsional. Format bebas, mis. 2026/2027.">
                <x-admin.input name="academic_year" :value="old('academic_year', $class->academic_year ?? null)" placeholder="2026/2027" />
            </x-admin.field>
        </div>
    </div>

    <div class="flex justify-end gap-2">
        <a href="{{ route('sibel.classes.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ isset($class) ? 'Simpan Perubahan' : 'Tambah Kelas' }}
        </button>
    </div>
</form>
@endsection
