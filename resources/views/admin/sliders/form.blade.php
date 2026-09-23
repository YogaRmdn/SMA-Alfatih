@extends('admin.layouts.app')

@section('title', isset($slider) ? 'Edit Slider' : 'Tambah Slider')

@section('content')
<x-admin.page-header title="{{ isset($slider) ? 'Edit Slider' : 'Tambah Slider' }}">
    <x-slot:button>
        <a href="{{ route('admin.sliders.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($slider) ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($slider) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Slider</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul">
                <x-admin.input name="title" :value="$slider->title ?? ''" placeholder="Contoh: Selamat Datang di SMA IT Tahfizh Al-Fatih Pekanbaru" />
            </x-admin.field>

            <x-admin.field label="Sub Judul">
                <x-admin.textarea name="subtitle" rows="2" :value="$slider->subtitle ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Teks Tombol">
                    <x-admin.input name="button_text" :value="$slider->button_text ?? ''" placeholder="Contoh: Info PPDB" />
                </x-admin.field>
                <x-admin.field label="Link Tombol">
                    <x-admin.input name="button_link" :value="$slider->button_link ?? ''" placeholder="https://..." />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Gambar Slider" :path="$slider->image ?? null" hint="Disarankan 1920x800, JPG/PNG/WebP" />
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$slider->sort_order ?? 0" />
                    </x-admin.field>
                    <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$slider->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.sliders.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
