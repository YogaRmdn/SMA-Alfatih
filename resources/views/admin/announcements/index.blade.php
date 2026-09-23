@extends('admin.layouts.app')

@section('title', 'Kelola Pengumuman')

@section('content')
<x-admin.page-header title="Kelola Pengumuman" subtitle="Publikasikan Pengumuman Resmi Sekolah">
    <x-slot:button>
        <a href="{{ route('admin.announcements.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Pengumuman
        </a>
        <x-admin.delete-all :route="'admin.announcements.delete-all'" message="Semua pengumuman beserta lampirannya akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1 max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Pengumuman..."
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

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($announcements as $item)
            <div class="flex items-start gap-3 p-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800">
                        <a href="{{ route('admin.announcements.edit', $item) }}" class="hover:text-emerald-700">{{ $item->title }}</a>
                    </p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                        @if ($item->is_published)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[12px] font-semibold text-emerald-700">Published</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[12px] font-semibold text-amber-700">Draft</span>
                        @endif
                        @if ($item->attachment)
                            <a href="{{ asset('storage/'.$item->attachment) }}" target="_blank" class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-[12px] font-medium text-sky-600">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                Lampiran
                            </a>
                        @endif
                    </div>
                    <p class="mt-1.5 text-[12px] text-slate-500">{{ $item->published_at?->translatedFormat('d M Y') ?? '-' }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$item" route-prefix="admin.announcements" />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada pengumuman.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
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
