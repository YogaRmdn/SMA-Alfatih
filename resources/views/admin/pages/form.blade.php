@extends('admin.layouts.app')

@section('title', isset($page) ? 'Edit Halaman' : 'Tambah Halaman')

@section('content')
<x-admin.page-header title="{{ isset($page) ? 'Edit Halaman' : 'Tambah Halaman' }}">
    <x-slot:button>
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($page) ? route('admin.pages.update', $page) : route('admin.pages.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @isset($page) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Halaman</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Judul Halaman" name="title" required>
                    <x-admin.input name="title" :value="$page->title ?? ''" placeholder="Contoh: Visi & Misi" required />
                </x-admin.field>
                <x-admin.field label="Bagian" name="section" required>
                    <x-admin.select name="section" :options="['profile' => 'Profil', 'akademik' => 'Akademik', 'lainnya' => 'Lainnya']" :value="$page->section ?? null" placeholder="Pilih bagian" required />
                </x-admin.field>
            </div>

            <x-admin.field label="Slug" hint="Kosongkan untuk membuat otomatis dari judul.">
                <x-admin.input name="slug" :value="$page->slug ?? ''" placeholder="visi-misi" />
            </x-admin.field>

            <x-admin.field label="Isi Halaman">
                <textarea name="content" rows="14"
                    class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Tulis isi halaman di sini...">{{ old('content', $page->content ?? '') }}</textarea>
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Gambar" :path="$page->image ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
                <div class="pt-1">
                    <x-admin.checkbox name="is_active" label="Tampilkan di website" :checked="$page->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.pages.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            {{ isset($page) ? 'Simpan Perubahan' : 'Simpan Halaman' }}
        </button>
    </div>
</form>
@endsection
