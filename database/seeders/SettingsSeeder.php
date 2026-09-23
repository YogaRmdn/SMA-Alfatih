<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'SMA IT Tahfizh Al-Fatih Pekanbaru',
            'site_tagline' => 'Mencetak Generasi Qur\'ani, Berprestasi & Berkarakter',
            'site_description' => 'Sekolah menengah atas Islam terpadu tahfizh Al-Qur\'an di Pekanbaru yang mencetak generasi unggul, berakhlak mulia, dan berprestasi.',
            'logo' => null,
            'favicon' => null,
            'address' => 'Jl. Rasamala, Komplek Perumahan Beringin Indah, Kel. Sidomulyo Timur, Kec. Marpoyan Damai, Kota Pekanbaru, Riau',
            'phone' => '081364356067',
            'whatsapp' => '6281364356067',
            'email' => 'smaiittahfizhalfatih@gmail.com',
            'instagram' => 'https://www.instagram.com/smaittahfizhalfatih?utm_source=ig_web_button_share_sheet&stkn=ZDNlZDc0MzIxNw==',
            'facebook' => 'https://facebook.com/smaitalfatih',
            'youtube' => 'https://youtube.com/@smaittahfizhal-fatih?si=hziCHzXtdjZSCGL0',
            'twitter' => null,
            'maps_embed' => null,
            'operational_hours' => 'Senin - Jumat: 07.00 - 16.00 WIB',
            'ppdb_open' => '1',
            'ppdb_tahun_ajaran' => '2026/2027',
            'meta_keywords' => 'SMA IT Tahfizh Al-Fatih, sekolah islam Pekanbaru, sekolah tahfizh, PPDB Pekanbaru',
            'video_profile' => null,

            // ===== Teks & Judul Halaman Depan =====
            'hero_badge' => 'Sekolah Islam Terpadu & Tahfizh Al-Qur\'an',
            'ticker_label' => 'Pengumuman',

            'profil_eyebrow' => 'Tentang Kami',
            'profil_title_prefix' => 'Selamat Datang di',
            'profil_badge_1' => 'Al-Qur\'an',
            'profil_badge_2' => 'Sebagai Jantung Kehidupan',
            'profil_text' => 'Kami berkomitmen mencetak generasi Qur\'ani yang berprestasi, berkarakter, dan siap menghadapi tantangan zaman. Dengan perpaduan kurikulum nasional dan pendidikan tahfizh Al-Qur\'an, setiap peserta didik dibina secara holistik — intelektual, spiritual, dan sosial.',

            'sambutan_eyebrow' => 'Sambutan',
            'sambutan_title' => 'Sambutan Kepala Sekolah',
            'sambutan_intro' => 'Sepatah kata dari pimpinan untuk menyambut Anda di',
            'sambutan_heading' => 'Assalamu\'alaikum & Selamat Datang',
            'sambutan_text_1' => 'Alhamdulillah, kami bersyukur ke hadirat Allah SWT atas segala nikmat dan karunia-Nya. Kami mengucapkan selamat datang di {site_name} — sekolah menengah atas Islam terpadu tahfizh Al-Qur\'an yang berkomitmen mencetak generasi Qur\'ani, berprestasi, dan berkarakter.',
            'sambutan_text_2' => 'Semoga melalui website ini, para orang tua, calon peserta didik, dan seluruh masyarakat dapat mengenal lebih dekat program, kegiatan, serta suasana kekeluargaan di sekolah kami. Mari bersama wujudkan masa depan cerah generasi penerus bangsa.',

            'program_eyebrow' => 'Keunggulan Kami',
            'program_title_prefix' => 'Program',
            'program_title_highlight' => 'Unggulan',
            'program_subtitle' => 'Program-program pilihan yang dirancang untuk mengembangkan potensi akademik dan karakter Islami peserta didik.',

            'cta_title' => 'Siap Bergabung dengan Keluarga Besar Kami?',
            'cta_text' => 'Daftarkan putra/putri Anda melalui PPDB dan wujudkan impian menjadi generasi hafizh yang berkarakter Islami.',

            'fasilitas_eyebrow' => 'Sarana & Prasarana',
            'fasilitas_title' => 'Fasilitas Sekolah',
            'fasilitas_subtitle' => 'Fasilitas lengkap untuk mendukung kenyamanan dan keberhasilan belajar peserta didik.',

            'ekskul_eyebrow' => 'Pengembangan Diri',
            'ekskul_title' => 'Ekstrakurikuler',
            'ekskul_subtitle' => 'Wadah pengembangan bakat, minat, dan soft skill peserta didik di luar jam pelajaran.',

            'prestasi_eyebrow' => 'Dokumentasi',
            'prestasi_title' => 'Galeri Prestasi',
            'prestasi_subtitle' => 'Momen kebanggaan siswa-siswi {site_name} dalam berbagai ajang perlombaan.',

            'berita_eyebrow' => 'Informasi Terbaru',
            'berita_title' => 'Berita & Kegiatan',
            'berita_badge' => 'Sumber resmi {site_name}',

            'galeri_eyebrow' => 'Dokumentasi',
            'galeri_title' => 'Galeri Kegiatan',
            'galeri_subtitle' => 'Momen-momen berharga dalam kehidupan sekolah kami.',

            'testimoni_eyebrow' => 'Kata Mereka',
            'testimoni_title' => 'Testimoni Alumni & Wali',

            'partner_title' => 'Mitra & Kerja Sama',
            'partner_subtitle' => 'Bersama membangun pendidikan berkualitas.',

            'kontak_eyebrow' => 'Hubungi Kami',
            'kontak_title' => 'Kontak Sekolah',
            'kontak_subtitle' => 'Silakan hubungi kami untuk informasi lebih lanjut mengenai PPDB dan kegiatan sekolah.',

            'stat_1_label' => 'Tahun Berdiri',
            'stat_1_value' => '2020',
            'stat_2_label' => 'Siswa Aktif',
            'stat_2_value' => '160+',
            'stat_3_label' => 'Guru & Staff',
            'stat_3_value' => '29',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
