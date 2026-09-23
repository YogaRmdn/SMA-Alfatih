@extends('admin.layouts.app')

@section('title', 'Data Pendaftar PPDB')

@section('content')
<x-admin.page-header title="Data Pendaftar PPDB" subtitle="Kelola Seluruh Pendaftaran PPDB Online">
    <x-slot:button>
        <x-admin.delete-all :route="'admin.ppdb.delete-all'" message="Semua data pendaftar PPDB beserta dokumennya akan dihapus permanen dan tidak dapat dikembalikan. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
    <x-admin.stat-card label="Menunggu" :value="$counts['pending']" color="amber"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" :href="route('admin.ppdb.index', ['status' => 'pending'])" />
    <x-admin.stat-card label="Terverifikasi" :value="$counts['verified']" color="sky"
        icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" :href="route('admin.ppdb.index', ['status' => 'verified'])" />
    <x-admin.stat-card label="Lulus Adm." :value="$counts['lulus_administrasi']" color="violet"
        icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" :href="route('admin.ppdb.index', ['status' => 'lulus_administrasi'])" />
    <x-admin.stat-card label="Diterima" :value="$counts['accepted']" color="emerald"
        icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" :href="route('admin.ppdb.index', ['status' => 'accepted'])" />
    <x-admin.stat-card label="Ditolak" :value="$counts['rejected']" color="rose"
        icon="M6 18L18 6M6 6l12 12" :href="route('admin.ppdb.index', ['status' => 'rejected'])" class="col-span-2 sm:col-span-1" />
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 p-4">
        <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
            <div class="relative flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Nama / No. Registrasi / NISN..."
                       class="w-full rounded-lg border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <svg class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <select name="status" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                <option value="lulus_administrasi" {{ request('status') === 'lulus_administrasi' ? 'selected' : '' }}>Lulus Administrasi</option>
                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <select name="gender" class="rounded-lg border-slate-300 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua Jenis Kelamin</option>
                <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            <input type="text" name="academic_year" value="{{ request('academic_year') }}" placeholder="Tahun Ajaran"
                   class="w-full rounded-lg border-slate-300 py-2 px-3 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500 md:w-40">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">Filter</button>
            @if (request()->hasAny(['q', 'status', 'gender', 'academic_year']))
                <a href="{{ route('admin.ppdb.index') }}" class="text-sm text-slate-500 hover:text-emerald-700">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-max text-sm">
            <thead>
                <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">No. Registrasi</th>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">JK</th>
                    <th class="px-5 py-3">NISN</th>
                    <th class="px-5 py-3">Asal Sekolah</th>
                    <th class="px-5 py-3">Tahun Ajaran</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Daftar</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($registrations as $item)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-xs font-semibold text-slate-700">{{ $item->registration_number }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.ppdb.show', $item) }}" class="font-medium text-slate-800 hover:text-emerald-700">{{ $item->full_name }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->gender === 'L' ? 'L' : 'P' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->nisn ?? '-' }}</td>
                        <td class="max-w-[180px] truncate px-5 py-3 text-slate-600">{{ $item->origin_school ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->academic_year ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $item->statusColor() }}">{{ $item->statusLabel() }}</span>
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.ppdb.show', $item) }}" title="Lihat"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-sky-300 hover:bg-sky-50 hover:text-sky-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <form id="delete-form-{{ $item->id }}" method="POST" action="{{ route('admin.ppdb.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-form-{{ $item->id }}')" title="Hapus"
                                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center text-slate-400">Belum ada pendaftar PPDB.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-100 p-4">
        <x-admin.pagination :paginator="$registrations" />
    </div>
</div>
@endsection
