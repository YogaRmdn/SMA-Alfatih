@extends('admin.layouts.app')

@section('title', 'Kelola Galeri')

@section('content')
<x-admin.page-header title="Kelola Galeri" subtitle="Kelola foto dan video dokumentasi kegiatan">
    <x-slot:button>
        <a href="{{ route('admin.galleries.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Item
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <select name="album_id" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Album</option>
                @foreach ($albums as $album)
                    <option value="{{ $album->id }}" {{ request('album_id') == $album->id ? 'selected' : '' }}>{{ $album->title }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Foto & Video</option>
                <option value="photo" {{ request('type') === 'photo' ? 'selected' : '' }}>Foto</option>
                <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 lg:grid-cols-4">
        @forelse ($galleries as $gallery)
            <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white">
                @if ($gallery->type === 'video')
                    <div class="relative flex h-40 items-center justify-center bg-slate-900 sm:h-44">
                        <svg class="h-12 w-12 text-white/80" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                        <span class="absolute bottom-2 left-2 rounded bg-black/50 px-1.5 py-0.5 text-[10px] font-medium text-white">VIDEO</span>
                    </div>
                @else
                    <img src="{{ asset('storage/'.$gallery->image) }}" class="h-40 w-full object-cover sm:h-44" alt="{{ $gallery->title ?? 'Foto galeri' }}" loading="lazy">
                @endif
                <div class="flex items-center justify-between bg-white p-3">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-slate-700">{{ $gallery->title ?? 'Tanpa judul' }}</p>
                        <p class="text-[10px] text-slate-400">{{ $gallery->album?->title ?? 'Tanpa album' }}</p>
                    </div>
                    <div class="flex gap-1">
                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="rounded p-1.5 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600" title="Edit">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </a>
                        <form id="delete-gallery-{{ $gallery->id }}" method="POST" action="{{ route('admin.galleries.destroy', $gallery) }}">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-gallery-{{ $gallery->id }}')" class="rounded p-1.5 text-slate-500 hover:bg-red-50 hover:text-red-600" title="Hapus">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-400">Belum ada item galeri.</div>
        @endforelse
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$galleries" />
    </div>
</div>
@endsection
