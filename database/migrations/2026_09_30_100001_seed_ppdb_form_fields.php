<?php

use Database\Seeders\PpdbFormFieldSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Isi konfigurasi formulir PPDB awal supaya halaman pendaftaran tetap
     * berfungsi setelah `migrate`, tanpa harus menjalankan seeder terpisah.
     *
     * Seeder memakai insert-if-missing per key, jadi kustomisasi admin yang
     * sudah ada tidak tertimpa.
     */
    public function up(): void
    {
        (new PpdbFormFieldSeeder)->run();
    }

    public function down(): void
    {
        DB::table('ppdb_form_fields')->delete();
    }
};
