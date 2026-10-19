<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menandai asal data siswa: 'self' = daftar mandiri lewat /sibel/daftar,
     * 'manual' = dibuat guru BK/admin lewat menu Data Siswa.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('source', 20)->default('manual');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
