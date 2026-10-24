@extends('admin.layouts.app')

@section('title', 'Peserta TOEFL')

@section('content')
<x-admin.page-header title="Data Peserta TOEFL" subtitle="Identitas peserta tersimpan otomatis saat mereka masuk portal, atau didaftarkan lebih dulu di sini. Kode akses dikelola di halaman ujian.">
    <x-slot:button>
        <a href="{{ route('toefl.admin.students.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Peserta
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="GET" action="{{ route('toefl.admin.students.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, nomor, atau kelas…"
        class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Cari</button>
</form>

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
            <tr>
                <th class="px-5 py-3">Nama</th>
                <th class="px-5 py-3">Nomor</th>
                <th class="px-5 py-3">Kelas</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-center">Attempt</th>
                <th class="px-5 py-3 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($students as $student)
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3">
                        <a href="{{ route('toefl.admin.students.edit', $student) }}" class="font-semibold text-slate-800 hover:text-indigo-700">{{ $student->full_name }}</a>
                        @if ($student->email)<p class="text-xs text-slate-500">{{ $student->email }}</p>@endif
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-slate-600">{{ $student->student_number ?: '—' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $student->class_name ?: '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $student->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                            {{ $student->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center tabular-nums">{{ $student->attempts_count }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('toefl.admin.students.edit', $student) }}" class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-500">Belum ada peserta terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $students->links() }}</div>
@endsection
