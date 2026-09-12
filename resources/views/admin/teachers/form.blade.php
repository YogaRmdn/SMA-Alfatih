@extends('admin.layouts.app')

@section('title', isset($teacher) ? 'Edit Guru' : 'Tambah Guru')

@section('content')
<x-admin.page-header title="{{ isset($teacher) ? 'Edit Guru' : 'Tambah Guru' }}">
    <x-slot:button>
        <a href="{{ route('admin.teachers.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($teacher) ? route('admin.teachers.update', $teacher) : route('admin.teachers.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @isset($teacher) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Guru</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Lengkap" name="name" required>
                    <x-admin.input name="name" :value="$teacher->name ?? ''" required />
                </x-admin.field>
                <x-admin.field label="NIP">
                    <x-admin.input name="nip" :value="$teacher->nip ?? ''" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Mata Pelajaran">
                    <x-admin.input name="subject" :value="$teacher->subject ?? ''" placeholder="Contoh: Matematika" />
                </x-admin.field>
                <x-admin.field label="Jabatan">
                    <x-admin.input name="position" :value="$teacher->position ?? ''" placeholder="Contoh: Wali Kelas X-A" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Pendidikan Terakhir">
                    <x-admin.input name="education" :value="$teacher->education ?? ''" placeholder="Contoh: S1 Pendidikan Matematika" />
                </x-admin.field>
                <x-admin.field label="Urutan">
                    <x-admin.input type="number" name="sort_order" :value="$teacher->sort_order ?? 0" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="No. Telepon">
                    <x-admin.input name="phone" :value="$teacher->phone ?? ''" />
                </x-admin.field>
                <x-admin.field label="Email">
                    <x-admin.input type="email" name="email" :value="$teacher->email ?? ''" />
                </x-admin.field>
            </div>

            <x-admin.image-upload name="photo" label="Foto Guru" :path="$teacher->photo ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
            <x-admin.checkbox name="is_active" label="Aktif tampil di website" :checked="$teacher->is_active ?? true" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.teachers.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
