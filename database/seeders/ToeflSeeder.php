<?php

namespace Database\Seeders;

use App\Models\ToeflExam;
use App\Models\ToeflPassage;
use App\Models\ToeflQuestion;
use App\Models\ToeflSection;
use App\Services\ToeflAccessCodeService;
use Illuminate\Database\Seeder;

/**
 * Ujian demo TOEFL ITP Level 1 — TANPA akun login.
 *
 * KONTEN: soal-soal di sini adalah CONTOH latihan singkat (bukan butir
 * resmi ETS) — dibuat agar seluruh alur (identitas + kode akses, timer,
 * 3 bagian, auto-save, audio, skor) bisa langsung diuji. Publikasikan
 * ujian lewat panel admin setelah mengganti soal dengan butir ujian yang sah.
 *
 * SPESIFIKASI 3 BAGIAN (dipakai ujian demo ini & jadi default ujian baru):
 *   1. Listening Comprehension          — 50 soal / 35 menit (2100 detik)
 *   2. Structure and Written Expression — 40 soal / 25 menit (1500 detik)
 *   3. Reading Comprehension            — 50 soal / 55 menit (3300 detik)
 */
class ToeflSeeder extends Seeder
{
    public function run(): void
    {
        $exam = ToeflExam::firstOrCreate(
            ['title' => 'Try Out TOEFL ITP Level 1 (Contoh)'],
            [
                'description' => 'Ujian contoh untuk menguji seluruh alur ujian TOEFL ITP online. Terdiri dari 3 bagian: Listening 50 soal/35 menit, Structure 40 soal/25 menit, dan Reading 50 soal/55 menit.',
                'status' => ToeflExam::STATUS_PUBLISHED,
                'start_at' => now()->subDay(),
                'end_at' => now()->addMonth(),
                'show_result_to_student' => true,
                'allow_review' => true,
                'exam_code' => 'TOEFLDEMO',
                'created_by' => null,
            ]
        );

        if ($exam->sections()->exists()) {
            $this->command?->info('Ujian demo TOEFL sudah ada — dilewati.');

            return;
        }

        // Tiga bagian sesuai spesifikasi baku (lihat config/toefl.php).
        $listening = $exam->sections()->create([
            'section_type' => 'listening',
            'title' => 'Listening Comprehension',
            'duration_seconds' => 2100, // 35 menit — 50 soal
            'section_order' => 1,
        ]);
        $structure = $exam->sections()->create([
            'section_type' => 'structure',
            'title' => 'Structure and Written Expression',
            'duration_seconds' => 1500, // 25 menit — 40 soal
            'section_order' => 2,
        ]);
        $reading = $exam->sections()->create([
            'section_type' => 'reading',
            'title' => 'Reading Comprehension',
            'duration_seconds' => 3300, // 55 menit — 50 soal
            'section_order' => 3,
        ]);

        // ----- Listening (contoh tanpa file audio; unggah via panel admin) -----
        foreach ($this->listeningQuestions() as $i => $question) {
            $this->createQuestion($listening, $question, $i + 1);
        }

        // ----- Structure & Written Expression -----
        foreach ($this->structureQuestions() as $i => $question) {
            $type = $i % 2 === 0 ? 'sentence_completion' : 'error_identification';
            $this->createQuestion($structure, $question, $i + 1, $type);
        }

        // ----- Reading (berbasis bacaan) -----
        $passage = ToeflPassage::create([
            'section_id' => $reading->id,
            'title' => 'The History of Pekanbaru',
            'passage_text' => 'Pekanbaru is the capital city of Riau Province on the island of Sumatra, Indonesia. '
                .'The city grew rapidly in the early twentieth century after oil was discovered in the surrounding '
                .'region. Several Dutch oil companies established their headquarters there, and a railway was '
                ."built to connect the fields with the city.\n\n"
                .'Today, Pekanbaru is a busy center of trade, education, and government. Its population has grown '
                .'from a few thousand at the beginning of the last century to more than one million people. '
                .'Despite this rapid development, the city still preserves several historic buildings from the '
                .'oil boom era, which have become popular tourist attractions.',
            'passage_order' => 1,
        ]);

        foreach ($this->readingQuestions() as $i => $question) {
            $this->createQuestion($reading, $question, $i + 1, 'main_idea', $passage);
        }

        // Perbarui ringkasan ujian.
        $exam->update([
            'total_questions' => $exam->questionCount(),
            'duration_seconds' => (int) $exam->sections()->sum('duration_seconds'),
        ]);

        // 1 kode akses contoh (cetak ke terminal sekali saja) — kode level
        // ujian, dipakai seluruh peserta yang menulis nama + kelas di portal.
        $issued = app(ToeflAccessCodeService::class)->issue($exam);

        $this->command?->info('Ujian demo TOEFL ITP dibuat: 3 bagian sesuai spesifikasi baku '
            .'(Listening 50 soal/35 mnt, Structure 40 soal/25 mnt, Reading 50 soal/55 mnt) '
            .'— berisi '.$exam->questionCount().' soal contoh.');
        $this->command?->info('Kode akses portal: '.$issued['code']
            .' (tampil sekali ini saja — salin sekarang).');
    }

    /**
     * @param  array{q: string, options: array<string, string>, key: string}  $question
     */
    protected function createQuestion(ToeflSection $section, array $question, int $order, string $type = 'multiple_choice', ?ToeflPassage $passage = null): ToeflQuestion
    {
        return ToeflQuestion::create([
            'section_id' => $section->id,
            'passage_id' => $passage?->id,
            'question_type' => $type,
            'question_text' => $question['q'],
            'options' => $question['options'],
            'answer_key' => $question['key'],
            'points' => 1,
            'question_order' => $order,
        ]);
    }

    /**
     * @return array<int, array{q: string, options: array<string, string>, key: string}>
     */
    protected function listeningQuestions(): array
    {
        return [
            ['q' => 'Where does the conversation most probably take place?', 'options' => ['A' => 'At a restaurant', 'B' => 'At a library', 'C' => 'At an airport', 'D' => 'At a hospital'], 'key' => 'C'],
            ['q' => 'What is the woman going to do?', 'options' => ['A' => 'Buy a ticket', 'B' => 'Cancel her trip', 'C' => 'Wait for her friend', 'D' => 'Change her seat'], 'key' => 'A'],
            ['q' => 'How much does the man need to pay?', 'options' => ['A' => '$15', 'B' => '$50', 'C' => '$75', 'D' => '$100'], 'key' => 'B'],
            ['q' => 'What time will the train depart?', 'options' => ['A' => '7:15', 'B' => '7:50', 'C' => '8:15', 'D' => '8:50'], 'key' => 'D'],
            ['q' => 'Why is the man late?', 'options' => ['A' => 'He missed the bus', 'B' => 'He got stuck in traffic', 'C' => 'He overslept', 'D' => 'He took the wrong road'], 'key' => 'B'],
        ];
    }

    /**
     * @return array<int, array{q: string, options: array<string, string>, key: string}>
     */
    protected function structureQuestions(): array
    {
        return [
            ['q' => 'Neither the teacher nor the students _____ aware of the schedule change.', 'options' => ['A' => 'was', 'B' => 'were', 'C' => 'is', 'D' => 'has been'], 'key' => 'B'],
            ['q' => 'The committee _____ the proposal until next Friday.', 'options' => ['A' => 'will not discuss', 'B' => 'does not discuss', 'C' => 'had not discussed', 'D' => 'not discussed'], 'key' => 'A'],
            ['q' => 'If she had studied harder, she _____ the exam.', 'options' => ['A' => 'will pass', 'B' => 'would pass', 'C' => 'would have passed', 'D' => 'has passed'], 'key' => 'C'],
            ['q' => 'Hardly _____ the classroom when the bell rang.', 'options' => ['A' => 'he had entered', 'B' => 'had he entered', 'C' => 'he has entered', 'D' => 'did he enter'], 'key' => 'B'],
            ['q' => 'The new regulation aims at _____ the number of accidents on the road.', 'options' => ['A' => 'reduce', 'B' => 'reducing', 'C' => 'reduced', 'D' => 'reduction'], 'key' => 'B'],
            ['q' => 'Identifikasi kesalahan: The students (A) was told (B) to hand in (C) their reports (D) tomorrow.', 'options' => ['A' => 'was told', 'B' => 'to hand in', 'C' => 'their reports', 'D' => 'tomorrow'], 'key' => 'A'],
            ['q' => 'Identifikasi kesalahan: She (A) has been working (B) here (C) since three years (D) ago.', 'options' => ['A' => 'has been working', 'B' => 'here', 'C' => 'since three years', 'D' => 'ago'], 'key' => 'C'],
            ['q' => 'Identifikasi kesalahan: The quality (A) of the products (B) were checked (C) before shipping (D).', 'options' => ['A' => 'of the products', 'B' => 'were checked', 'C' => 'before shipping', 'D' => 'The quality'], 'key' => 'B'],
        ];
    }

    /**
     * @return array<int, array{q: string, options: array<string, string>, key: string}>
     */
    protected function readingQuestions(): array
    {
        return [
            ['q' => 'What is the main idea of the passage?', 'options' => ['A' => 'Oil was discovered in Pekanbaru', 'B' => 'Pekanbaru has developed from a small town into a major city', 'C' => 'Dutch companies built railways in Sumatra', 'D' => 'Tourism is the main industry in Pekanbaru'], 'key' => 'B'],
            ['q' => 'When did Pekanbaru grow rapidly?', 'options' => ['A' => 'In the early twentieth century', 'B' => 'After World War II', 'C' => 'Last century only recently', 'D' => 'At the beginning of the twenty-first century'], 'key' => 'A'],
            ['q' => 'The word "preserves" in paragraph 2 is closest in meaning to _____.', 'options' => ['A' => 'destroys', 'B' => 'builds', 'C' => 'keeps', 'D' => 'sells'], 'key' => 'C'],
            ['q' => 'What was built to connect the oil fields with the city?', 'options' => ['A' => 'A highway', 'B' => 'A railway', 'C' => 'An airport', 'D' => 'A pipeline'], 'key' => 'B'],
            ['q' => 'It can be inferred from the passage that _____.', 'options' => ['A' => 'Pekanbaru has more than one million inhabitants', 'B' => 'Oil is still the only industry in Pekanbaru', 'C' => 'Historic buildings were removed for tourism', 'D' => 'The population has decreased'], 'key' => 'A'],
        ];
    }
}
