@extends('admin.layouts.app')

@section('title', isset($extracurricular) ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler')

@section('content')
<x-admin.page-header title="{{ isset($extracurricular) ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler' }}">
    <x-slot:button>
        <a href="{{ route('admin.extracurriculars.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($extracurricular) ? route('admin.extracurriculars.update', $extracurricular) : route('admin.extracurriculars.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($extracurricular) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Ekstrakurikuler</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Nama Ekstrakurikuler" name="name" required>
                <x-admin.input name="name" :value="$extracurricular->name ?? ''" required placeholder="Contoh: Futsal" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Jadwal">
                    <x-admin.input name="schedule" :value="$extracurricular->schedule ?? ''" placeholder="Contoh: Sabtu, 14.00 - 16.00" />
                </x-admin.field>
                <x-admin.field label="Pembina">
                    <x-admin.input name="advisor" :value="$extracurricular->advisor ?? ''" />
                </x-admin.field>
            </div>

            <x-admin.field label="Deskripsi">
                <x-admin.textarea name="description" rows="3" :value="$extracurricular->description ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Foto" :path="$extracurricular->image ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$extracurricular->sort_order ?? 0" />
                    </x-admin.field>
                    <x-admin.checkbox name="is_active" label="Aktif tampil di website" :checked="$extracurricular->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.extracurriculars.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
