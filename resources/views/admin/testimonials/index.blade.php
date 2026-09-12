@extends('admin.layouts.app')

@section('title', 'Kelola Testimoni')

@section('content')
<x-admin.page-header title="Kelola Testimoni" subtitle="Kelola testimoni alumni dan orang tua">
    <x-slot:button>
        <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Testimoni
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Isi Testimoni</th>
                    <th class="px-5 py-3">Status Tampil</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($testimonials as $testimonial)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">
                                    @if ($testimonial->photo)
                                        <img src="{{ asset('storage/'.$testimonial->photo) }}" class="h-full w-full object-cover" alt="{{ $testimonial->name }}" loading="lazy">
                                    @else
                                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                    @endif
                                </span>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $testimonial->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $testimonial->position }} {{ $testimonial->alumni_year ? '('.$testimonial->alumni_year.')' : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $testimonial->position ?? 'Alumni' }}</td>
                        <td class="max-w-md px-5 py-3 text-slate-500">{{ Str::limit($testimonial->content, 80) }}</td>
                        <td class="px-5 py-3">
                            @if ($testimonial->is_active)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$testimonial" route-prefix="admin.testimonials" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada testimoni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$testimonials" />
    </div>
</div>
@endsection
