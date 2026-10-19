<?php

namespace App\Services;

use App\Models\SibelAttempt;
use App\Models\SibelResult;
use Illuminate\Support\Facades\DB;

/**
 * Mesin penilaian SIBEL.
 *
 * Aturan (lihat rencana proyek — bagian 5F):
 *  1-3. Hitung seluruh jawaban A (visual), B (auditori), C (kinestetik).
 *  4.   Tentukan skor tertinggi.
 *  5-7. Tunggal tertinggi -> kategori dominan.
 *  8-9. Tertinggi sama   -> hasil seri dua/tiga kategori.
 *  10.  Tidak pernah memilih kategori secara acak saat seri.
 *  11.  Simpan skor, kategori, status seri, dan versi kuesioner.
 *  12.  Perhitungan dilakukan di backend (server-side).
 *  13.  Jumlah jawaban wajib harus sama dengan jumlah pertanyaan wajib.
 *
 * Persentase = (jumlah jawaban kategori / total jawaban valid) x 100%.
 */
class LearningStyleScoringService
{
    /**
     * Hitung skor untuk satu sesi pengisian.
     *
     * @return array{
     *     scores: array<string, int>,
     *     percentages: array<string, float>,
     *     total: int,
     *     dominant_categories: array<int, string>,
     *     result_status: string,
     *     interpretation: string,
     *     recommendation: string,
     *     questionnaire_version: string|null,
     * }
     *
     * @throws IncompleteAttemptException Bila jawaban wajib belum lengkap.
     */
    public function compute(SibelAttempt $attempt): array
    {
        $attempt->loadMissing(['questionnaire', 'answers.option']);

        $questions = $attempt->questionnaire->questions;
        $requiredIds = $questions->where('is_required', true)->pluck('id');
        $answers = $attempt->answers;

        // 13. Seluruh pertanyaan wajib harus terjawab.
        $answeredIds = $answers->pluck('question_id');
        $missing = $requiredIds->diff($answeredIds);

        if ($missing->isNotEmpty()) {
            throw new IncompleteAttemptException(
                'Jawaban belum lengkap: masih ada '.$missing->count().' pertanyaan wajib yang belum dijawab.'
            );
        }

        // 1-3. Hitung per kategori berdasarkan `category_code` pada opsi terpilih.
        $scores = array_fill_keys(array_keys(config('sibel.categories', [])), 0);

        foreach ($answers as $answer) {
            $category = $this->categoryOf($answer->option?->option_code, $answer->option?->category_code);

            if ($category !== null && array_key_exists($category, $scores)) {
                $scores[$category]++;
            }
        }

        $total = array_sum($scores);

        if ($total === 0) {
            throw new IncompleteAttemptException('Tidak ada jawaban valid yang bisa dinilai.');
        }

        // Persentase per kategori.
        $percentages = [];
        foreach ($scores as $key => $score) {
            $percentages[$key] = round(($score / $total) * 100, 1);
        }

        // 4. Skor tertinggi.
        $max = max($scores);

        // 5-9. Kategori dengan skor tertinggi (bisa lebih dari satu = seri).
        $dominant = array_keys(array_filter($scores, fn (int $score) => $score === $max));
        $resultStatus = count($dominant) === 1 ? SibelResult::STATUS_DOMINANT : SibelResult::STATUS_TIE;

        return [
            'scores' => $scores,
            'percentages' => $percentages,
            'total' => $total,
            'dominant_categories' => $dominant,
            'result_status' => $resultStatus,
            'interpretation' => $this->interpretation($dominant, $resultStatus),
            'recommendation' => $this->recommendation($dominant),
            'questionnaire_version' => $attempt->questionnaire->version,
        ];
    }

    /**
     * Hitung dan simpan hasil ke tabel `results` (idempoten per sesi).
     * Wajib dipanggil setelah sesi berstatus selesai.
     */
    public function scoreAndPersist(SibelAttempt $attempt): SibelResult
    {
        return DB::transaction(function () use ($attempt) {
            $data = $this->compute($attempt);

            return SibelResult::updateOrCreate(
                ['attempt_id' => $attempt->id],
                [
                    'score_a' => $data['scores']['visual'] ?? 0,
                    'score_b' => $data['scores']['auditori'] ?? 0,
                    'score_c' => $data['scores']['kinestetik'] ?? 0,
                    'percentage_a' => $data['percentages']['visual'] ?? 0,
                    'percentage_b' => $data['percentages']['auditori'] ?? 0,
                    'percentage_c' => $data['percentages']['kinestetik'] ?? 0,
                    'dominant_categories' => $data['dominant_categories'],
                    'result_status' => $data['result_status'],
                    'interpretation' => $data['interpretation'],
                    'recommendation' => $data['recommendation'],
                    'questionnaire_version' => $data['questionnaire_version'],
                ]
            );
        });
    }

    protected function categoryOf(?string $optionCode, ?string $categoryCode): ?string
    {
        if ($categoryCode !== null && $categoryCode !== '') {
            return $categoryCode;
        }

        return $optionCode ? config("sibel.code_category.{$optionCode}") : null;
    }

    /**
     * Interpretasi positif, tidak menghakimi, dan tidak menyatakan siswa
     * hanya mampu belajar dengan satu cara.
     *
     * @param  array<int, string>  $dominant
     */
    protected function interpretation(array $dominant, string $resultStatus): string
    {
        $labels = $this->labels($dominant);

        $descriptions = array_map(
            fn (string $key) => (string) config("sibel.categories.{$key}.description"),
            $dominant
        );

        if ($resultStatus === SibelResult::STATUS_TIE) {
            return 'Hasil kuesioner menunjukkan kecenderungan seimbang antara '.implode(' dan ', $labels)
                .' karena keduanya memperoleh skor tertinggi yang sama. '.implode(' ', $descriptions)
                .' Keduanya sama-sama bisa dimanfaatkan — cobalah variasi strategi dari masing-masing kategori sesuai kebutuhan materi.';
        }

        return 'Kecenderungan utama: '.$labels[0].'. '.$descriptions[0]
            .' Ini adalah kecenderungan, bukan satu-satunya cara belajar — tetap eksplorasi metode lain yang terasa membantu.';
    }

    /**
     * Rekomendasi strategi belajar (digabung bila hasil seri).
     *
     * @param  array<int, string>  $dominant
     */
    protected function recommendation(array $dominant): string
    {
        $labels = $this->labels($dominant);

        $lines = ['Rekomendasi strategi belajar untuk kecenderungan '.implode(' dan ', $labels).':'];

        $seen = [];
        foreach ($dominant as $key) {
            foreach ((array) config("sibel.categories.{$key}.strategies", []) as $strategy) {
                if (isset($seen[$strategy])) {
                    continue;
                }
                $seen[$strategy] = true;
                $lines[] = '- '.$strategy;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, string>  $keys
     * @return array<int, string>
     */
    protected function labels(array $keys): array
    {
        return array_map(
            fn (string $key) => (string) config("sibel.categories.{$key}.label", ucfirst($key)),
            $keys
        );
    }
}
