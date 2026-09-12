@extends('admin.layouts.app')

@section('title', 'Kelola Halaman')

@section('content')
<x-admin.page-header title="Kelola Halaman" subtitle="Kelola halaman konten statis website">
    <x-slot:button>
        <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Halaman
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul halaman..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="section" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Bagian</option>
                <option value="profile" {{ request('section') === 'profile' ? 'selected' : '' }}>Profil</option>
                <option value="akademik" {{ request('section') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="lainnya" {{ request('section') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
            @if (request()->hasAny(['q', 'section']))
                <a href="{{ route('admin.pages.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Judul</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Bagian</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pages as $page)
                    <tr class="transition hover:bg-slate-50">
                        <td class="max-w-xs px-5 py-3">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $page->title }}</a>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $page->slug }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ ucfirst($page->section) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($page->is_active)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$page" route-prefix="admin.pages" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada halaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$pages" />
    </div>
</div>
@endsection
