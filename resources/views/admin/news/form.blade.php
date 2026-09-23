@extends('admin.layouts.app')

@section('title', isset($news) ? 'Edit Berita' : 'Tambah Berita')

@section('content')
<x-admin.page-header title="{{ isset($news) ? 'Edit Berita' : 'Tambah Berita' }}"
    subtitle="{{ isset($news) ? 'Perbarui Informasi Berita' : 'Buat Berita Sekolah Baru' }}">
    <x-slot:button>
        <a href="{{ route('admin.news.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($news) ? route('admin.news.update', $news) : route('admin.news.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @isset($news) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Berita</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Berita" name="title" required>
                <x-admin.input name="title" :value="$news->title ?? ''" placeholder="Contoh: Siswa SMA IT Tahfizh Al-Fatih Pekanbaru Raih Juara OSN" required />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Kategori">
                    <x-admin.select name="category_id" :options="$categories->pluck('name', 'id')" :value="$news->category_id ?? null" placeholder="Pilih Kategori" />
                </x-admin.field>
                <x-admin.field label="Tanggal Terbit">
                    <x-admin.input type="datetime-local" name="published_at" :value="isset($news) && $news->published_at ? $news->published_at->format('Y-m-d\TH:i') : ''" />
                </x-admin.field>
            </div>

            <x-admin.field label="Ringkasan (Excerpt)">
                <x-admin.textarea name="excerpt" rows="2" :value="$news->excerpt ?? ''" placeholder="Ringkasan singkat berita (opsional)" />
            </x-admin.field>

            <x-admin.field label="Isi Berita" name="content" required>
                <textarea name="content" id="content" rows="14"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Tulis Isi Berita di Sini..." required>{{ old('content', $news->content ?? '') }}</textarea>
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="thumbnail" label="Thumbnail" :path="$news->thumbnail ?? null" hint="JPG/PNG/WebP" />
                <div class="space-y-4 pt-1">
                    <x-admin.checkbox name="is_published" label="Publikasikan Berita ini" :checked="$news->is_published ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.news.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ isset($news) ? 'Simpan Perubahan' : 'Simpan Berita' }}
        </button>
    </div>
</form>
@endsection
