@extends('admin.layouts.app')

@section('title', 'Kelola Partner')

@section('content')
<x-admin.page-header title="Kelola Partner" subtitle="Kelola Mitra dan Kerja Sama Sekolah">
    <x-slot:button>
        <a href="{{ route('admin.partners.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Partner
        </a>
        <x-admin.delete-all :route="'admin.partners.delete-all'" message="Semua partner beserta logonya akan dihapus secara permanen. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
    @forelse ($partners as $partner)
        <div class="group rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl bg-slate-50">
                @if ($partner->logo)
                    <img src="{{ asset('storage/'.$partner->logo) }}" class="h-full w-full object-contain p-1" alt="{{ $partner->name }}" loading="lazy">
                @else
                    <span class="text-xl font-bold text-emerald-700">{{ strtoupper(substr($partner->name, 0, 1)) }}</span>
                @endif
            </div>
            <h3 class="mt-3 truncate text-sm font-semibold text-slate-800">{{ $partner->name }}</h3>
            <div class="mt-3 flex justify-center gap-2">
                <a href="{{ route('admin.partners.edit', $partner) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-700">Edit</a>
                <form id="delete-partner-{{ $partner->id }}" method="POST" action="{{ route('admin.partners.destroy', $partner) }}">
                    @csrf @method('DELETE')
                    <button type="button" onclick="confirmDelete('delete-partner-{{ $partner->id }}')" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-red-50 hover:text-red-600">Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada partner.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$partners" />
</div>
@endsection
