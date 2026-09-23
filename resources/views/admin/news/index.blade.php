@extends('admin.layouts.app')

@section('title', 'Kelola Berita')

@section('content')
<x-admin.page-header title="Kelola Berita" subtitle="Kelola Seluruh Berita Sekolah">
    <x-slot:button>
        <a href="{{ route('admin.news.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Berita
        </a>
        <x-admin.delete-all :route="'admin.news.delete-all'" message="Semua berita beserta thumbnail akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Judul Berita..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="category_id" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
            @if (request()->hasAny(['q', 'category_id', 'status']))
                <a href="{{ route('admin.news.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($news as $item)
            <div class="flex items-start gap-3 p-4">
                @if ($item->thumbnail)
                    <img src="{{ asset('storage/'.$item->thumbnail) }}" class="h-14 w-16 shrink-0 rounded-lg object-cover" alt="{{ $item->title }}" loading="lazy">
                @else
                    <span class="flex h-14 w-16 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" /></svg>
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <p class="line-clamp-2 text-sm font-semibold text-slate-800">
                        <a href="{{ route('admin.news.edit', $item) }}" class="hover:text-emerald-700">{{ $item->title }}</a>
                    </p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                        @if ($item->category)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[12px] font-medium text-emerald-700">{{ $item->category->name }}</span>
                        @endif
                        @if ($item->is_published)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[12px] font-semibold text-emerald-700">Published</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[12px] font-semibold text-amber-700">Draft</span>
                        @endif
                    </div>
                    <p class="mt-1.5 text-[12px] text-slate-500">
                        {{ $item->author?->name ?? '-' }} &middot; {{ $item->published_at?->translatedFormat('d M Y') ?? '-' }} &middot; {{ number_format($item->views) }}x dilihat
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$item" route-prefix="admin.news" />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada berita.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Thumbnail</th>
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Penulis</th>
                    <th class="px-5 py-3">Views</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Tanggal</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($news as $item)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3">
                            @if ($item->thumbnail)
                                <img src="{{ asset('storage/'.$item->thumbnail) }}" class="h-12 w-16 rounded-lg object-cover" alt="{{ $item->title }}" loading="lazy">
                            @else
                                <span class="flex h-12 w-16 items-center justify-center rounded-lg bg-slate-100 text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="max-w-xs px-5 py-3">
                            <a href="{{ route('admin.news.edit', $item) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $item->title }}</a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $item->category?->name ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->author?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ number_format($item->views) }}</td>
                        <td class="px-5 py-3">
                            @if ($item->is_published)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Published</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->published_at?->translatedFormat('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$item" route-prefix="admin.news" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400">Belum ada berita.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$news" />
    </div>
</div>
@endsection
