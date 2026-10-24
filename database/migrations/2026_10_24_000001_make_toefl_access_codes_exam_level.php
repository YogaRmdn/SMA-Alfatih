<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kode akses TOEFL menjadi LEVEL UJIAN (bukan milik satu akun siswa).
 *
 * Modul TOEFL kini berjalan TANPA registrasi & login: peserta mengisi
 * nama + kelas di portal, lalu memasukkan kode akses ujian. Karena identitas
 * baru diketahui saat peserta masuk, kode tidak bisa terikat ke satu siswa
 * sejak awal — maka `student_id` dibuat nullable (baris lama tetap valid).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('toefl_access_codes', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke NOT NULL: kode tanpa pemilik tidak bisa dipetakan lagi.
        DB::table('toefl_access_codes')->whereNull('student_id')->delete();

        Schema::table('toefl_access_codes', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable(false)->change();
        });
    }
};
