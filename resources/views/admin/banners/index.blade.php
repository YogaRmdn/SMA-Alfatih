@extends('admin.layouts.app')

@section('title', 'Kelola Banner')

@php
    $positionLabels = [
        'top' => 'Atas',
        'hero' => 'Hero (Halaman Utama)',
        'bottom' => 'Bawah',
        'side' => 'Samping',
    ];
@endphp

@section('content')
<x-admin.page-header title="Kelola Banner" subtitle="Kelola Banner Iklan dan Promo">
    <x-slot:button>
        <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Banner
        </a>
        <x-admin.delete-all :route="'admin.banners.delete-all'" message="Semua banner beserta gambarnya akan dihapus secara permanen. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    @forelse ($banners as $banner)
        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="relative h-32 overflow-hidden">
                <img src="{{ asset('storage/'.$banner->image) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $banner->title }}" loading="lazy">
                <span class="absolute left-3 top-3 rounded-full bg-emerald-700 px-2.5 py-1 text-xs font-semibold uppercase text-white">{{ $positionLabels[$banner->position] ?? $banner->position }}</span>
            </div>
            <div class="flex items-center justify-between p-4">
                <div>
                    <h3 class="font-semibold text-slate-800">{{ $banner->title ?? 'Tanpa judul' }}</h3>
                    <p class="text-xs text-slate-500">{{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                </div>
                <x-admin.actions :item="$banner" route-prefix="admin.banners" />
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada banner.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$banners" />
</div>
@endsection
