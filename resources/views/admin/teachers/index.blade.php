@extends('admin.layouts.app')

@section('title', 'Data Guru')

@section('content')
<x-admin.page-header title="Data Guru" subtitle="Kelola data guru dan tenaga pendidik">
    <x-slot:button>
        <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Guru
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
    @forelse ($teachers as $teacher)
        <div class="group rounded-2xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-emerald-50">
                @if ($teacher->photo)
                    <img src="{{ asset('storage/'.$teacher->photo) }}" class="h-full w-full object-cover" alt="{{ $teacher->name }}" loading="lazy">
                @else
                    <span class="text-2xl font-bold text-emerald-700">{{ strtoupper(substr($teacher->name, 0, 1)) }}</span>
                @endif
            </div>
            <h3 class="mt-3 text-sm font-semibold text-slate-800">{{ $teacher->name }}</h3>
            <p class="text-xs text-slate-500">{{ $teacher->subject ?? 'Guru' }}</p>
            <div class="mt-3 flex justify-center gap-2">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-700">Edit</a>
                <form id="delete-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete('delete-teacher-{{ $teacher->id }}')" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada data guru.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$teachers" />
</div>
@endsection
