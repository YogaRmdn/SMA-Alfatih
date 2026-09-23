@extends('admin.layouts.app')

@section('title', 'Pengaturan Website')

@section('content')
<x-admin.page-header title="Pengaturan Website" subtitle="Atur Identitas dan Konfigurasi Umum Website"></x-admin.page-header>

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
                <x-admin.input name="site_tagline" :value="$settings['site_tagline'] ?? ''" placeholder="Motto atau Tagline Sekolah" />
            </x-admin.field>

            <x-admin.field label="Deskripsi Website">
                <x-admin.textarea name="site_description" rows="3" :value="$settings['site_description'] ?? ''" placeholder="Deskripsi Singkat Sekolah" />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.image-upload name="logo" label="Logo" :path="$settings['logo'] ?? null" hint="JPG/PNG/WebP" />
                <x-admin.image-upload name="favicon" label="Favicon" :path="$settings['favicon'] ?? null" hint="JPG/PNG/WebP/ICO" />
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
            <h3 class="font-semibold text-slate-800">Teks Halaman Depan</h3>
            <p class="mt-0.5 text-xs text-slate-500">Judul besar, label, dan teks pendukung di beranda. Gunakan placeholder <code class="rounded bg-slate-100 px-1 font-mono text-[11px]">{site_name}</code> untuk menyisipkan nama sekolah.</p>
        </div>
        <div class="space-y-6 p-6">

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Hero & Pengumuman</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Badge Hero" name="hero_badge">
                        <x-admin.input name="hero_badge" :value="$settings['hero_badge'] ?? ''" placeholder="Sekolah Islam Terpadu & Tahfizh Al-Qur'an" />
                    </x-admin.field>
                    <x-admin.field label="Label Pengumuman (Ticker)">
                        <x-admin.input name="ticker_label" :value="$settings['ticker_label'] ?? ''" placeholder="Pengumuman" />
                    </x-admin.field>
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Profil / Tentang Kami</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Label Kecil">
                        <x-admin.input name="profil_eyebrow" :value="$settings['profil_eyebrow'] ?? ''" placeholder="Tentang Kami" />
                    </x-admin.field>
                    <x-admin.field label="Awalan Judul" hint="Nama sekolah otomatis ditampilkan setelahnya.">
                        <x-admin.input name="profil_title_prefix" :value="$settings['profil_title_prefix'] ?? ''" placeholder="Selamat Datang di" />
                    </x-admin.field>
                    <x-admin.field label="Badge Utama">
                        <x-admin.input name="profil_badge_1" :value="$settings['profil_badge_1'] ?? ''" placeholder="Al-Qur'an" />
                    </x-admin.field>
                    <x-admin.field label="Sub Badge">
                        <x-admin.input name="profil_badge_2" :value="$settings['profil_badge_2'] ?? ''" placeholder="Sebagai Jantung Kehidupan" />
                    </x-admin.field>
                </div>
                <div class="mt-5">
                    <x-admin.field label="Paragraf Profil">
                        <x-admin.textarea name="profil_text" rows="3" :value="$settings['profil_text'] ?? ''" placeholder="Deskripsi singkat tentang sekolah..." />
                    </x-admin.field>
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Sambutan Kepala Sekolah</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Label Kecil">
                        <x-admin.input name="sambutan_eyebrow" :value="$settings['sambutan_eyebrow'] ?? ''" placeholder="Sambutan" />
                    </x-admin.field>
                    <x-admin.field label="Judul">
                        <x-admin.input name="sambutan_title" :value="$settings['sambutan_title'] ?? ''" placeholder="Sambutan Kepala Sekolah" />
                    </x-admin.field>
                    <x-admin.field label="Kalimat Pengantar" hint="Nama sekolah otomatis ditampilkan setelahnya.">
                        <x-admin.input name="sambutan_intro" :value="$settings['sambutan_intro'] ?? ''" placeholder="Sepatah kata dari pimpinan untuk menyambut Anda di" />
                    </x-admin.field>
                    <x-admin.field label="Sub Judul">
                        <x-admin.input name="sambutan_heading" :value="$settings['sambutan_heading'] ?? ''" placeholder="Assalamu'alaikum & Selamat Datang" />
                    </x-admin.field>
                </div>
                <div class="mt-5 space-y-5">
                    <x-admin.field label="Paragraf 1">
                        <x-admin.textarea name="sambutan_text_1" rows="3" :value="$settings['sambutan_text_1'] ?? ''" placeholder="Sambutan... gunakan {site_name} untuk nama sekolah" />
                    </x-admin.field>
                    <x-admin.field label="Paragraf 2">
                        <x-admin.textarea name="sambutan_text_2" rows="3" :value="$settings['sambutan_text_2'] ?? ''" placeholder="Sambutan bagian kedua..." />
                    </x-admin.field>
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Statistik</p>
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ([1 => 'Tahun Berdiri', 2 => 'Siswa Aktif', 3 => 'Guru & Staff'] as $i => $defaultLabel)
                        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Statistik {{ $i }}</p>
                            <x-admin.field label="Label">
                                <x-admin.input name="stat_{{ $i }}_label" :value="$settings['stat_' . $i . '_label'] ?? ''" :placeholder="$defaultLabel" />
                            </x-admin.field>
                            <x-admin.field label="Nilai">
                                <x-admin.input name="stat_{{ $i }}_value" :value="$settings['stat_' . $i . '_value'] ?? ''" placeholder="Contoh: 2020, 160+, 29" />
                            </x-admin.field>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Program Unggulan & Ajakan</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <x-admin.field label="Label Kecil">
                        <x-admin.input name="program_eyebrow" :value="$settings['program_eyebrow'] ?? ''" placeholder="Keunggulan Kami" />
                    </x-admin.field>
                    <x-admin.field label="Label CTA (Ajakan)">
                        <x-admin.input name="cta_title" :value="$settings['cta_title'] ?? ''" placeholder="Siap Bergabung dengan Keluarga Besar Kami?" />
                    </x-admin.field>
                    <x-admin.field label="Awalan Judul Program">
                        <x-admin.input name="program_title_prefix" :value="$settings['program_title_prefix'] ?? ''" placeholder="Program" />
                    </x-admin.field>
                    <x-admin.field label="Kata Disorot Judul Program" hint="Bagian dengan warna gradasi.">
                        <x-admin.input name="program_title_highlight" :value="$settings['program_title_highlight'] ?? ''" placeholder="Unggulan" />
                    </x-admin.field>
                </div>
                <div class="mt-5 space-y-5">
                    <x-admin.field label="Sub Judul Program">
                        <x-admin.textarea name="program_subtitle" rows="2" :value="$settings['program_subtitle'] ?? ''" placeholder="Deskripsi singkat program unggulan..." />
                    </x-admin.field>
                    <x-admin.field label="Teks CTA">
                        <x-admin.textarea name="cta_text" rows="2" :value="$settings['cta_text'] ?? ''" placeholder="Kalimat ajakan mendaftar PPDB..." />
                    </x-admin.field>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                @php
                    $sections = [
                        'fasilitas' => ['Fasilitas Sekolah', 'Sarana & Prasarana', 'Fasilitas Sekolah'],
                        'ekskul' => ['Ekstrakurikuler', 'Pengembangan Diri', 'Ekstrakurikuler'],
                        'prestasi' => ['Galeri Prestasi', 'Dokumentasi', 'Galeri Prestasi'],
                        'galeri' => ['Galeri Kegiatan', 'Dokumentasi', 'Galeri Kegiatan'],
                        'testimoni' => ['Testimoni', 'Kata Mereka', 'Testimoni Alumni & Wali'],
                        'partner' => ['Mitra & Kerja Sama', null, 'Mitra & Kerja Sama'],
                        'kontak' => ['Kontak', 'Hubungi Kami', 'Kontak Sekolah'],
                        'berita' => ['Berita & Kegiatan', 'Informasi Terbaru', 'Berita & Kegiatan'],
                    ];
                @endphp
                @foreach ($sections as $prefix => $meta)
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                        <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $meta[0] }}</p>
                        <div class="space-y-4">
                            @if ($meta[1])
                                <x-admin.field label="Label Kecil">
                                    <x-admin.input name="{{ $prefix }}_eyebrow" :value="$settings[$prefix . '_eyebrow'] ?? ''" :placeholder="$meta[1]" />
                                </x-admin.field>
                            @endif
                            <x-admin.field label="Judul">
                                <x-admin.input name="{{ $prefix }}_title" :value="$settings[$prefix . '_title'] ?? ''" :placeholder="$meta[2]" />
                            </x-admin.field>
                            @if ($prefix !== 'testimoni')
                                <x-admin.field label="Sub Judul">
                                    <x-admin.textarea name="{{ $prefix }}_subtitle" rows="2" :value="$settings[$prefix . '_subtitle'] ?? ''" :placeholder="$meta[2]" />
                                </x-admin.field>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl border border-slate-100 bg-slate-50/60 p-5">
                <p class="mb-4 text-xs font-bold uppercase tracking-wide text-slate-500">Sumber Resmi Berita</p>
                <x-admin.field label="Teks Badge Sumber Resmi" hint="Tampil di samping judul Berita di beranda.">
                    <x-admin.input name="berita_badge" :value="$settings['berita_badge'] ?? ''" placeholder="Sumber resmi {site_name}" />
                </x-admin.field>
            </div>
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
                <x-admin.checkbox name="ppdb_open" label="Buka Pendaftaran PPDB" :checked="($settings['ppdb_open'] ?? '0') === '1'" />
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Pengaturan</button>
    </div>
</form>
@endsection
