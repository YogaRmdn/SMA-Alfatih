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
            'address' => 'Jl. Garuda Sakti KM 3, Panam, Kec. Tampan, Kota Pekanbaru, Riau 28291',
            'phone' => '(0761) 562206',
            'whatsapp' => '6281270000000',
            'email' => 'info@smaitalfatih.sch.id',
            'instagram' => 'https://instagram.com/smaitalfatih',
            'facebook' => 'https://facebook.com/smaitalfatih',
            'youtube' => 'https://youtube.com/@smaitalfatih',
            'twitter' => null,
            'maps_embed' => null,
            'operational_hours' => 'Senin - Jumat: 07.00 - 16.00 WIB',
            'ppdb_open' => '1',
            'ppdb_tahun_ajaran' => '2026/2027',
            'meta_keywords' => 'SMA IT Tahfizh Al-Fatih, sekolah islam Pekanbaru, sekolah tahfizh, PPDB Pekanbaru',
            'video_profile' => null,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
