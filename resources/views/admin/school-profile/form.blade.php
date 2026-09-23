@extends('admin.layouts.app')

@section('title', $meta['card'])

@section('content')
<x-admin.page-header :title="$meta['card']" :subtitle="$meta['description']">
    <x-slot:button>
        <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
            Halaman Statis
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ route($updateRoute) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">{{ $meta['card'] }}</h3>
            @if (! $page)
                <p class="mt-0.5 text-xs text-slate-500">Halaman ini akan dibuat otomatis saat disimpan.</p>
            @endif
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Judul Halaman" name="title" required>
                <x-admin.input name="title" :value="$page->title ?? $meta['card']" placeholder="Contoh: {{ $meta['card'] }}" required />
            </x-admin.field>

            <x-admin.field label="Isi Halaman" hint="Tulis konten {{ strtolower($meta['card']) }} di sini.">
                <x-admin.textarea name="content" rows="16" :value="$page->content ?? ''" placeholder="Tulis Isi Halaman di Sini..." />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="image" label="Gambar" :path="$page->image ?? null" hint="JPG/PNG/WebP" />
                <div class="pt-1">
                    <x-admin.checkbox name="is_active" label="Tampilkan di Website" :checked="$page->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            Simpan Halaman
        </button>
    </div>
</form>
@endsection