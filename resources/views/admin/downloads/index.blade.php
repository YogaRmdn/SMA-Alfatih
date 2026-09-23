@extends('admin.layouts.app')

@section('title', 'Kelola Download')

@section('content')
<x-admin.page-header title="Kelola File Download" subtitle="Kelola File yang Dapat Diunduh Pengunjung">
    <x-slot:button>
        <a href="{{ route('admin.downloads.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah File
        </a>
        <x-admin.delete-all :route="'admin.downloads.delete-all'" message="Semua file download akan dihapus permanen dari storage. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari File..."
                   class="rounded-lg border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 md:w-64">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Cari</button>
        </form>
    </div>

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($downloads as $download)
            <div class="flex items-start gap-3 p-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-xs font-bold uppercase text-red-600">{{ $download->file_type }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-slate-800">
                        <a href="{{ route('admin.downloads.edit', $download) }}" class="hover:text-emerald-700">{{ $download->title }}</a>
                    </p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] text-slate-500">
                        <span>{{ $download->category ?? '-' }}</span>
                        @if ($download->file_size)
                            <span class="text-slate-400">&middot; {{ format_bytes($download->file_size) }}</span>
                        @endif
                        <span class="text-slate-400">&middot; {{ number_format($download->downloads) }}x diunduh</span>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$download" route-prefix="admin.downloads" />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada file.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">File</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Ukuran</th>
                    <th class="px-5 py-3">Downloads</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($downloads as $download)
                    <tr class="transition hover:bg-slate-50">
                        <td class="max-w-md px-5 py-3">
                            <a href="{{ route('admin.downloads.edit', $download) }}" class="inline-flex items-center gap-2 font-medium text-slate-800 hover:text-emerald-700">
                                <span class="rounded-lg bg-red-50 px-2 py-1 text-[11px] font-bold uppercase text-red-600">{{ $download->file_type }}</span>
                                {{ $download->title }}
                            </a>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $download->category ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $download->file_size ? format_bytes($download->file_size) : '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ number_format($download->downloads) }}x</td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$download" route-prefix="admin.downloads" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada file.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$downloads" />
    </div>
</div>
@endsection
