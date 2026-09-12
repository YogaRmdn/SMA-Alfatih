@extends('admin.layouts.app')

@section('title', isset($album) ? 'Edit Album' : 'Tambah Album')

@section('content')
<x-admin.page-header title="{{ isset($album) ? 'Edit Album' : 'Tambah Album' }}">
    <x-slot:button>
        <a href="{{ route('admin.albums.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($album) ? route('admin.albums.update', $album) : route('admin.albums.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($album) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Album</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Album" name="title" required>
                <x-admin.input name="title" :value="$album->title ?? ''" required />
            </x-admin.field>
            <x-admin.field label="Deskripsi">
                <x-admin.textarea name="description" rows="3" :value="$album->description ?? ''" />
            </x-admin.field>
            <x-admin.image-upload name="cover" label="Cover Album" :path="$album->cover ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.albums.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
