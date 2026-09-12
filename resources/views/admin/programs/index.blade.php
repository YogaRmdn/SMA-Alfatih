@extends('admin.layouts.app')

@section('title', 'Kelola Program')

@section('content')
<x-admin.page-header title="Kelola Program" subtitle="Kelola program unggulan, tahfizh, akademik, dan IT">
    <x-slot:button>
        <a href="{{ route('admin.programs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Program
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari program..."
                   class="rounded-lg border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 md:w-64">
            <select name="type" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Tipe</option>
                <option value="unggulan" {{ request('type') === 'unggulan' ? 'selected' : '' }}>Unggulan</option>
                <option value="tahfizh" {{ request('type') === 'tahfizh' ? 'selected' : '' }}>Tahfizh</option>
                <option value="akademik" {{ request('type') === 'akademik' ? 'selected' : '' }}>Akademik</option>
                <option value="it" {{ request('type') === 'it' ? 'selected' : '' }}>IT</option>
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Program</th>
                    <th class="px-5 py-3">Tipe</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($programs as $program)
                    <tr class="transition hover:bg-slate-50">
                        <td class="max-w-md px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if ($program->image)
                                    <img src="{{ asset('storage/'.$program->image) }}" class="h-10 w-14 rounded-lg object-cover" alt="{{ $program->name }}" loading="lazy">
                                @endif
                                <a href="{{ route('admin.programs.edit', $program) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $program->name }}</a>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $typeColors = ['unggulan' => 'bg-amber-100 text-amber-700', 'tahfizh' => 'bg-emerald-100 text-emerald-700', 'akademik' => 'bg-sky-100 text-sky-700', 'it' => 'bg-violet-100 text-violet-700'];
                            @endphp
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $typeColors[$program->type] ?? 'bg-slate-100 text-slate-600' }}">{{ $program->type }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($program->is_active)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$program" route-prefix="admin.programs" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-400">Belum ada program.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$programs" />
    </div>
</div>
@endsection
