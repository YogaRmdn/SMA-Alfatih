<?php

namespace Database\Seeders;

use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelOption;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Services\LearningStyleScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Data dummy lengkap untuk dashboard admin SIBEL (pengembangan lokal).
 *
 * TIDAK dipanggil otomatis oleh DatabaseSeeder (konsisten dengan seeder demo
 * lainnya). Jalankan manual:
 *
 *   php artisan db:seed --class=SibelDummySeeder
 *
 * Isinya:
 *  - 6 kelas + 30 siswa aktif + 2 siswa tidak aktif
 *  - Sesi berjalan memakai kuesioner SKALA LIKERT (penilaian 4-1 per
 *    pernyataan) — kuesioner pilihan A/B/C ditutup sebagai arsip hasil
 *  - 3 kuesioner berbeda status (Sesi Dibuka / Ditutup / Draft) — soal
 *    dikloning dari kuesioner bawaan SibelSeeder agar penilaian tetap valid
 *  - Sesi selesai lampau (terpublikasi) + sesi terkini (campuran
 *    terpublikasi/belum + catatan guru BK) + sesi berjalan + siswa belum mengisi
 *  - Beberapa hasil Likert terbaru supaya laporan punya data sesi aktif;
 *    mayoritas siswa sengaja dibiarkan belum mengisi agar portal bisa dicoba
 *
 * Seluruh skor dihitung lewat LearningStyleScoringService (server-side),
 * sehingga data dummy selalu konsisten dengan aturan penilaian aplikasi.
 * Seeder idempoten: aman dijalankan berulang.
 */
class SibelDummySeeder extends Seeder
{
    /**
     * Pola jawaban 15 karakter untuk 15 pertanyaan (A=Visual, B=Auditori,
     * C=Kinestetik). Campuran dominan dan seri untuk menguji tampilan grafik,
     * daftar hasil, dan rekap kelas.
     */
    protected const PATTERNS = [
        'AAAABBBBCCABCCA', // A6 B5 C4  -> Visual
        'BBBBBBBBBBBBBBB', // B15       -> Auditori
        'CCCCCCCCCCCCCCC', // C15       -> Kinestetik
        'AAABBBCCCABCABC', // A5 B5 C5  -> seri tiga kategori
        'AAACCCBBBAAACCC', // A6 B3 C6  -> seri Visual & Kinestetik
        'ABCCABCCABCCABC', // A4 B4 C7  -> Kinestetik
        'CCCCCCCCCCBBBBB', // C10 B5    -> Kinestetik
        'AAABAAABAAABAAA', // A12 B3    -> Visual
        'BBBBBBBAAAAACCC', // B7 A5 C3  -> Auditori
        'ABCABCABCABCABC', // A5 B5 C5  -> seri tiga kategori
        'AABBAABBAABBAAB', // A8 B7     -> Visual
        'BBBCCCBBBAAACCC', // B6 C6 A3  -> seri Auditori & Kinestetik
        'CACACACACACACAC', // C8 A7     -> Kinestetik
        'BABABABABABABAB', // B8 A7     -> Auditori
        'CCCBBBAAACCCBBB', // C6 B6 A3  -> seri Auditori & Kinestetik
        'AAABBBCCCAAABBB', // A6 B6 C3  -> seri Visual & Auditori
        'CCACACACACACACA', // C8 A7     -> Kinestetik
    ];

    /** Nama siswa aktif (30) — nama umum siswa Indonesia. */
    protected const STUDENTS = [
        'Aisyah Rahmawati',
        'Ahmad Fauzan',
        'Bima Saputra',
        'Bella Safira',
        'Citra Lestari',
        'Chandra Kirana',
        'Daffa Pratama',
        'Dinda Ayu Larasati',
        'Elina Maharani',
        'Eko Prasetyo',
        'Fajar Nugroho',
        'Fitri Handayani',
        'Ghina Salsabila',
        'Gilang Ramadhan',
        'Hafiz Ramadhan',
        'Hana Maulida',
        'Intan Permata',
        'Ilham Syahputra',
        'Joko Susilo',
        'Khadijah Amalia',
        'Lukman Hakim',
        'Laila Nur Azizah',
        'Melati Anggraini',
        'Muhammad Rizky Ardiansyah',
        'Nadia Safitri',
        'Naufal Hakiki',
        'Putri Ramadhani',
        'Qurrota Ayun',
        'Rizky Firmansyah',
        'Salsabila Nur Aini',
    ];

    /** Siswa tidak aktif (contoh data alumni/pindah) — tidak dihitung dashboard. */
    protected const INACTIVE_STUDENTS = [
        'Miftahul Jannah',
        'Rangga Aditya',
    ];

    protected const CLASSES = [
        ['name' => 'X-A', 'academic_year' => '2026/2027'],
        ['name' => 'X-B', 'academic_year' => '2026/2027'],
        ['name' => 'X-C', 'academic_year' => '2026/2027'],
        ['name' => 'XI-IPA-1', 'academic_year' => '2026/2027'],
        ['name' => 'XI-IPA-2', 'academic_year' => '2026/2027'],
        ['name' => 'XI-IPS-1', 'academic_year' => '2026/2027'],
    ];

    /** Catatan guru BK untuk sebagian hasil (ditampilkan di detail hasil). */
    protected const TEACHER_NOTES = [
        'Konsultasi sudah dilakukan — siswa diberi strategi belajar sesuai hasil kuesioner.',
        'Perlu pendampingan lanjutan terkait kebiasaan belajar di rumah.',
        'Orang tua sudah dihubungi, hasil dibahas pada pertemuan berikutnya.',
        'Siswa menunjukkan perkembangan positif sejak asesmen awal.',
        'Hasil dibahas bersama wali kelas; siswa diminta mencoba strategi baru.',
    ];

    public function run(): void
    {
        // Pastikan kuesioner bawaan (15 pertanyaan) + versi Skala Likert
        // tersedia lebih dulu.
        $this->call(SibelSeeder::class);
        $this->call(SibelLikertSeeder::class);

        $primary = SibelQuestionnaire::where('title', 'Kuesioner Kecenderungan Gaya Belajar Siswa')
            ->firstOrFail();

        // Sesi berjalan memakai kuesioner Skala Likert (nilai opsi 4-1 per
        // pernyataan). Portal hanya menyajikan SATU kuesioner aktif — yang
        // terbaru di antara yang berstatus active — jadi kuesioner pilihan
        // A/B/C ditutup sebagai arsip (riwayat hasil & skornya tetap utuh).
        $likert = SibelQuestionnaire::where('scoring_method', SibelQuestionnaire::SCORING_METHOD_LIKERT)
            ->orderBy('id')
            ->first();

        if ($likert) {
            $likert->update([
                'status' => SibelQuestionnaire::STATUS_ACTIVE,
                'needs_verification' => false,
            ]);
        }

        if ($primary->isActive()) {
            $primary->update(['status' => SibelQuestionnaire::STATUS_CLOSED]);
        }

        $closed = $this->questionnaire(
            title: 'Kuesioner Gaya Belajar — Semester Ganjil 2025/2026',
            instructions: 'Sesi pengisian semester ganjil 2025/2026. Sesi sudah ditutup dan hasil telah dipublikasikan kepada siswa.',
            version: '1.0',
            status: SibelQuestionnaire::STATUS_CLOSED,
            needsVerification: false,
            source: $primary,
        );

        $draft = $this->questionnaire(
            title: 'Kuesioner Gaya Belajar — Semester Genap 2026/2027',
            instructions: 'Draf pengukuran ulang semester genap 2026/2027. Belum dibuka untuk siswa — menunggu verifikasi instrumen oleh guru BK.',
            version: '0.9',
            status: SibelQuestionnaire::STATUS_DRAFT,
            needsVerification: true,
            source: $primary,
        );

        $classes = collect(self::CLASSES)->map(
            fn (array $class) => SibelClass::updateOrCreate(
                ['name' => $class['name'], 'academic_year' => $class['academic_year']],
                $class
            )
        );

        $activeStudents = $this->students(self::STUDENTS, $classes, SibelStudent::STATUS_ACTIVE, 1);
        $inactiveStudents = $this->students(self::INACTIVE_STUDENTS, $classes, SibelStudent::STATUS_INACTIVE, 90);

        $scoring = app(LearningStyleScoringService::class);
        $patterns = array_values(self::PATTERNS);

        foreach ($activeStudents->values() as $index => $student) {
            $slot = $index % 10;

            // Slot 9: siswa belum mengisi apa pun (menambah angka "Belum Mengisi").
            if ($slot === 9) {
                continue;
            }

            // Sesi lampau pada kuesioner yang sudah ditutup (hampir semua siswa).
            $this->completedAttempt($student, $closed, $patterns[($index + 3) % count($patterns)], [
                'daysAgo' => 105 + ($index % 25),
                'publish' => true,
                'teacherNote' => null,
            ], $scoring);

            // Slot 7: sesi berjalan di kuesioner utama (progres sebagian).
            if ($slot === 7) {
                $this->inProgressAttempt($student, $primary, $patterns[$index % count($patterns)]);

                continue;
            }

            // Sisanya: sesi selesai pada kuesioner utama.
            $this->completedAttempt($student, $primary, $patterns[$index % count($patterns)], [
                'daysAgo' => $index % 14,
                'publish' => $index % 4 !== 0,
                'teacherNote' => $index % 5 === 0
                    ? self::TEACHER_NOTES[intdiv($index, 5) % count(self::TEACHER_NOTES)]
                    : null,
            ], $scoring);
        }

        // Siswa tidak aktif: satu riwayat lampau saja agar daftar siswa terlihat realistis.
        foreach ($inactiveStudents->values() as $index => $student) {
            $this->completedAttempt($student, $closed, $patterns[($index + 1) % count($patterns)], [
                'daysAgo' => 150 + $index,
                'publish' => true,
                'teacherNote' => null,
            ], $scoring);
        }

        // Hasil terbaru pada sesi LIKERT yang sedang dibuka: 6 siswa pertama
        // sudah mengisi (campuran terpublikasi/belum) supaya laporan & rekap
        // punya data sesi aktif — sisanya sengaja dibiarkan belum mengisi
        // agar portal /sibel masih bisa dicoba mengisi dari awal.
        if ($likert) {
            foreach ($activeStudents->take(6)->values() as $index => $student) {
                $this->completedAttempt($student, $likert, $patterns[($index + 5) % count($patterns)], [
                    'daysAgo' => $index + 1,
                    'publish' => $index % 2 === 0,
                    'teacherNote' => $index === 2
                        ? self::TEACHER_NOTES[0]
                        : null,
                ], $scoring);
            }
        }

        $this->command?->info(sprintf(
            'Data dummy SIBEL siap: %d kelas, %d siswa aktif (+%d tidak aktif), %d kuesioner — sesi dibuka: %s.',
            $classes->count(),
            $activeStudents->count(),
            $inactiveStudents->count(),
            SibelQuestionnaire::count(),
            SibelQuestionnaire::activeForFilling()?->title ?? '-',
        ));
    }

    /**
     * Buat/kloning kuesioner beserta salinan pertanyaan + opsi dari kuesioner
     * sumber (agar setiap kuesioner punya soal sendiri untuk penilaian).
     */
    protected function questionnaire(
        string $title,
        string $instructions,
        string $version,
        string $status,
        bool $needsVerification,
        SibelQuestionnaire $source,
    ): SibelQuestionnaire {
        $questionnaire = SibelQuestionnaire::firstOrCreate(
            ['title' => $title],
            [
                'instructions' => $instructions,
                'version' => $version,
                'status' => $status,
                'allow_retake' => false,
                'needs_verification' => $needsVerification,
            ]
        );

        if (! $questionnaire->questions()->exists()) {
            foreach ($source->questions as $question) {
                $clone = SibelQuestion::create([
                    'questionnaire_id' => $questionnaire->id,
                    'question_text' => $question->question_text,
                    'order_number' => $question->order_number,
                    'is_required' => $question->is_required,
                ]);

                foreach ($question->options as $option) {
                    SibelOption::create([
                        'question_id' => $clone->id,
                        'option_code' => $option->option_code,
                        'option_text' => $option->option_text,
                        'category_code' => $option->category_code,
                    ]);
                }
            }
        }

        return $questionnaire;
    }

    /**
     * @param  array<int, string>  $names
     * @param  Collection<int, SibelClass>  $classes
     * @return Collection<int, SibelStudent>
     */
    protected function students(array $names, $classes, string $status, int $numberOffset)
    {
        return collect($names)->map(fn (string $name, int $index) => SibelStudent::firstOrCreate(
            ['full_name' => $name],
            [
                'class_id' => $classes[$index % $classes->count()]->id,
                'student_number' => 'SBL-'.str_pad((string) ($numberOffset + $index), 3, '0', STR_PAD_LEFT),
                'status' => $status,
                'source' => SibelStudent::SOURCE_MANUAL,
            ]
        ));
    }

    /**
     * Sesi selesai lengkap dengan jawaban + hasil skor (dihitung mesin penilaian).
     *
     * @param  array{daysAgo: int, publish: bool, teacherNote: string|null}  $options
     */
    protected function completedAttempt(
        SibelStudent $student,
        SibelQuestionnaire $questionnaire,
        string $pattern,
        array $options,
        LearningStyleScoringService $scoring,
    ): void {
        if ($student->attempts()->where('questionnaire_id', $questionnaire->id)->exists()) {
            return;
        }

        $startedAt = now()->subDays($options['daysAgo'])->subHours(2);

        $attempt = SibelAttempt::create([
            'student_id' => $student->id,
            'questionnaire_id' => $questionnaire->id,
            'status' => SibelAttempt::STATUS_COMPLETED,
            'started_at' => $startedAt,
            'completed_at' => $startedAt->copy()->addMinutes(25),
        ]);

        $this->fillAnswers($attempt, $questionnaire, $pattern);

        $result = $scoring->scoreAndPersist($attempt);

        if ($options['publish'] || $options['teacherNote'] !== null) {
            $result->update([
                'is_published' => $options['publish'],
                'teacher_note' => $options['teacherNote'],
            ]);
        }
    }

    /**
     * Sesi yang masih berjalan (sebagian terjawab) — tampil di "Aktivitas
     * Pengisian Terbaru" dan dihitung pada "Belum Mengisi" bila belum selesai.
     */
    protected function inProgressAttempt(
        SibelStudent $student,
        SibelQuestionnaire $questionnaire,
        string $pattern,
    ): void {
        if ($student->attempts()->where('questionnaire_id', $questionnaire->id)->exists()) {
            return;
        }

        $attempt = SibelAttempt::create([
            'student_id' => $student->id,
            'questionnaire_id' => $questionnaire->id,
            'status' => SibelAttempt::STATUS_IN_PROGRESS,
            'started_at' => now()->subMinutes(40),
        ]);

        // Jawab sebagian pertanyaan saja (6 pertanyaan pertama).
        $this->fillAnswers($attempt, $questionnaire, $pattern, limit: 6);
    }

    /**
     * Isi jawaban sesuai pola A/B/C. Kode pertanyaan diambil dengan modulo
     * panjang pola supaya tetap aman bila jumlah pertanyaan berubah.
     */
    protected function fillAnswers(SibelAttempt $attempt, SibelQuestionnaire $questionnaire, string $pattern, ?int $limit = null): void
    {
        $questions = $questionnaire->questions->values();
        $length = strlen($pattern);

        foreach ($questions as $index => $question) {
            if ($limit !== null && $index >= $limit) {
                break;
            }

            $code = strtoupper($pattern[$index % $length]);
            $option = $question->options->firstWhere('option_code', $code)
                ?? $question->options->first();

            if ($option === null) {
                continue;
            }

            SibelAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $option->id,
            ]);
        }
    }
}
