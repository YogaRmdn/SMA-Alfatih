@extends('admin.layouts.app')

@section('title', 'Kelola Fasilitas')

@section('content')
<x-admin.page-header title="Kelola Fasilitas" subtitle="Tampilkan fasilitas unggulan sekolah">
    <x-slot:button>
        <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Fasilitas
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($facilities as $facility)
        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="relative h-40 overflow-hidden bg-emerald-50">
                @if ($facility->image)
                    <img src="{{ asset('storage/'.$facility->image) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $facility->name }}" loading="lazy">
                @else
                    <div class="flex h-full items-center justify-center">
                        <svg class="h-12 w-12 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-800">{{ $facility->name }}</h3>
                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $facility->description ?? 'Tanpa deskripsi' }}</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-4 py-2.5">
                <x-admin.actions :item="$facility" route-prefix="admin.facilities" />
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada fasilitas.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$facilities" />
</div>
@endsection
