<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dukungan metode penilaian Skala Likert untuk SIBEL.
 *
 * - `questionnaires.scoring_method`: "choice" (pilihan tunggal A/B/C — lama)
 *   atau "likert" (skala 4 tingkat: Sangat Sesuai s.d. Tidak Sesuai).
 * - `questions.category_code`: kategori indikator (visual/auditori/kinestetik)
 *   yang dimiliki setiap pernyataan pada kuesioner Likert.
 * - `options.value`: nilai numerik opsi (1-4) untuk skoring Likert.
 *
 * Kolom bersifat nullable/default sehingga kuesioner lama & seluruh hasil
 * historis tetap kompatibel tanpa perubahan data.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->string('scoring_method', 20)->default('choice')->after('version');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('category_code', 20)->nullable()->after('question_text');
        });

        Schema::table('options', function (Blueprint $table) {
            // Pada kuesioner Likert, kategori indikator melekat di pernyataan
            // (questions.category_code) sehingga opsi tidak lagi membawanya.
            $table->string('category_code', 20)->nullable()->change();
            $table->unsignedTinyInteger('value')->nullable()->after('category_code');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn('scoring_method');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('category_code');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->string('category_code', 20)->nullable(false)->change();
            $table->dropColumn('value');
        });
    }
};
