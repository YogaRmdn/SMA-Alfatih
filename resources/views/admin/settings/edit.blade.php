@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<x-admin.page-header title="Pengaturan Website" subtitle="Atur identitas dan konfigurasi umum website"></x-admin.page-header>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Identitas Sekolah</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Nama Website" name="site_name" required>
                    <x-admin.input name="site_name" :value="$settings['site_name'] ?? ''" required />
                </x-admin.field>
                <x-admin.field label="Tahun Ajaran PPDB">
                    <x-admin.input name="ppdb_tahun_ajaran" :value="$settings['ppdb_tahun_ajaran'] ?? ''" placeholder="Contoh: 2026/2027" />
                </x-admin.field>
            </div>

            <x-admin.field label="Tagline">
                <x-admin.input name="site_tagline" :value="$settings['site_tagline'] ?? ''" placeholder="Motto atau tagline sekolah" />
            </x-admin.field>

            <x-admin.field label="Deskripsi Website">
                <x-admin.textarea name="site_description" rows="3" :value="$settings['site_description'] ?? ''" placeholder="Deskripsi singkat sekolah" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="logo" label="Logo" :path="$settings['logo'] ?? null" hint="JPG/PNG/WebP, maks 4 MB" />
                <x-admin.image-upload name="favicon" label="Favicon" :path="$settings['favicon'] ?? null" hint="JPG/PNG/WebP/ICO, maks 1 MB" />
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Kontak</h3>
        </div>
        <div class="space-y-5 p-6">
            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Alamat">
                    <x-admin.input name="address" :value="$settings['address'] ?? ''" />
                </x-admin.field>
                <x-admin.field label="Jam Operasional">
                    <x-admin.input name="operational_hours" :value="$settings['operational_hours'] ?? ''" placeholder="Contoh: Senin - Jumat, 07.00 - 15.00" />
                </x-admin.field>
                <x-admin.field label="Telepon">
                    <x-admin.input name="phone" :value="$settings['phone'] ?? ''" />
                </x-admin.field>
                <x-admin.field label="WhatsApp">
                    <x-admin.input name="whatsapp" :value="$settings['whatsapp'] ?? ''" placeholder="Contoh: 6281234567890" />
                </x-admin.field>
                <x-admin.field label="Email">
                    <x-admin.input type="email" name="email" :value="$settings['email'] ?? ''" />
                </x-admin.field>
            </div>
            <x-admin.field label="Embed Google Maps" hint="Paste kode iframe atau URL embed dari Google Maps.">
                <x-admin.textarea name="maps_embed" rows="3" :value="$settings['maps_embed'] ?? ''" placeholder="<iframe src=...></iframe>" />
            </x-admin.field>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Media Sosial</h3>
        </div>
        <div class="grid gap-5 p-6 md:grid-cols-2">
            <x-admin.field label="Instagram">
                <x-admin.input type="url" name="instagram" :value="$settings['instagram'] ?? ''" placeholder="https://instagram.com/..." />
            </x-admin.field>
            <x-admin.field label="Facebook">
                <x-admin.input type="url" name="facebook" :value="$settings['facebook'] ?? ''" placeholder="https://facebook.com/..." />
            </x-admin.field>
            <x-admin.field label="YouTube">
                <x-admin.input type="url" name="youtube" :value="$settings['youtube'] ?? ''" placeholder="https://youtube.com/..." />
            </x-admin.field>
            <x-admin.field label="Twitter / X">
                <x-admin.input type="url" name="twitter" :value="$settings['twitter'] ?? ''" placeholder="https://twitter.com/..." />
            </x-admin.field>
            <x-admin.field label="Link Video Profil" name="video_profile" hint="URL video profil (YouTube/Drive) untuk halaman profil.">
                <x-admin.input type="url" name="video_profile" :value="$settings['video_profile'] ?? ''" placeholder="https://youtube.com/embed/..." />
            </x-admin.field>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">SEO & PPDB</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Meta Keywords" hint="Pisahkan dengan koma.">
                <x-admin.input name="meta_keywords" :value="$settings['meta_keywords'] ?? ''" placeholder="sma, tahfizh, islam, pekanbaru, ..." />
            </x-admin.field>
            <div class="pt-1">
                <x-admin.checkbox name="ppdb_open" label="Buka pendaftaran PPDB" :checked="($settings['ppdb_open'] ?? '0') === '1'" />
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Pengaturan</button>
    </div>
</form>
@endsection
