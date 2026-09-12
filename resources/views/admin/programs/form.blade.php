@extends('admin.layouts.app')

@section('title', isset($program) ? 'Edit Program' : 'Tambah Program')

@section('content')
<x-admin.page-header title="{{ isset($program) ? 'Edit Program' : 'Tambah Program' }}">
    <x-slot:button>
        <a href="{{ route('admin.programs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($program) ? route('admin.programs.update', $program) : route('admin.programs.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @isset($program) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Program</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Program" name="name" required>
                    <x-admin.input name="name" :value="$program->name ?? ''" required placeholder="Contoh: Program Tahfizh 30 Juz" />
                </x-admin.field>
                <x-admin.field label="Tipe Program" name="type" required>
                    <x-admin.select name="type" :options="['unggulan' => 'Program Unggulan', 'tahfizh' => 'Program Tahfizh', 'akademik' => 'Program Akademik', 'it' => 'Program IT']" :value="$program->type ?? 'unggulan'" />
                </x-admin.field>
            </div>

            <x-admin.field label="Ringkasan (Excerpt)">
                <x-admin.textarea name="excerpt" rows="2" :value="$program->excerpt ?? ''" placeholder="Ringkasan singkat program" />
            </x-admin.field>

            <x-admin.field label="Deskripsi Lengkap">
                <x-admin.textarea name="content" rows="8" :value="$program->content ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Gambar Program" :path="$program->image ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$program->sort_order ?? 0" />
                    </x-admin.field>
                    <x-admin.checkbox name="is_active" label="Aktif tampil di website" :checked="$program->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.programs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
