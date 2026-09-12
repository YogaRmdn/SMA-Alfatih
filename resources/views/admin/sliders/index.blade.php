@extends('admin.layouts.app')

@section('title', 'Kelola Slider')

@section('content')
<x-admin.page-header title="Kelola Slider" subtitle="Kelola banner slider hero di halaman utama">
    <x-slot:button>
        <a href="{{ route('admin.sliders.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Slider
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    @forelse ($sliders as $slider)
        <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="relative h-48 overflow-hidden">
                <img src="{{ asset('storage/'.$slider->image) }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105" alt="{{ $slider->title }}" loading="lazy">
                @if (!$slider->is_active)
                    <span class="absolute left-3 top-3 rounded-full bg-amber-500 px-2.5 py-1 text-xs font-semibold text-white">Nonaktif</span>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-slate-800">{{ $slider->title ?? 'Tanpa judul' }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $slider->subtitle }}</p>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-4 py-2.5">
                <x-admin.actions :item="$slider" route-prefix="admin.sliders" />
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada slider.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$sliders" />
</div>
@endsection
