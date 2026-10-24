<?php

namespace App\Services;

use App\Models\ToeflAttempt;
use App\Models\ToeflResult;
use Illuminate\Support\Facades\DB;

/**
 * Mesin penilaian TOEFL ITP Level 1 — dihitung sepenuhnya di server.
 *
 *  1. Jawaban siswa dibandingkan dengan `answer_key` tiap pertanyaan.
 *  2. Skor mentah per bagian (Listening / Structure / Reading) = jumlah benar.
 *  3. Total benar = jumlah seluruh benar; total soal = jumlah seluruh soal.
 *  4. Skor scaled = aproksimasi linear [310..680] dari proporsi benar
 *     (tabel konversi resmi ETS berbeda — hasil dicap sebagai latihan).
 *  5. Skor HANYA dihitung & disimpan oleh server; kunci jawaban tidak pernah
 *     dikirim ke respons sisi siswa.
 */
class ToeflScoringService
{
    /**
     * Hitung skor untuk satu attempt (idempoten — updateOrCreate).
     */
    public function scoreAndPersist(ToeflAttempt $attempt): ToeflResult
    {
        return DB::transaction(function () use ($attempt) {
            $attempt->loadMissing(['exam.sections.questions', 'answers.question']);

            $byType = ['listening' => 0, 'structure' => 0, 'reading' => 0];
            $totalQuestions = 0;
            $totalCorrect = 0;

            $answers = $attempt->answers->keyBy('question_id');

            foreach ($attempt->exam->sections as $section) {
                foreach ($section->questions as $question) {
                    $totalQuestions++;

                    $answer = $answers->get($question->id);
                    $given = $answer?->selected_answer;

                    if ($given !== null && $given !== '' && strcasecmp($given, $question->answer_key) === 0) {
                        $totalCorrect++;

                        if (array_key_exists($section->section_type, $byType)) {
                            $byType[$section->section_type]++;
                        }
                    }
                }
            }

            return ToeflResult::updateOrCreate(
                ['attempt_id' => $attempt->id],
                [
                    'listening_raw_score' => $byType['listening'],
                    'structure_raw_score' => $byType['structure'],
                    'reading_raw_score' => $byType['reading'],
                    'total_correct' => $totalCorrect,
                    'total_questions' => $totalQuestions,
                    'scaled_score' => $this->scaledScore($totalCorrect, $totalQuestions),
                    'completed_at' => now(),
                ]
            );
        });
    }

    /**
     * Aproksimasi linear skor scaled TOEFL ITP (310–680).
     * CATATAN: bukan tabel konversi resmi ETS — untuk latihan.
     */
    public function scaledScore(int $correct, int $total): int
    {
        if ($total <= 0) {
            return 0;
        }

        $min = (int) config('toefl.scoring.min_scaled', 310);
        $max = (int) config('toefl.scoring.max_scaled', 680);

        $ratio = $correct / $total;

        return (int) round($min + ($ratio * ($max - $min)));
    }
}
