@extends('admin.layouts.app')

@section('title', 'Ganti Password')

@section('content')
<div class="mx-auto max-w-xl">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-800">Ganti Password</h2>
            <p class="text-sm text-slate-500">Perbarui password akun Anda secara berkala untuk keamanan.</p>
        </div>

        <form method="POST" action="{{ route('password.update.custom') }}" class="space-y-5 p-6">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="current_password" :value="'Password Saat Ini'" />
                <x-text-input id="current_password" type="password" name="current_password" class="mt-1 block w-full" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="'Password Baru'" />
                <x-text-input id="password" type="password" name="password" class="mt-1 block w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="'Konfirmasi Password'" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" class="mt-1 block w-full" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
