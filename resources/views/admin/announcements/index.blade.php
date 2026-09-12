@extends('admin.layouts.app')

@section('title', 'Kelola Pengumuman')

@section('content')
<x-admin.page-header title="Kelola Pengumuman" subtitle="Publikasikan pengumuman resmi sekolah">
    <x-slot:button>
        <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Pengumuman
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1 max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pengumuman..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="status" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Lampiran</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($announcements as $item)
                    <tr class="transition hover:bg-slate-50">
                        <td class="max-w-md px-5 py-3">
                            <a href="{{ route('admin.announcements.edit', $item) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $item->title }}</a>
                        </td>
                        <td class="px-5 py-3">
                            @if ($item->attachment)
                                <a href="{{ asset('storage/'.$item->attachment) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-600 hover:text-sky-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    PDF
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if ($item->is_published)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Published</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->published_at?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$item" route-prefix="admin.announcements" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada pengumuman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$announcements" />
    </div>
</div>
@endsection
