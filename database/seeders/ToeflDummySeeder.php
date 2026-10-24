<?php

namespace Database\Seeders;

use App\Models\ToeflAccessCode;
use App\Models\ToeflAnswer;
use App\Models\ToeflAttempt;
use App\Models\ToeflEvent;
use App\Models\ToeflExam;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use App\Models\ToeflResult;
use App\Models\ToeflSection;
use App\Models\ToeflStudent;
use App\Services\ToeflAccessCodeService;
use App\Services\ToeflScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Data dummy lengkap untuk modul TOEFL ITP (pengembangan lokal).
 *
 * TIDAK dipanggil otomatis oleh DatabaseSeeder (konsisten dengan seeder demo
 * lain seperti SibelDummySeeder & PpdbDemoSeeder). Jalankan manual:
 *
 *   php artisan db:seed --class=ToeflDummySeeder
 *
 * Isinya:
 *  - 30 peserta (identitas saat masuk portal — tanpa akun login)
 *  - 3 ujian: satu DITUTUP (arsip hasil), satu DIBUKA (bisa langsung dicoba
 *    di portal), dan satu DRAF (bagian sudah dibuat, soal masih kosong)
 *  - Dua paket soal PENUH sesuai spesifikasi baku: Listening 50 soal/35 mnt,
 *    Structure 40 soal/25 mnt, Reading 50 soal/55 mnt (140 butir per ujian,
 *    dari database/data/toefl/*.php)
 *  - Attempt selesai dengan jawaban nyata, skor dihitung melalui
 *    ToeflScoringService (bukan angka karangan), termasuk attempt "waktu
 *    habis" dan tiga sesi yang masih berlangsung untuk pemantauan
 *  - Log peristiwa browser (pindah tab, masuk bagian) dan kode akses
 *    (aktif, kedaluwarsa, dicabut)
 *
 * Seeder ini IDEMPOTEN: bila sebuah ujian sudah ada, isinya tidak dibuat
 * ulang sehingga aman dijalankan berulang kali.
 */
class ToeflDummySeeder extends Seeder
{
    /** Bank soal — nama berkas di database/data/toefl/. */
    protected const BANK_A = 'a';

    protected const BANK_B = 'b';

    /** [nama lengkap, kelas] peserta dummy (identitas ditulis saat masuk portal). */
    protected const STUDENTS = [
        ['Ahmad Fauzan', 'XI IPA 1'],
        ['Aisyah Ramadhani', 'XI IPA 1'],
        ['Bima Saputra', 'XI IPA 1'],
        ['Citra Ayu Lestari', 'XI IPA 2'],
        ['Daffa Alfarizi', 'XI IPA 2'],
        ['Devina Putri', 'XI IPA 2'],
        ['Erick Setiawan', 'XI IPA 3'],
        ['Farah Nabila', 'XI IPA 3'],
        ['Gilang Ramadhan', 'XI IPA 3'],
        ['Hana Maulida', 'XI IPS 1'],
        ['Ilham Pratama', 'XI IPS 1'],
        ['Intan Permata', 'XI IPS 1'],
        ['Joko Prasetyo', 'XII IPA 1'],
        ['Kartika Sari', 'XII IPA 1'],
        ['Lutfi Hakim', 'XII IPA 1'],
        ['Maya Anjani', 'XII IPA 2'],
        ['Muhammad Rizky Ardiansyah', 'XII IPA 2'],
        ['Nadia Safitri', 'XII IPA 2'],
        ['Naufal Hakiki', 'XII IPA 3'],
        ['Putri Ramadhani', 'XII IPA 3'],
        ['Qurrota Ayun', 'XII IPA 3'],
        ['Rizky Firmansyah', 'XII IPS 1'],
        ['Salsabila Nur Aini', 'XII IPS 1'],
        ['Teguh Wibowo', 'XII IPS 2'],
        ['Ulfa Zahra', 'XII IPS 2'],
        ['Yoga Pratama', 'XII IPS 2'],
        ['Zahra Aulia', 'XI IPA 1'],
        ['Andini Puspa', 'XI IPS 2'],
        ['Bagas Nugroho', 'XI IPS 2'],
        ['Cindy Larasati', 'XII IPA 1'],
    ];

    public function run(): void
    {
        $this->command?->info('Menyiapkan data dummy TOEFL ITP...');

        $students = $this->seedStudents();

        /*
        | Ujian 1 — arsip Gelombang 1 (sudah ditutup, banyak hasil).
        */
        $closed = $this->seedExam([
            'title' => 'Try Out TOEFL ITP Level 1 — Gelombang 1',
            'description' => 'Try Out TOEFL ITP Level 1 untuk kelas XI dan XII, Gelombang 1. '
                .'Terdiri dari Listening (50 soal / 35 menit), Structure (40 soal / 25 menit), '
                .'dan Reading (50 soal / 55 menit). Skor latihan dihitung dalam rentang 310–680.',
            'status' => ToeflExam::STATUS_CLOSED,
            'bank' => self::BANK_A,
            'start_at' => now()->subDays(46)->setTime(7, 30),
            'end_at' => now()->subDays(44)->setTime(12, 0),
        ]);

        /*
        | Ujian 2 — Gelombang 2 (masih dibuka, bisa langsung dicoba di portal).
        */
        $open = $this->seedExam([
            'title' => 'Try Out TOEFL ITP Level 1 — Gelombang 2',
            'description' => 'Gelombang 2 sebagai sesi pengganti bagi peserta yang belum mengikuti '
                .'Gelombang 1. Format dan jumlah soal sama, dan hasil langsung terlihat setelah '
                .'ujian dikumpulkan. Masuk melalui portal dengan menulis nama dan kelas.',
            'status' => ToeflExam::STATUS_PUBLISHED,
            'bank' => self::BANK_B,
            'start_at' => now()->subDays(5)->setTime(7, 30),
            'end_at' => now()->addDays(21)->setTime(15, 0),
        ]);

        /*
        | Ujian 3 — paket latihan berikutnya (draf, soal belum diisi).
        */
        $draft = $this->seedExam([
            'title' => 'Latihan UTBK-TOEFL 2026 (Paket C)',
            'description' => 'Paket latihan persiapan UTBK 2026. Bagian dan durasi sudah ditetapkan, '
                .'soal sedang disusun oleh panitia. Ujian belum dapat diikuti sebelum diterbitkan.',
            'status' => ToeflExam::STATUS_DRAFT,
            'bank' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        // Kode akses: aktif / kedaluwarsa / dicabut (contoh status yang bisa dikelola admin).
        $this->seedCodes($closed, active: 2, expired: 1, revoked: 1);
        $portalCode = $this->seedCodes($open, active: 2, expired: 1, revoked: 1);

        // Attempt Gelombang 1 (arsip): 14 selesai + 2 waktu habis.
        $this->seedAttempts($closed, array_slice($students, 0, 16), array_merge(
            array_fill(0, 14, ToeflAttempt::STATUS_SUBMITTED),
            array_fill(0, 2, ToeflAttempt::STATUS_EXPIRED),
        ));

        // Attempt Gelombang 2: 11 selesai, 1 waktu habis, 3 masih berlangsung.
        $this->seedAttempts($open, array_slice($students, 10, 15), array_merge(
            array_fill(0, 11, ToeflAttempt::STATUS_SUBMITTED),
            array_fill(0, 1, ToeflAttempt::STATUS_EXPIRED),
            array_fill(0, 3, ToeflAttempt::STATUS_IN_PROGRESS),
        ));

        $this->report($closed, $open, $draft, $portalCode);
    }

    /**
     * Identitas peserta (tanpa akun login) — dipakai ulang bila sudah ada.
     *
     * @return array<int, ToeflStudent>
     */
    protected function seedStudents(): array
    {
        $students = [];

        foreach (self::STUDENTS as $index => [$name, $className]) {
            $students[] = ToeflStudent::firstOrCreate(
                ['full_name' => $name, 'class_name' => $className],
                [
                    'student_number' => sprintf('TF-2026-%03d', $index + 1),
                    'status' => ToeflStudent::STATUS_ACTIVE,
                ]
            );
        }

        return $students;
    }

    /**
     * Buat ujian + 3 bagian sesuai spesifikasi baku; isi soal hanya bila
     * ujian masih kosong (idempoten).
     *
     * @param  array{title: string, description: string, status: string, bank: ?string, start_at: ?Carbon, end_at: ?Carbon}  $spec
     */
    protected function seedExam(array $spec): ToeflExam
    {
        $exam = ToeflExam::firstOrCreate(
            ['title' => $spec['title']],
            [
                'description' => $spec['description'],
                'status' => $spec['status'],
                'total_questions' => 0,
                'duration_seconds' => 0,
                'start_at' => $spec['start_at'],
                'end_at' => $spec['end_at'],
                'show_result_to_student' => true,
                'allow_review' => true,
            ]
        );

        $sections = [];

        foreach (config('toefl.sections') as $type => $definition) {
            $sections[$type] = ToeflSection::firstOrCreate(
                ['exam_id' => $exam->id, 'section_type' => $type],
                [
                    'title' => $definition['label'],
                    'duration_seconds' => $definition['default_duration'],
                    'section_order' => $definition['order'],
                ]
            );
        }

        if ($spec['bank'] !== null && $exam->questionCount() === 0) {
            $this->seedQuestions($sections, $this->bank($spec['bank']));
        }

        $exam->update([
            'total_questions' => $exam->questionCount(),
            'duration_seconds' => (int) $exam->sections()->sum('duration_seconds'),
        ]);

        return $exam;
    }

    /**
     * Muat bank soal dari database/data/toefl/ (listening_*.php, dst.).
     *
     * @return array{listening: array, structure: array, reading: array}
     */
    protected function bank(string $pack): array
    {
        return [
            'listening' => require base_path("database/data/toefl/listening_{$pack}.php"),
            'structure' => require base_path("database/data/toefl/structure_{$pack}.php"),
            'reading' => require base_path("database/data/toefl/reading_{$pack}.php"),
        ];
    }

    /**
     * @param  array<string, ToeflSection>  $sections
     * @param  array{listening: array, structure: array, reading: array}  $bank
     */
    protected function seedQuestions(array $sections, array $bank): void
    {
        foreach ($bank['listening'] as $index => $item) {
            ToeflQuestion::create([
                'section_id' => $sections['listening']->id,
                'question_type' => 'multiple_choice',
                'question_text' => $item['script']."\n\n".$item['q'],
                'options' => $item['options'],
                'answer_key' => $item['key'],
                'points' => 1,
                'question_order' => $index + 1,
            ]);
        }

        foreach ($bank['structure'] as $index => $item) {
            ToeflQuestion::create([
                'section_id' => $sections['structure']->id,
                'question_type' => $item['kind'] === 'error' ? 'error_identification' : 'sentence_completion',
                'question_text' => $item['q'],
                'options' => $item['options'],
                'answer_key' => $item['key'],
                'points' => 1,
                'question_order' => $index + 1,
            ]);
        }

        foreach ($bank['reading'] as $index => $passage) {
            $model = ToeflPassage::create([
                'section_id' => $sections['reading']->id,
                'title' => $passage['title'],
                'passage_text' => $passage['passage'],
                'passage_order' => $index + 1,
            ]);

            foreach ($passage['questions'] as $order => $question) {
                ToeflQuestion::create([
                    'section_id' => $sections['reading']->id,
                    'passage_id' => $model->id,
                    'question_type' => $this->readingType($question['q']),
                    'question_text' => $question['q'],
                    'options' => $question['options'],
                    'answer_key' => $question['key'],
                    'points' => 1,
                    'question_order' => $order + 1,
                ]);
            }
        }
    }

    /**
     * Tebak jenis soal reading dari bentuk kalimatnya (untuk label admin).
     */
    protected function readingType(string $stem): string
    {
        $stem = mb_strtolower($stem);

        return match (true) {
            str_contains($stem, 'main idea'), str_contains($stem, 'mainly'), str_contains($stem, 'main purpose') => 'main_idea',
            str_contains($stem, 'closest in meaning') => 'vocabulary',
            str_contains($stem, 'refers to') => 'reference',
            str_contains($stem, 'inferred'), str_contains($stem, 'concluded'), str_contains($stem, 'suggest') => 'inference',
            default => 'supporting_detail',
        };
    }

    /**
     * Terbitkan contoh kode akses ujian (level ujian). Mengembalikan kode
     * aktif terakhir dalam format tampilan bila berhasil dibuat.
     */
    protected function seedCodes(ToeflExam $exam, int $active, int $expired, int $revoked): ?string
    {
        if (ToeflAccessCode::where('exam_id', $exam->id)->exists()) {
            return null; // sudah pernah ditanam — jangan dobel
        }

        $service = app(ToeflAccessCodeService::class);
        $portalCode = null;

        for ($i = 0; $i < $active; $i++) {
            $issued = $service->issue($exam);
            $portalCode = $issued['code'];
        }

        for ($i = 0; $i < $expired; $i++) {
            $service->issue($exam, now()->subDays(7));
        }

        for ($i = 0; $i < $revoked; $i++) {
            $issued = $service->issue($exam);
            $issued['model']->update(['revoked_at' => now()->subDay()]);
        }

        return $portalCode;
    }

    /**
     * Tanam attempt + jawaban + peristiwa untuk daftar peserta.
     *
     * @param  array<int, ToeflStudent>  $students
     * @param  array<int, string>  $modes  status tiap peserta (submitted/expired/in_progress)
     */
    protected function seedAttempts(ToeflExam $exam, array $students, array $modes): void
    {
        $sections = $exam->sections()->with(['questions' => fn ($query) => $query->orderBy('question_order')])->get();
        $totalDuration = max(1, (int) $sections->sum('duration_seconds'));

        $accessCode = ToeflAccessCode::query()
            ->where('exam_id', $exam->id)
            ->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderBy('id')
            ->first();

        foreach ($students as $index => $student) {
            $mode = $modes[$index] ?? ToeflAttempt::STATUS_SUBMITTED;
            $timing = $this->timing($exam, $mode, $totalDuration, $index);

            $attempt = ToeflAttempt::firstOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $student->id],
                [
                    'access_code_id' => $accessCode?->id,
                    'status' => $mode,
                    'started_at' => $timing['started_at'],
                    'deadline_at' => $timing['deadline_at'],
                    'submitted_at' => $timing['submitted_at'],
                    'current_section_type' => $mode === ToeflAttempt::STATUS_IN_PROGRESS
                        ? $this->currentSection($sections, $timing['started_at'])
                        : 'reading',
                    'session_version' => 1,
                ]
            );

            if (! $attempt->wasRecentlyCreated) {
                if ($mode === ToeflAttempt::STATUS_IN_PROGRESS || $attempt->answers()->exists()) {
                    continue; // sudah lengkap dari seeder sebelumnya
                }

                // Penanaman parsal (mis. seeder sempat gagal) — bersihkan dulu.
                $attempt->answers()->delete();
                ToeflEvent::where('attempt_id', $attempt->id)->delete();
                ToeflResult::where('attempt_id', $attempt->id)->delete();
            }

            $ability = $this->ability($exam, $student);
            $this->fillAnswers($attempt, $sections, $timing, $ability, $mode);

            if ($mode !== ToeflAttempt::STATUS_IN_PROGRESS) {
                app(ToeflScoringService::class)->scoreAndPersist($attempt->fresh());
            }

            $this->seedEvents($attempt, $timing);
        }
    }

    /**
     * Sebaran jam mulai/batas/kumpul yang masuk akal untuk tiap mode.
     *
     * @return array{started_at: Carbon, deadline_at: Carbon, submitted_at: ?Carbon, used_seconds: int}
     */
    protected function timing(ToeflExam $exam, string $mode, int $totalDuration, int $index): array
    {
        if ($mode === ToeflAttempt::STATUS_IN_PROGRESS) {
            $startedAt = now()->subMinutes(mt_rand(9, 48));

            return [
                'started_at' => $startedAt,
                'deadline_at' => $startedAt->copy()->addSeconds($totalDuration),
                'submitted_at' => null,
                'used_seconds' => $startedAt->diffInSeconds(now()),
            ];
        }

        // Attempt selesai disebar di dalam jadwal ujian (bila masih berjalan, ya di masa lalu).
        $windowStart = ($exam->start_at ?? now()->subDays(10))->copy();
        $windowEnd = ($exam->end_at ?? now())->copy();

        if ($windowEnd->isFuture()) {
            $windowEnd = now()->subHours(3);
        }

        if ($windowEnd->lessThanOrEqualTo($windowStart)) {
            $windowStart = $windowEnd->copy()->subDays(2);
        }

        $span = max(1, $windowStart->diffInSeconds($windowEnd));
        $startedAt = $windowStart->copy()
            ->addSeconds((int) ($span * ($index + 1) / (count(self::STUDENTS) + 2)))
            ->addMinutes(mt_rand(0, 45));

        $deadlineAt = $startedAt->copy()->addSeconds($totalDuration);

        // Pengerjaan normal memakai 55–90% waktu; yang waktu habis melewati deadline.
        $used = $mode === ToeflAttempt::STATUS_EXPIRED
            ? $totalDuration + mt_rand(35, 95)
            : (int) ($totalDuration * (550 + mt_rand(0, 350)) / 1000);

        return [
            'started_at' => $startedAt,
            'deadline_at' => $deadlineAt,
            'submitted_at' => $startedAt->copy()->addSeconds($used),
            'used_seconds' => $used,
        ];
    }

    /**
     * Kemampuan peserta (deterministik per ujian + siswa) agar sebaran skor
     * terlihat realistis: ada yang di bawah 400 sampai di atas 600.
     */
    protected function ability(ToeflExam $exam, ToeflStudent $student): float
    {
        mt_srand($exam->id * 100003 + $student->id * 977);

        return round(0.22 + mt_rand(0, 740) / 1000, 3);
    }

    /**
     * Tulis jawaban peserta: sebagian benar, sebagian salah, sebagian
     * dikosongkan — mengikuti kemampuan tiap peserta agar skor akhir
     * (dihitung ToeflScoringService) tersebar realistis.
     *
     * @param  Collection<int, ToeflSection>  $sections
     * @param  array{started_at: Carbon, deadline_at: Carbon, submitted_at: ?Carbon, used_seconds: int}  $timing
     */
    protected function fillAnswers(ToeflAttempt $attempt, Collection $sections, array $timing, float $ability, string $mode): void
    {
        $blankRate = $mode === ToeflAttempt::STATUS_EXPIRED ? 0.24 : 0.05;
        $startedAt = $timing['started_at'];
        $limit = $mode === ToeflAttempt::STATUS_IN_PROGRESS ? now() : $timing['submitted_at'];
        $totalQuestions = max(1, (int) $sections->sum(fn ($section) => $section->questions->count()));

        // Sesi berjalan: jawaban tersebar merata di seluruh durasi ujian sehingga
        // hanya bagian yang sudah sempat dikerjakan yang terisi.
        $spacing = $mode === ToeflAttempt::STATUS_IN_PROGRESS
            ? max(1, (int) $sections->sum('duration_seconds'))
            : max(1, $timing['used_seconds']);

        mt_srand($attempt->id * 6151 + 17);

        $rows = [];
        $cursor = 0;

        foreach ($sections as $section) {
            $sectionAbility = min(0.99, max(0.25, $ability + mt_rand(-70, 70) / 1000));

            foreach ($section->questions as $question) {
                $cursor++;

                $savedAt = $startedAt->copy()->addSeconds(
                    (int) ($spacing * $cursor / $totalQuestions)
                );

                if ($savedAt->greaterThan($limit)) {
                    continue 2; // sisa waktu tidak cukup — sengaja dikosongkan
                }

                if (mt_rand(1, 1000) <= (int) ($blankRate * 1000)) {
                    continue; // peserta melewatkan soal ini
                }

                $selected = $question->answer_key;

                if (mt_rand(1, 1000) > (int) ($sectionAbility * 1000)) {
                    $wrong = array_values(array_filter(
                        array_keys($question->options ?? []),
                        fn (string $option) => strcasecmp($option, $question->answer_key) !== 0
                    ));

                    $selected = $wrong === [] ? $question->answer_key : $wrong[array_rand($wrong)];
                }

                $rows[] = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_answer' => $selected,
                    'saved_at' => $savedAt,
                    'created_at' => $savedAt,
                    'updated_at' => $savedAt,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            ToeflAnswer::insert($chunk);
        }
    }

    /**
     * Bagian yang sedang dikerjakan (untuk sesi berjalan) dihitung dari
     * durasi kumulatif tiap bagian.
     *
     * @param  Collection<int, ToeflSection>  $sections
     */
    protected function currentSection(Collection $sections, Carbon $startedAt): string
    {
        $elapsed = $startedAt->diffInSeconds(now());
        $cumulative = 0;

        foreach ($sections as $section) {
            $cumulative += $section->duration_seconds;

            if ($elapsed < $cumulative) {
                return $section->section_type;
            }
        }

        return 'reading';
    }

    /**
     * Log pemantauan browser: masuk/keluar bagian + beberapa perpindahan
     * tab (sebagian attempt bersih tanpa perpindahan tab).
     *
     * @param  array{started_at: Carbon, deadline_at: Carbon, submitted_at: ?Carbon, used_seconds: int}  $timing
     */
    protected function seedEvents(ToeflAttempt $attempt, array $timing): void
    {
        if (ToeflEvent::where('attempt_id', $attempt->id)->exists()) {
            return;
        }

        $startedAt = $timing['started_at'];
        $endAt = $timing['submitted_at'] ?? now();

        mt_srand($attempt->id * 3571 + 11);

        $events = [];
        $offset = 0;

        // Masuk/keluar tiap bagian, dihitung dari durasi kumulatif (Listening
        // 35 mnt, Structure +25 mnt, Reading +55 mnt) — peristiwa yang
        // waktunya melewati batas pengerjaan tidak dicatat.
        foreach ($attempt->exam->sections as $section) {
            $events[] = [
                'type' => 'section_enter',
                'at' => $startedAt->copy()->addSeconds($offset),
                'metadata' => ['section' => $section->section_type],
            ];

            $offset += $section->duration_seconds;

            $events[] = [
                'type' => 'section_leave',
                'at' => $startedAt->copy()->addSeconds($offset),
                'metadata' => ['section' => $section->section_type],
            ];
        }

        // Sebagian peserta sempat berpindah tab / keluar layar penuh.
        $span = max(120, $startedAt->diffInSeconds($endAt));
        $switches = mt_rand(0, 4);

        for ($i = 0; $i < $switches; $i++) {
            $at = $startedAt->copy()->addSeconds(mt_rand(120, max(121, $span - 60)));

            $events[] = ['type' => 'tab_hidden', 'at' => $at, 'metadata' => ['visibilityState' => 'hidden']];
            $events[] = [
                'type' => 'tab_visible',
                'at' => $at->copy()->addSeconds(mt_rand(4, 60)),
                'metadata' => ['visibilityState' => 'visible'],
            ];
        }

        $events = array_values(array_filter(
            $events,
            fn (array $event) => $event['at']->lessThanOrEqualTo($endAt)
        ));

        usort($events, fn (array $a, array $b) => $a['at']->getTimestamp() <=> $b['at']->getTimestamp());

        ToeflEvent::insert(array_map(fn (array $event) => [
            'attempt_id' => $attempt->id,
            'event_type' => $event['type'],
            'event_timestamp' => $event['at'],
            'metadata' => json_encode($event['metadata']),
            'created_at' => $event['at'],
            'updated_at' => $event['at'],
        ], $events));
    }

    /**
     * Ringkasan di terminal + kode akses untuk dicoba di portal.
     */
    protected function report(ToeflExam $closed, ToeflExam $open, ToeflExam $draft, ?string $portalCode): void
    {
        $this->command?->info('Data dummy TOEFL ITP siap.');

        $this->command?->table(
            ['Ujian', 'Status', 'Soal', 'Durasi', 'Attempt'],
            [
                $this->row($closed),
                $this->row($open),
                $this->row($draft),
            ]
        );

        $this->command?->info('Peserta dummy: '.ToeflStudent::count()
            .' identitas | Hasil tersimpan: '.ToeflResult::count()
            .' | Sesi berjalan: '.ToeflAttempt::where('status', ToeflAttempt::STATUS_IN_PROGRESS)->count());

        if ($portalCode !== null) {
            $this->command?->info('Kode akses Gelombang 2 (aktif): '.$portalCode
                .' — tampil sekali ini saja, salin sekarang.');
        } else {
            $this->command?->info('Kode akses sudah pernah dibuat sebelumnya. '
                .'Buat kode baru lewat halaman detail ujian bila perlu.');
        }

        $this->command?->info('Coba sebagai peserta: '.route('toefl.portal'));
        $this->command?->info('Panel pengelola: '.route('toefl.admin.dashboard'));
    }

    /**
     * @return array<int, string>
     */
    protected function row(ToeflExam $exam): array
    {
        return [
            $exam->title,
            $exam->statusLabel(),
            (string) $exam->total_questions,
            sprintf('%d mnt', intdiv((int) $exam->duration_seconds, 60)),
            (string) $exam->attempts()->count(),
        ];
    }
}
