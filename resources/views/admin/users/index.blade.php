@extends('admin.layouts.app')

@section('title', 'Kelola User')

@section('content')
<x-admin.page-header title="Kelola User" subtitle="Kelola Akun Pengguna Sistem">
    <x-slot:button>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah User
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama atau Email..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="role_id" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
            @if (request()->hasAny(['q', 'role_id']))
                <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="divide-y divide-slate-100 lg:hidden">
        @forelse ($users as $user)
            <div class="flex items-center gap-3 p-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">
                        {{ $user->name }}
                        @if ($user->id === auth()->id())
                            <span class="text-xs font-medium text-emerald-600">(Anda)</span>
                        @endif
                    </p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                        <span class="rounded-full px-2.5 py-0.5 text-[12px] font-semibold {{ $user->isSuperAdmin() ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' }}">{{ $user->role?->name ?? '-' }}</span>
                        <span class="text-[12px] text-slate-400">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-1.5">
                    <x-admin.actions :item="$user" route-prefix="admin.users" />
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Belum ada user.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Role</th>
                    <th class="px-5 py-3">Bergabung</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-50 text-sm font-bold text-emerald-700">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                                    @if ($user->id === auth()->id())
                                        <p class="text-xs text-emerald-600">Anda</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $user->isSuperAdmin() ? 'bg-violet-100 text-violet-700' : 'bg-sky-100 text-sky-700' }} px-2.5 py-0.5 text-xs font-semibold">{{ $user->role?->name ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <x-admin.actions :item="$user" route-prefix="admin.users" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$users" />
    </div>
</div>
@endsection
