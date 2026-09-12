@extends('admin.layouts.app')

@section('title', 'Kelola Ekstrakurikuler')

@section('content')
<x-admin.page-header title="Kelola Ekstrakurikuler" subtitle="Kelola kegiatan ekstrakurikuler siswa">
    <x-slot:button>
        <a href="{{ route('admin.extracurriculars.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Ekskul
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($extracurriculars as $extracurricular)
        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="relative h-36 overflow-hidden bg-emerald-50">
                @if ($extracurricular->image)
                    <img src="{{ asset('storage/'.$extracurricular->image) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $extracurricular->name }}" loading="lazy">
                @else
                    <div class="flex h-full items-center justify-center">
                        <svg class="h-12 w-12 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-800">{{ $extracurricular->name }}</h3>
                <p class="mt-1 text-xs text-slate-500">Jadwal: {{ $extracurricular->schedule ?? '-' }}</p>
                <p class="line-clamp-2 text-xs text-slate-400">{{ $extracurricular->description ?? '' }}</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-4 py-2.5">
                <x-admin.actions :item="$extracurricular" route-prefix="admin.extracurriculars" />
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada ekstrakurikuler.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$extracurriculars" />
</div>
@endsection
