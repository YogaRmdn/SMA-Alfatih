@extends('admin.layouts.app')

@section('title', isset($category) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<x-admin.page-header title="{{ isset($category) ? 'Edit Kategori' : 'Tambah Kategori' }}">
    <x-slot:button>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<div class="mx-auto max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5 p-6">
        @csrf
        @isset($category) @method('PUT') @endisset

        <x-admin.field label="Nama Kategori" name="name" required>
            <x-admin.input name="name" :value="$category->name ?? ''" required />
        </x-admin.field>

        <x-admin.field label="Deskripsi">
            <x-admin.textarea name="description" rows="3" :value="$category->description ?? ''" />
        </x-admin.field>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
        </div>
    </form>
</div>
@endsection
