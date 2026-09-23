@extends('admin.layouts.app')

@section('title', isset($download) ? 'Edit File' : 'Tambah File')

@section('content')
<x-admin.page-header title="{{ isset($download) ? 'Edit File' : 'Tambah File' }}">
    <x-slot:button>
        <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($download) ? route('admin.downloads.update', $download) : route('admin.downloads.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($download) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail File</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Judul File" name="title" required>
                    <x-admin.input name="title" :value="$download->title ?? ''" required />
                </x-admin.field>
                <x-admin.field label="Kategori">
                    <x-admin.input name="category" :value="$download->category ?? ''" placeholder="Contoh: Surat Resmi" />
                </x-admin.field>
            </div>

            <x-admin.field label="Deskripsi">
                <x-admin.textarea name="description" rows="3" :value="$download->description ?? ''" />
            </x-admin.field>

            <x-admin.file-upload name="file" label="File" required :path="$download->file ?? null" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xls,.xlsx" hint="PDF/DOC/JPG/PNG/XLS" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.downloads.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
