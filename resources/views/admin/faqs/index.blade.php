@extends('admin.layouts.app')

@section('title', 'Kelola FAQ')

@section('content')
<x-admin.page-header title="Kelola FAQ" subtitle="Kelola Pertanyaan yang Sering Diajukan">
    <x-slot:button>
        <a href="{{ route('admin.faqs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah FAQ
        </a>
        <x-admin.delete-all :route="'admin.faqs.delete-all'" message="Semua FAQ akan dihapus secara permanen. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="space-y-3">
    @forelse ($faqs as $faq)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="font-semibold text-slate-800">{{ $faq->question }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ Str::limit($faq->answer, 120) }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$faq" route-prefix="admin.faqs" />
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">Belum ada FAQ.</div>
    @endforelse
</div>
<div class="mt-4">
    <x-admin.pagination :paginator="$faqs" />
</div>
@endsection
