@extends('admin.layouts.app')

@section('title', isset($achievement) ? 'Edit Prestasi' : 'Tambah Prestasi')

@section('content')
<x-admin.page-header title="{{ isset($achievement) ? 'Edit Prestasi' : 'Tambah Prestasi' }}">
    <x-slot:button>
        <a href="{{ route('admin.achievements.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($achievement) ? route('admin.achievements.update', $achievement) : route('admin.achievements.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($achievement) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Prestasi</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Nama Prestasi" name="title" required>
                <x-admin.input name="title" :value="$achievement->title ?? ''" required placeholder="Contoh: Juara 1 OSN Matematika Tingkat Provinsi" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-3">
                <x-admin.field label="Kategori">
                    <x-admin.select name="category" :options="['Akademik' => 'Akademik', 'Non-Akademik' => 'Non-Akademik']" :value="$achievement->category ?? null" placeholder="Pilih" />
                </x-admin.field>
                <x-admin.field label="Juara">
                    <x-admin.input name="rank" :value="$achievement->rank ?? ''" placeholder="Juara 1" />
                </x-admin.field>
                <x-admin.field label="Tahun">
                    <x-admin.input type="number" name="year" :value="$achievement->year ?? date('Y')" />
                </x-admin.field>
            </div>

            <x-admin.field label="Deskripsi">
                <x-admin.textarea name="description" rows="3" :value="$achievement->description ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="photo" label="Foto Prestasi" :path="$achievement->photo ?? null" hint="JPG/PNG/WebP" />
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$achievement->sort_order ?? 0" />
                    </x-admin.field>
                    <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$achievement->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.achievements.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
