@extends('admin.layouts.app')

@section('title', isset($staff) ? 'Edit Staff' : 'Tambah Staff')

@section('content')
<x-admin.page-header title="{{ isset($staff) ? 'Edit Staff' : 'Tambah Staff' }}">
    <x-slot:button>
        <a href="{{ route('admin.staffs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($staff) ? route('admin.staffs.update', $staff) : route('admin.staffs.store') }}" enctype="multipart/form-data" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @isset($staff) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Staff</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Lengkap" name="name" required>
                    <x-admin.input name="name" :value="$staff->name ?? ''" required />
                </x-admin.field>
                <x-admin.field label="NIP">
                    <x-admin.input name="nip" :value="$staff->nip ?? ''" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Jabatan" name="position" required>
                    <x-admin.input name="position" :value="$staff->position ?? ''" required placeholder="Contoh: Kepala Tata Usaha" />
                </x-admin.field>
                <x-admin.field label="Pendidikan">
                    <x-admin.input name="education" :value="$staff->education ?? ''" />
                </x-admin.field>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="No. Telepon">
                    <x-admin.input name="phone" :value="$staff->phone ?? ''" />
                </x-admin.field>
                <x-admin.field label="Email">
                    <x-admin.input type="email" name="email" :value="$staff->email ?? ''" />
                </x-admin.field>
            </div>

            <x-admin.image-upload name="photo" label="Foto Staff" :path="$staff->photo ?? null" hint="JPG/PNG/WebP" />
            <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$staff->is_active ?? true" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.staffs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
