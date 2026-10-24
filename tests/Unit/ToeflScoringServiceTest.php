<?php

namespace Tests\Unit;

use App\Models\ToeflAttempt;
use App\Models\ToeflResult;
use App\Services\ToeflScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Mesin penilaian TOEFL — skor mentah per bagian + skor scaled aproksimasi.
 */
class ToeflScoringServiceTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected ToeflScoringService $scoring;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scoring = app(ToeflScoringService::class);
    }

    public function test_skor_scaled_pada_rentang_minimal_dan_maksimal(): void
    {
        $min = (int) config('toefl.scoring.min_scaled');
        $max = (int) config('toefl.scoring.max_scaled');

        $this->assertSame($min, $this->scoring->scaledScore(0, 140));
        $this->assertSame($max, $this->scoring->scaledScore(140, 140));
        $this->assertSame(0, $this->scoring->scaledScore(10, 0), 'Tanpa soal -> 0.');
    }

    public function test_skor_scaled_linear_di_tengah_rentang(): void
    {
        $min = (int) config('toefl.scoring.min_scaled');
        $max = (int) config('toefl.scoring.max_scaled');
        $midpoint = (int) round($min + (($max - $min) / 2));

        $this->assertSame($midpoint, $this->scoring->scaledScore(70, 140));
    }

    public function test_skor_mentah_dihitung_per_bagian_dari_kunci_jawaban(): void
    {
        $attempt = $this->startAttemptWithAnswers([
            // Listening: 2 benar dari 2.
            0 => 'A',
            1 => 'A',
            // Structure: 1 benar dari 2.
            2 => 'A',
            3 => 'B',
            // Reading: 0 benar dari 2.
            4 => 'C',
            5 => 'D',
        ]);

        $result = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame(2, $result->listening_raw_score);
        $this->assertSame(1, $result->structure_raw_score);
        $this->assertSame(0, $result->reading_raw_score);
        $this->assertSame(3, $result->total_correct);
        $this->assertSame(6, $result->total_questions);
    }

    public function test_jawaban_kosong_tidak_dihitung_benar(): void
    {
        $attempt = $this->startAttemptWithAnswers([
            0 => 'A',
            1 => null, // sengaja dikosongkan
        ]);

        $result = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame(1, $result->total_correct);
        $this->assertSame(6, $result->total_questions);
    }

    public function test_penulisan_huruf_besar_kecil_tidak_mempengaruhi_skor(): void
    {
        $attempt = $this->startAttemptWithAnswers([
            0 => 'a', // huruf kecil — tetap cocok dengan kunci 'A'
            1 => 'A',
        ]);

        $result = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame(2, $result->listening_raw_score);
    }

    public function test_penilaian_ulang_idempoten_mengupdate_bukan_menduplikasi(): void
    {
        $attempt = $this->startAttemptWithAnswers([0 => 'A']);

        $first = $this->scoring->scoreAndPersist($attempt);

        // Skor ulang (mis. setelah submit ganda) -> baris yang sama di-update.
        $second = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, ToeflResult::count());
        $this->assertSame(1, $second->total_correct);
    }

    public function test_persentase_dan_rincian_bagian_untuk_tampilan(): void
    {
        $attempt = $this->startAttemptWithAnswers([0 => 'A', 1 => 'A']);

        $result = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame(33.3, $result->percentage(), '2 benar dari 6 soal = 33.3%.');
        $breakdown = $result->sectionsBreakdown();
        $this->assertSame(2, $breakdown['listening']['raw']);
        $this->assertArrayHasKey('structure', $breakdown);
        $this->assertArrayHasKey('reading', $breakdown);
    }

    /**
     * Rangkai attempt dengan jawaban terpasang (index -> kode jawaban|null).
     * Urutan soal mengikuti id (listening dulu, lalu structure, reading).
     *
     * @param  array<int, string|null>  $answersByIndex
     */
    protected function startAttemptWithAnswers(array $answersByIndex): ToeflAttempt
    {
        $exam = $this->buildExam(2)['exam'];
        $code = $this->issueCode($exam);

        $attempt = $this->enterExam($code, $exam);
        $this->beginExam($attempt);

        $questions = $this->allQuestions($exam);

        foreach ($answersByIndex as $index => $answer) {
            if ($answer === null) {
                continue;
            }

            $attempt->answers()->create([
                'question_id' => $questions[$index]->id,
                'selected_answer' => $answer,
                'saved_at' => now(),
            ]);
        }

        return $attempt->refresh();
    }
}
