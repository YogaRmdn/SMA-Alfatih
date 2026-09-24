@extends('admin.layouts.app')

@section('title', 'Log Aktivitas')

@php
    $badges = [
        'login' => ['label' => 'Login', 'class' => 'bg-emerald-100 text-emerald-700'],
        'logout' => ['label' => 'Logout', 'class' => 'bg-slate-100 text-slate-600'],
        'login_failed' => ['label' => 'Login Gagal', 'class' => 'bg-red-100 text-red-700'],
    ];
@endphp

@section('content')
<x-admin.page-header title="Log Aktivitas" subtitle="Riwayat login, logout, dan percobaan login pengguna sistem (khusus Super Admin)">
    <x-slot:button>
        <div class="grid gap-2 sm:flex">
            <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-200 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Muat Ulang
            </a>
            <x-admin.delete-all route="admin.activity-logs.clear" label="Bersihkan Semua" message="Semua log aktivitas akan dihapus permanen dan tidak dapat dikembalikan. Lanjutkan?" />
        </div>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="grid gap-3 lg:grid-cols-12 lg:items-center">
            <div class="relative lg:col-span-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, deskripsi, atau IP..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <div class="lg:col-span-2">
                <select name="action" class="w-full rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="all">Semua Aksi</option>
                    @foreach ($badges as $key => $badge)
                        <option value="{{ $key }}" {{ request('action', 'all') == $key ? 'selected' : '' }}>{{ $badge['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" title="Dari tanggal"
                       class="w-full rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="lg:col-span-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" title="Sampai tanggal"
                       class="w-full rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div class="flex items-center gap-2 lg:col-span-2">
                <button type="submit" class="w-full rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
                @if (request()->hasAny(['q', 'action', 'date_from', 'date_to']))
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($logs as $log)
            @php
                $badge = $badges[$log->action] ?? ['label' => $log->action, 'class' => 'bg-slate-100 text-slate-600'];
                $userLabel = $log->user_id ? $log->user->name : ($log->action === 'login_failed' ? 'Pengguna tidak dikenal' : '-');
                $userSub = $log->user_id ? $log->user->email : $log->ip_address;
            @endphp
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">{{ $log->user_id ? mb_strtoupper(mb_substr($log->user->name, 0, 1)) : '?' }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $userLabel }}</p>
                            <span class="rounded-full px-2.5 py-0.5 text-[12px] font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </div>
                        <p class="truncate text-xs text-slate-500">{{ $userSub }}</p>
                        @if ($log->description)
                            <p class="mt-1 text-xs text-slate-600">{{ $log->description }}</p>
                        @endif
                        <p class="mt-1 text-[12px] text-slate-400">IP: {{ $log->ip_address ?? '-' }} &middot; {{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</p>
                    </div>
                    <form method="POST" id="delete-log-{{ $log->id }}" action="{{ route('admin.activity-logs.destroy', $log) }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('delete-log-{{ $log->id }}', 'Hapus log ini?')" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada log aktivitas.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Pengguna</th>
                    <th class="px-5 py-3">Aksi</th>
                    <th class="px-5 py-3">Detail</th>
                    <th class="px-5 py-3">IP Address</th>
                    <th class="px-5 py-3">Waktu</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($logs as $log)
                    @php
                        $badge = $badges[$log->action] ?? ['label' => $log->action, 'class' => 'bg-slate-100 text-slate-600'];
                    @endphp
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">{{ $log->user_id ? mb_strtoupper(mb_substr($log->user->name, 0, 1)) : '?' }}</span>
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800">{{ $log->user_id ? $log->user->name : ($log->action === 'login_failed' ? 'Pengguna tidak dikenal' : '-') }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $log->user_id ? $log->user->email : '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td class="max-w-xs px-5 py-3 text-slate-600">{{ $log->description }}</td>
                        <td class="px-5 py-3">
                            <span class="font-mono text-xs text-slate-500">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" id="delete-log-{{ $log->id }}" action="{{ route('admin.activity-logs.destroy', $log) }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-log-{{ $log->id }}', 'Hapus log ini?')" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-600" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">Belum ada log aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$logs" />
    </div>
</div>
@endsection