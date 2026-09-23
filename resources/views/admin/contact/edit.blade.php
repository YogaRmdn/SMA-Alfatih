@extends('admin.layouts.app')

@section('title', 'Pengaturan Kontak')

@section('content')
<x-admin.page-header title="Pengaturan Kontak" subtitle="Kelola Informasi Kontak dan Lokasi Sekolah"></x-admin.page-header>

<form method="POST" action="{{ route('admin.contact.update', $contact) }}" class="mx-auto max-w-3xl space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Informasi Kontak</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Alamat">
                <x-admin.input name="address" :value="$contact->address ?? ''" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Telepon">
                    <x-admin.input name="phone" :value="$contact->phone ?? ''" />
                </x-admin.field>
                <x-admin.field label="WhatsApp">
                    <x-admin.input name="whatsapp" :value="$contact->whatsapp ?? ''" placeholder="Contoh: 6281234567890" />
                </x-admin.field>
                <x-admin.field label="Email">
                    <x-admin.input type="email" name="email" :value="$contact->email ?? ''" />
                </x-admin.field>
                <x-admin.field label="Jam Operasional">
                    <x-admin.input name="operational_hours" :value="$contact->operational_hours ?? ''" placeholder="Contoh: Senin - Jumat, 07.00 - 15.00" />
                </x-admin.field>
                <x-admin.field label="Latitude">
                    <x-admin.input name="latitude" :value="$contact->latitude ?? ''" placeholder="Contoh: 0.5071" />
                </x-admin.field>
                <x-admin.field label="Longitude">
                    <x-admin.input name="longitude" :value="$contact->longitude ?? ''" placeholder="Contoh: 101.4478" />
                </x-admin.field>
            </div>

            <x-admin.field label="Embed Google Maps" hint="Paste kode iframe atau URL embed dari Google Maps.">
                <x-admin.textarea name="maps_embed" rows="4" :value="$contact->maps_embed ?? ''" placeholder="<iframe src=...></iframe>" />
            </x-admin.field>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Kontak</button>
    </div>
</form>
@endsection
