@extends('sibel.layouts.app')

@section('title', 'Kelas')
@section('breadcrumb', 'Kelas')

@section('content')
<x-admin.page-header title="Kelas" subtitle="Kelompokkan siswa berdasarkan kelas untuk rekapitulasi hasil">
    <x-slot:button>
        <a href="{{ route('sibel.classes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Kelas
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($classes as $class)
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md">
            <div class="flex items-start justify-between">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </span>
                <div class="flex gap-1">
                    <a href="{{ route('sibel.classes.edit', $class) }}" title="Edit"
                       class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-amber-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </a>
                    <form id="delete-class-{{ $class->id }}" method="POST" action="{{ route('sibel.classes.destroy', $class) }}">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" title="Hapus"
                            onclick="confirmDelete('delete-class-{{ $class->id }}', 'Kelas beserta seluruh siswa di dalamnya (termasuk riwayat pengisian) akan dihapus permanen. Lanjutkan?')"
                            class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>

            <h3 class="mt-3 text-lg font-bold text-slate-800">{{ $class->name }}</h3>
            <p class="text-xs text-slate-500">{{ $class->academic_year ?? 'Tahun ajaran belum diatur' }}</p>

            <div class="mt-4 grid grid-cols-2 gap-2 text-center">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xl font-bold text-slate-800">{{ $class->students_count }}</p>
                    <p class="text-[11px] uppercase tracking-wide text-slate-500">Siswa</p>
                </div>
                <div class="rounded-xl bg-emerald-50 p-3">
                    <p class="text-xl font-bold text-emerald-700">{{ $completedCounts[$class->id] ?? 0 }}</p>
                    <p class="text-[11px] uppercase tracking-wide text-emerald-600">Selesai Mengisi</p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
            Belum ada kelas. Tambahkan kelas untuk mengelompokkan siswa.
        </div>
    @endforelse
</div>

@if ($classes->hasPages())
    <div class="mt-5">{{ $classes->links() }}</div>
@endif
@endsection
