@extends('admin.layouts.app')

@section('title', 'Kelola Album')

@section('content')
<x-admin.page-header title="Kelola Album" subtitle="Kelompokkan Foto dan Video ke dalam Album">
    <x-slot:button>
        <a href="{{ route('admin.albums.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Album
        </a>
        <x-admin.delete-all :route="'admin.albums.delete-all'" message="Semua album beserta foto/video galeri di dalamnya akan dihapus permanen. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($albums as $album)
        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <a href="{{ route('admin.albums.edit', $album) }}" class="block">
                <div class="relative h-44 overflow-hidden bg-emerald-50">
                    @if ($album->cover)
                        <img src="{{ asset('storage/'.$album->cover) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $album->title }}" loading="lazy">
                    @else
                        <div class="flex h-full items-center justify-center">
                            <svg class="h-12 w-12 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                    @endif
                    <span class="absolute right-3 top-3 rounded-full bg-black/60 px-2.5 py-1 text-xs font-semibold text-white">{{ $album->galleries_count }} item</span>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-slate-800">{{ $album->title }}</h3>
                    <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $album->description ?? 'Tanpa deskripsi' }}</p>
                </div>
            </a>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-4 py-2.5">
                <x-admin.actions :item="$album" route-prefix="admin.albums" />
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada album.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$albums" />
</div>
@endsection
