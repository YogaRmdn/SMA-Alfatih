@extends('admin.layouts.app')

@section('title', isset($gallery) ? 'Edit Item Galeri' : 'Tambah Item Galeri')

@section('content')
@php
    $galleryType = in_array(old('type', $gallery->type ?? 'photo'), ['photo', 'video'], true) ? old('type', $gallery->type ?? 'photo') : 'photo';
@endphp
<x-admin.page-header title="{{ isset($gallery) ? 'Edit Item Galeri' : 'Tambah Item Galeri' }}">
    <x-slot:button>
        <a href="{{ route('admin.galleries.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($gallery) ? route('admin.galleries.update', $gallery) : route('admin.galleries.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6" x-data="{ type: @js($galleryType) }">
    @csrf
    @isset($gallery) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Galeri</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Album">
                <x-admin.select name="album_id" :options="$albums->pluck('title', 'id')" :value="$gallery->album_id ?? null" placeholder="Tanpa album" />
            </x-admin.field>

            <x-admin.field label="Tipe Konten" name="type" required>
                <div class="flex gap-3">
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm">
                        <input type="radio" name="type" value="photo" x-model="type" class="text-emerald-600 focus:ring-emerald-500"> Foto
                    </label>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm">
                        <input type="radio" name="type" value="video" x-model="type" class="text-emerald-600 focus:ring-emerald-500"> Video
                    </label>
                </div>
            </x-admin.field>

            <x-admin.field label="Judul">
                <x-admin.input name="title" :value="$gallery->title ?? ''" />
            </x-admin.field>

            <div x-show="type === 'photo'" x-cloak>
                <x-admin.image-upload name="image" label="File Foto" :path="$gallery->image ?? null" hint="JPG/PNG/WebP" resize="1920x1080" />
            </div>

            <div x-show="type === 'video'" x-cloak>
                <x-admin.field label="URL Video (YouTube)">
                    <x-admin.input name="video_url" :value="$gallery->video_url ?? ''" placeholder="https://www.youtube.com/watch?v=..." />
                </x-admin.field>
            </div>

            <x-admin.field label="Deskripsi">
                <x-admin.textarea name="description" rows="2" :value="$gallery->description ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Urutan">
                    <x-admin.input type="number" name="sort_order" :value="$gallery->sort_order ?? 0" />
                </x-admin.field>
                <div class="pt-1">
                    <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$gallery->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.galleries.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
