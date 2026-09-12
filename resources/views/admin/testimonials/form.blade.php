@extends('admin.layouts.app')

@section('title', isset($testimonial) ? 'Edit Testimoni' : 'Tambah Testimoni')

@section('content')
<x-admin.page-header title="{{ isset($testimonial) ? 'Edit Testimoni' : 'Tambah Testimoni' }}">
    <x-slot:button>
        <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($testimonial) ? route('admin.testimonials.update', $testimonial) : route('admin.testimonials.store') }}" enctype="multipart/form-data" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($testimonial) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail Testimoni</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-3">
                <x-admin.field label="Nama" name="name" required>
                    <x-admin.input name="name" :value="$testimonial->name ?? ''" required />
                </x-admin.field>
                <x-admin.field label="Status">
                    <x-admin.input name="position" :value="$testimonial->position ?? ''" placeholder="Alumni / Orang Tua" />
                </x-admin.field>
                <x-admin.field label="Tahun Lulus">
                    <x-admin.input type="number" name="alumni_year" :value="$testimonial->alumni_year ?? ''" />
                </x-admin.field>
            </div>

            <x-admin.field label="Isi Testimoni" name="content" required>
                <x-admin.textarea name="content" rows="4" :value="$testimonial->content ?? ''" required />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="photo" label="Foto" :path="$testimonial->photo ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
                <div class="space-y-4 pt-1">
                    <x-admin.field label="Urutan">
                        <x-admin.input type="number" name="sort_order" :value="$testimonial->sort_order ?? 0" />
                    </x-admin.field>
                    <x-admin.checkbox name="is_active" label="Aktif tampil di website" :checked="$testimonial->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.testimonials.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
