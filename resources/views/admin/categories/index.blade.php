@extends('admin.layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<x-admin.page-header title="Kelola Kategori Berita" subtitle="Kategorikan berita agar mudah ditemukan pengunjung">
    <x-slot:button>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Kategori
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex items-center gap-3">
            <div class="relative flex-1 max-w-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Jumlah Berita</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $category->name }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $category->slug }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">{{ $category->news_count }} berita</span>
                        </td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$category" route-prefix="admin.categories" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$categories" />
    </div>
</div>
@endsection
