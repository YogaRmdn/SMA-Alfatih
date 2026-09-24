@extends('admin.layouts.app')

@section('title', isset($banner) ? 'Edit Banner' : 'Tambah Banner')

@section('content')
<x-admin.page-header title="{{ isset($banner) ? 'Edit Banner' : 'Tambah Banner' }}">
    <x-slot:button>
        <a href="{{ route('admin.banners.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($banner) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Banner</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul">
                <x-admin.input name="title" :value="$banner->title ?? ''" />
            </x-admin.field>

            <x-admin.field label="Link (opsional)" name="link" hint="URL tujuan saat banner diklik, mis. https://ppdb.alfatih.sch.id">
                <x-admin.input type="url" name="link" :value="$banner->link ?? ''" placeholder="https://..." />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Posisi" name="position">
                    <x-admin.select name="position" :options="['hero' => 'Hero (Slider Utama)', 'top' => 'Top (Paling Atas)', 'bottom' => 'Bottom (Paling Bawah)', 'side' => 'Side (Samping)']" :value="$banner->position ?? 'hero'" />
                </x-admin.field>
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$banner->sort_order ?? 0" />
                    </x-admin.field>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Gambar Banner" :path="$banner->image ?? null" hint="JPG/PNG/WebP" />
                <div class="space-y-4 pt-1">
                    <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$banner->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.banners.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
