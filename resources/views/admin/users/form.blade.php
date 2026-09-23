@extends('admin.layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<x-admin.page-header title="{{ isset($user) ? 'Edit User' : 'Tambah User' }}">
    <x-slot:button>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($user) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Akun</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Lengkap" name="name" required>
                    <x-admin.input name="name" :value="$user->name ?? ''" placeholder="Nama Lengkap User" required />
                </x-admin.field>
                <x-admin.field label="Role" name="role_id" required>
                    <x-admin.select name="role_id" :options="$roles->pluck('name', 'id')" :value="$user->role_id ?? null" placeholder="Pilih Role" required />
                </x-admin.field>
            </div>

            <x-admin.field label="Email" name="email" required>
                <x-admin.input type="email" name="email" :value="$user->email ?? ''" placeholder="email@contoh.com" required />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="{{ isset($user) ? 'Password Baru' : 'Password' }}" name="password" :required="!isset($user)" hint="{{ isset($user) ? 'Kosongkan jika tidak ingin mengganti password.' : 'Minimal 8 karakter.' }}">
                    <x-admin.input type="password" name="password" :required="!isset($user)" />
                </x-admin.field>
                <x-admin.field label="Konfirmasi Password" name="password_confirmation" :required="!isset($user)">
                    <x-admin.input type="password" name="password_confirmation" :required="!isset($user)" />
                </x-admin.field>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
