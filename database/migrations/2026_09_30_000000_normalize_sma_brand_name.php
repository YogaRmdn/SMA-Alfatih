<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejaan nama sekolah diseragamkan dari "SMA IT Tahfizh Al-Fatih" menjadi
     * "SMAIT Tahfizh Al-Fatih" (tanpa spasi) di seluruh nilai setting.
     *
     * Migration 2026_09_26_010000 sengaja TIDAK diubah agar riwayat migrasi
     * tetap immutable; normalisasi dilakukan di sini supaya hasilnya sama
     * untuk instalasi baru maupun database production yang sudah terlanjur
     * terisi nilai lama.
     */
    private const FROM = 'SMA IT Tahfizh Al-Fatih';

    private const TO = 'SMAIT Tahfizh Al-Fatih';

    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $this->replaceInEverySetting(self::FROM, self::TO);

        // Pastikan site_name selalu ada walau tabel sebelumnya kosong.
        if (DB::table('settings')->where('key', 'site_name')->doesntExist()) {
            $now = now();

            DB::table('settings')->insert([
                'key' => 'site_name',
                'value' => 'SMAIT Tahfizh Al-Fatih Pekanbaru',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->keepBothSpellingsInKeywords();

        Cache::forget('site_settings');
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $this->replaceInEverySetting(self::TO, self::FROM);

        // Sederhanakan kembali "SMA IT ..., SMA IT ..." yang muncul karena
        // up() sengaja menyimpan dua ejaan di meta_keywords.
        $duplicate = self::FROM.', '.self::FROM;
        $simple = self::FROM;

        DB::table('settings')
            ->where('value', 'like', '%'.$duplicate.'%')
            ->update([
                'value' => DB::raw("REPLACE(value, '{$duplicate}', '{$simple}')"),
                'updated_at' => now(),
            ]);

        Cache::forget('site_settings');
    }

    private function replaceInEverySetting(string $from, string $to): void
    {
        DB::table('settings')
            ->where('value', 'like', '%'.$from.'%')
            ->update([
                'value' => DB::raw("REPLACE(value, '{$from}', '{$to}')"),
                'updated_at' => now(),
            ]);
    }

    /**
     * Mesin pencari menerima kedua ejaan, jadi meta_keywords
     * menyimpan "SMAIT ..." dan "SMA IT ..." sekaligus.
     */
    private function keepBothSpellingsInKeywords(): void
    {
        $keywords = DB::table('settings')->where('key', 'meta_keywords')->value('value');

        if ($keywords === null || str_contains($keywords, self::FROM)) {
            return;
        }

        DB::table('settings')
            ->where('key', 'meta_keywords')
            ->update([
                'value' => self::FROM.' Pekanbaru, '.$keywords,
                'updated_at' => now(),
            ]);
    }
};
