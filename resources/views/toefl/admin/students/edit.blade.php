@extends('admin.layouts.app')

@section('title', 'Edit Peserta TOEFL')

@section('content')
<x-admin.page-header :title="'Edit: '.$student->full_name" subtitle="Ubah data peserta ujian TOEFL.">
    <x-slot:button>
        <a href="{{ route('toefl.admin.students.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid gap-6 lg:grid-cols-2">
    {{-- Data peserta --}}
    <form method="POST" action="{{ route('toefl.admin.students.update', $student) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4"><h3 class="font-semibold text-slate-800">Data Peserta</h3></div>
            <div class="space-y-5 p-6">
                <x-admin.field label="Nama Lengkap" name="full_name" required>
                    <x-admin.input name="full_name" :value="$student->full_name" required />
                </x-admin.field>

                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Nomor Induk" name="student_number">
                        <x-admin.input name="student_number" :value="$student->student_number" />
                    </x-admin.field>
                    <x-admin.field label="Kelas" name="class_name">
                        <x-admin.input name="class_name" :value="$student->class_name" />
                    </x-admin.field>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Email" name="email">
                        <x-admin.input type="email" name="email" :value="$student->email" />
                    </x-admin.field>
                    <x-admin.field label="Status" name="status" required>
                        <x-admin.select name="status" :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" :value="$student->status" required />
                    </x-admin.field>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('toefl.admin.students.index') }}" class="rounded-lg border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-lg bg-indigo-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-800">Simpan Perubahan</button>
        </div>
    </form>

    <form method="POST" action="{{ route('toefl.admin.students.destroy', $student) }}" class="text-right"
          onsubmit="return confirm('Hapus peserta beserta seluruh attemptnya?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50">Hapus Peserta</button>
    </form>

</div>
@endsection
