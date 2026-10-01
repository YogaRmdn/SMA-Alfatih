<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gender guru, dipakai untuk mengurutkan tampilan: laki-laki dulu, baru
     * perempuan. Nilai sengaja 'L'/'P' (bukan boolean) supaya mudah dibaca
     * di kolom database dan tidak ambigu saat diurutkan.
     *
     * Kolom dibuat nullable supaya guru yang sudah ada sebelum kolom ini tidak
     * ikut terhapus atau gagal dimigrasikan. Baris yang gender-nya kosong
     * dit-display paling akhir, jadi front-end tidak pernah campur urutan.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('gender', 1)->nullable()->after('name');
        });

        $this->backfillKnownTeachers();
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }

    /**
     * Isi gender untuk nama guru yang memang sudah ada di data demo/seed,
     * supaya data lama langsung ikut terurut dengan benar setelah migrate.
     *
     * Hanya nama yang benar-benar ada di seeder yang disentuh. Nama lain
     * (guru yang ditambahkan admin) dibiarkan kosong dan diisi lewat form,
     * bukan ditebak dari nama depan karena nama Indonesia banyak yang unisex.
     */
    protected function backfillKnownTeachers(): void
    {
        $map = [
            'Ilham Dwitama Haeba, Ph.D.' => 'L',
            'Ust. Ahmad Fauzi, Lc.' => 'L',
            'Rina Marlina, S.Pd.' => 'P',
            'Dimas Prasetyo, S.Pd.' => 'L',
            'Lia Amalia, S.Pd.' => 'P',
            'Ust. Muhammad Ilham' => 'L',
            'Taufik Hidayat, S.Kom.' => 'L',
            'Yusuf Ramadhan, S.Or.' => 'L',
            'Andi Saputra, S.Pd.' => 'L',
            'Ustd. Hana Yusuf' => 'P',
            'Hj. Siti Aminah, S.Pd.' => 'P',
            'Dedi Firmansyah, S.Pd.' => 'L',
            'Rina Marlina, M.Pd.' => 'P',
            'Ahmad Fauzi, S.Si.' => 'L',
            'Rahma Yanti, S.Si.' => 'P',
            'Budi Santoso, S.Ag.' => 'L',
            'Zainal Abidin, S.Pd.' => 'L',
            'Nurul Hidayah, S.Pd.' => 'P',
            'Hendra Gunawan, S.Kom.' => 'L',
            'Dewi Anggraini, S.Psi.' => 'P',
            'Abdul Aziz, M.Pd.' => 'L',
        ];

        foreach ($map as $name => $gender) {
            DB::table('teachers')
                ->where('name', $name)
                ->whereNull('gender')
                ->update(['gender' => $gender]);
        }
    }
};
