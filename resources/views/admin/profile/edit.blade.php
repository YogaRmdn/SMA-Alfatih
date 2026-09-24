@extends('admin.layouts.app')

@php
    $breadcrumbs = ['Profil'];
    $breadcrumb = 'Profil Saya';
@endphp

@section('title', 'Profil Saya')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    {{-- Profile hero --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="h-20 bg-gradient-to-r from-emerald-700 via-emerald-800 to-teal-900"></div>
        <div class="px-6 pb-6">
            <div class="-mt-6 flex flex-wrap items-end gap-4">
                <span class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-emerald-700 text-2xl font-extrabold text-white shadow-lg">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </span>
                <div class="pb-0.5">
                    <h1 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h1>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    {{ $user->role?->name ?? 'Tanpa Role' }}
                </span>
                @if ($user->email_verified_at)
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                        Email Terverifikasi
                    </span>
                @else
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-medium text-amber-700">
                        Email Belum Diverifikasi
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Informasi Profil --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-800">Informasi Profil</h2>
            <p class="text-sm text-slate-500">Perbarui nama dan alamat email akun Anda.</p>
        </div>
        <div class="p-6">
            @include('admin.profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Ganti Password --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-800">Ganti Password</h2>
            <p class="text-sm text-slate-500">Pastikan akun menggunakan password yang panjang dan acak agar tetap aman.</p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('password.update.custom') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <x-admin.field label="Password Saat Ini" name="current_password" required>
                    <x-admin.input name="current_password" type="password" required autocomplete="current-password" placeholder="Masukkan password saat ini" />
                </x-admin.field>

                <x-admin.field label="Password Baru" name="password" required>
                    <x-admin.input name="password" type="password" required autocomplete="new-password" placeholder="Masukkan password baru" />
                </x-admin.field>

                <x-admin.field label="Konfirmasi Password Baru" name="password_confirmation" required>
                    <x-admin.input name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi password baru" />
                </x-admin.field>

                <div class="flex items-center justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/30 transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.523 9.348A4.522 4.522 0 0113.5 11H11v2.5A1.5 1.5 0 019.5 15H9v2h2a1.5 1.5 0 001.5-1.5V14h.5a4.5 4.5 0 004.5-4.5 4.04 4.04 0 00-.065-.73" /></svg>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Zona Berbahaya --}}
    <div class="rounded-2xl border border-red-200 bg-white shadow-sm">
        <div class="border-b border-red-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-red-700">Hapus Akun</h2>
            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        <div class="p-6">
            @include('admin.profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection