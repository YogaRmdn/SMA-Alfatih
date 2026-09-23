@extends('admin.layouts.app')

@section('title', 'Kelola Komentar')

@section('content')
<x-admin.page-header title="Kelola Komentar" subtitle="Moderasi Komentar Pengunjung">
    <x-slot:button>
        <x-admin.delete-all :route="'admin.comments.delete-all'" message="Semua komentar pengunjung akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <select name="status" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Komentar</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
            @if (request()->has('status'))
                <a href="{{ route('admin.comments.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse ($comments as $comment)
            <div class="p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">{{ mb_strtoupper(mb_substr($comment->name, 0, 1)) }}</span>
                        <div>
                            <p class="font-medium text-slate-800">{{ $comment->name }}
                                <span class="ml-1 text-xs font-normal text-slate-400">{{ $comment->email }}</span>
                            </p>
                            <p class="text-xs text-slate-500">
                                pada
                                <a href="{{ route('admin.news.edit', $comment->news) }}" class="font-medium text-emerald-700 hover:underline">{{ $comment->news?->title ?? 'Berita dihapus' }}</a>
                                &middot; {{ $comment->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($comment->is_approved)
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Disetujui</span>
                        @else
                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Menunggu</span>
                        @endif
                        <form method="POST" action="{{ route('admin.comments.approve', $comment) }}">
                            @csrf
                            @if ($comment->is_approved)
                                <input type="hidden" name="approve" value="0">
                                <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50">Tolak</button>
                            @else
                                <input type="hidden" name="approve" value="1">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">Setujui</button>
                            @endif
                        </form>
                        <form id="delete-form-{{ $comment->id }}" method="POST" action="{{ route('admin.comments.destroy', $comment) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-{{ $comment->id }}')"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <p class="mt-3 rounded-xl bg-slate-50 p-4 text-sm text-slate-700">{{ $comment->content }}</p>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-slate-400">Belum ada komentar.</div>
        @endforelse
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$comments" />
    </div>
</div>
@endsection
