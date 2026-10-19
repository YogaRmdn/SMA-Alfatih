@extends('sibel.layouts.app')

@section('title', 'Data Siswa')
@section('breadcrumb', 'Data Siswa')

@section('content')
<x-admin.page-header title="Data Siswa" subtitle="Kelola data siswa, kelas, dan akun login kuesioner">
    <x-slot:button>
        <a href="{{ route('sibel.students.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Siswa
        </a>
    </x-slot:button>
</x-admin.page-header>

{{-- Filter & pencarian --}}
<div class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <form method="GET" action="{{ route('sibel.students.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <x-admin.field label="Cari Siswa" name="q">
                <x-admin.input name="q" :value="request('q')" placeholder="Nama atau nomor induk..." />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-56">
            <x-admin.field label="Kelas" name="class_id">
                <x-admin.select name="class_id" :value="request('class_id')" placeholder="Semua kelas"
                    :options="$classes->mapWithKeys(fn ($class) => [$class->id => $class->label()])->all()" />
            </x-admin.field>
        </div>
        <div class="w-full sm:w-52">
            <x-admin.field label="Asal" name="source">
                <x-admin.select name="source" :value="request('source')" placeholder="Semua asal"
                    :options="['self' => 'Daftar mandiri', 'manual' => 'Dibuat admin']" />
            </x-admin.field>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('sibel.students.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>
</div>

{{-- Tabel siswa --}}
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-5 py-3">Nama</th>
                    <th class="px-5 py-3">No. Induk</th>
                    <th class="px-5 py-3">Kelas</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-center">Selesai Mengisi</th>
                    <th class="px-5 py-3">Akun Login</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="px-5 py-3">
                            <a href="{{ route('sibel.students.show', $student) }}" class="font-semibold text-slate-800 hover:text-emerald-700">
                                {{ $student->full_name }}
                            </a>
                            @if ($student->source === \App\Models\SibelStudent::SOURCE_SELF)
                                <span class="mt-1 block w-fit rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wide text-amber-700 ring-1 ring-inset ring-amber-200" title="Mendaftar sendiri lewat /sibel/daftar">
                                    Daftar Mandiri
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-slate-600">{{ $student->student_number ?: '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">{{ $student->schoolClass->name ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $student->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="text-sm font-bold text-slate-700">{{ $student->completed_attempts_count }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if ($student->user_id)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    Aktif
                                </span>
                            @else
                                <span class="text-xs text-slate-400">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('sibel.students.show', $student) }}" title="Detail"
                                   class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-sky-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="{{ route('sibel.students.edit', $student) }}" title="Edit"
                                   class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-amber-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form id="delete-student-{{ $student->id }}" method="POST" action="{{ route('sibel.students.destroy', $student) }}">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button" title="Hapus"
                                        onclick="confirmDelete('delete-student-{{ $student->id }}', 'Data siswa beserta riwayat pengisian dan hasilnya akan dihapus permanen. Lanjutkan?')"
                                        class="rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                            Belum ada data siswa. Tambahkan siswa terlebih dahulu untuk bisa mengisi kuesioner.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection
