<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan kunci setting SEO baru HANYA bila belum ada.
     *
     * Sengaja memakai insert-if-missing (bukan Setting::set) supaya hasil
     * kustomisasi admin di panel tidak tertimpa saat migration dijalankan
     * di server production.
     */
    public function up(): void
    {
        $defaults = [
            'meta_keywords' => 'SMA IT Tahfizh Al-Fatih Pekanbaru, SMA tahfizh Pekanbaru, sekolah tahfizh Pekanbaru, sekolah islam terpadu Pekanbaru, SMAIT Al-Fatih, tahfizh Al-Fatih, sekolah islam Riau, PPDB Pekanbaru, PPDB SMA IT Tahfizh Al-Fatih, sekolah Quran Pekanbaru',
            'seo_home_title' => 'SMA IT Tahfizh Al-Fatih Pekanbaru — Sekolah Islam Terpadu & Tahfizh Al-Qur\'an',
            'seo_home_description' => 'SMA IT Tahfizh Al-Fatih Pekanbaru adalah sekolah islam terpadu & tahfizh Al-Qur\'an di Pekanbaru, Riau. PPDB tahun ajaran 2026/2027 sedang dibuka. Daftar sekarang!',
            'meta_geo_region' => 'ID-RI',
            'meta_geo_placename' => 'Pekanbaru',
            'meta_geo_position' => '0.507073;101.447779',
        ];

        $now = now();

        foreach ($defaults as $key => $value) {
            DB::table('settings')->insertOrIgnore([
                'key' => $key,
                'value' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Cache::forget('site_settings');
    }

    public function down(): void
    {
        DB::table('settings')
            ->whereIn('key', [
                'seo_home_title',
                'seo_home_description',
                'meta_geo_region',
                'meta_geo_placename',
                'meta_geo_position',
            ])
            ->delete();

        Cache::forget('site_settings');
    }
};
