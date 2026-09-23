@extends('admin.layouts.app')

@section('title', 'Kelola Prestasi')

@section('content')
<x-admin.page-header title="Kelola Prestasi" subtitle="Dokumentasikan Prestasi Siswa dan Sekolah">
    <x-slot:button>
        <a href="{{ route('admin.achievements.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Prestasi
        </a>
        <x-admin.delete-all :route="'admin.achievements.delete-all'" message="Semua prestasi beserta fotonya akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Prestasi..."
                   class="rounded-lg border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 md:w-64">
            <select name="category" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                <option value="Akademik" {{ request('category') === 'Akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="Non-Akademik" {{ request('category') === 'Non-Akademik' ? 'selected' : '' }}>Non-Akademik</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
        </form>
    </div>

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($achievements as $achievement)
            <div class="flex items-start gap-3 p-4">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800">{{ $achievement->title }}</p>
                    @if ($achievement->photo)
                        <img src="{{ asset('storage/'.$achievement->photo) }}" class="mt-2 h-20 w-28 rounded-lg object-cover" alt="{{ $achievement->title }}" loading="lazy">
                    @endif
                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[12px] font-medium text-emerald-700">{{ $achievement->category ?? '-' }}</span>
                        <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-[12px] font-medium text-amber-700">{{ $achievement->rank ?? '-' }}</span>
                        @if ($achievement->is_active)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[12px] font-semibold text-emerald-700">Aktif</span>
                        @else
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[12px] font-semibold text-slate-600">Nonaktif</span>
                        @endif
                    </div>
                    <p class="mt-1 text-[12px] text-slate-500">Tahun {{ $achievement->year ?? '-' }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$achievement" route-prefix="admin.achievements" />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada data prestasi.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Prestasi</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Juara</th>
                    <th class="px-5 py-3">Tahun</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($achievements as $achievement)
                    <tr class="transition hover:bg-slate-50">
                        <td class="max-w-md px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($achievement->photo)
                                    <img src="{{ asset('storage/'.$achievement->photo) }}" class="h-10 w-14 rounded-lg object-cover" alt="{{ $achievement->title }}" loading="lazy">
                                @endif
                                <span class="font-medium text-slate-800">{{ $achievement->title }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $achievement->category ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $achievement->rank ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $achievement->year ?? '-' }}</td>
                        <td class="px-5 py-3">
                            @if ($achievement->is_active)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$achievement" route-prefix="admin.achievements" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">Belum ada data prestasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$achievements" />
    </div>
</div>
@endsection
