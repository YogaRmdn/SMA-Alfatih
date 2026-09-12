@extends('admin.layouts.app')

@section('title', isset($announcement) ? 'Edit Pengumuman' : 'Tambah Pengumuman')

@section('content')
<x-admin.page-header title="{{ isset($announcement) ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}">
    <x-slot:button>
        <a href="{{ route('admin.announcements.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($announcement) ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @isset($announcement) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Pengumuman</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Pengumuman" name="title" required>
                <x-admin.input name="title" :value="$announcement->title ?? ''" required />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Tanggal Terbit">
                    <x-admin.input type="datetime-local" name="published_at" :value="isset($announcement) && $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : ''" />
                </x-admin.field>
                <x-admin.file-upload name="attachment" label="Lampiran PDF" :path="$announcement->attachment ?? null" accept=".pdf,.doc,.docx" hint="PDF/DOC/DOCX, maks 10 MB" />
            </div>

            <x-admin.field label="Isi Pengumuman" name="content" required>
                <x-admin.textarea name="content" rows="6" :value="$announcement->content ?? ''" required />
            </x-admin.field>

            <x-admin.checkbox name="is_published" label="Publikasikan pengumuman ini" :checked="$announcement->is_published ?? true" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.announcements.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
