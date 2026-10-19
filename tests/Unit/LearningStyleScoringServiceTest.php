<?php

namespace Tests\Unit;

use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelResult;
use App\Services\IncompleteAttemptException;
use App\Services\LearningStyleScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Mesin penilaian SIBEL (rencana proyek — bagian 11, skenario 1-6 & 12).
 */
class LearningStyleScoringServiceTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected LearningStyleScoringService $scoring;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scoring = app(LearningStyleScoringService::class);
        $this->createSibelRoles();
    }

    /**
     * Bangun sesi + jawaban dari pola, lalu hitung (tanpa persist).
     */
    protected function compute(string $pattern): array
    {
        return $this->scoring->compute($this->buildAttempt($pattern));
    }

    protected function buildAttempt(string $pattern): SibelAttempt
    {
        $questionnaire = $this->createQuestionnaire(strlen($pattern));
        $user = $this->createSibelUser('siswa');
        $student = $this->createStudentFor($user, SibelClass::create(['name' => 'X-A']));

        return $this->createAttempt($student, $questionnaire, $pattern);
    }

    public function test_semua_jawaban_a_menghasilkan_visual(): void
    {
        $data = $this->compute(str_repeat('A', 15));

        $this->assertSame(15, $data['scores']['visual']);
        $this->assertSame(0, $data['scores']['auditori']);
        $this->assertSame(0, $data['scores']['kinestetik']);
        $this->assertSame(['visual'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_DOMINANT, $data['result_status']);
        $this->assertStringContainsString('Visual', $data['interpretation']);
    }

    public function test_semua_jawaban_b_menghasilkan_auditori(): void
    {
        $data = $this->compute(str_repeat('B', 12));

        $this->assertSame(0, $data['scores']['visual']);
        $this->assertSame(12, $data['scores']['auditori']);
        $this->assertSame(['auditori'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_DOMINANT, $data['result_status']);
        $this->assertStringContainsString('Auditori', $data['interpretation']);
    }

    public function test_semua_jawaban_c_menghasilkan_kinestetik(): void
    {
        $data = $this->compute(str_repeat('C', 12));

        $this->assertSame(0, $data['scores']['visual']);
        $this->assertSame(12, $data['scores']['kinestetik']);
        $this->assertSame(['kinestetik'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_DOMINANT, $data['result_status']);
        $this->assertStringContainsString('Kinestetik', $data['interpretation']);
    }

    public function test_a7_b4_c3_menghasilkan_visual_dan_persentase_benar(): void
    {
        $data = $this->compute('AAAAAAA'.'BBBB'.'CCC');

        // Skenario 4: A=7, B=4, C=3 -> Visual dominan.
        $this->assertSame(7, $data['scores']['visual']);
        $this->assertSame(4, $data['scores']['auditori']);
        $this->assertSame(3, $data['scores']['kinestetik']);
        $this->assertSame(['visual'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_DOMINANT, $data['result_status']);

        // Skenario 12: persentase = (jawaban / total valid) x 100%.
        $this->assertSame(14, $data['total']);
        $this->assertSame(50.0, $data['percentages']['visual']);
        $this->assertSame(28.6, $data['percentages']['auditori']);
        $this->assertSame(21.4, $data['percentages']['kinestetik']);
    }

    public function test_a6_b6_c2_menghasilkan_seri_visual_dan_auditori(): void
    {
        $data = $this->compute('AAAAAA'.'BBBBBB'.'CC');

        // Skenario 5: dua kategori tertinggi sama -> seri (tanpa pilih acak).
        $this->assertSame(6, $data['scores']['visual']);
        $this->assertSame(6, $data['scores']['auditori']);
        $this->assertSame(2, $data['scores']['kinestetik']);
        $this->assertSame(['visual', 'auditori'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_TIE, $data['result_status']);
        $this->assertStringContainsString('seimbang antara Visual dan Auditori', $data['interpretation']);

        // Rekomendasi memuat strategi kedua kategori yang seri.
        $this->assertStringContainsString(config('sibel.categories.visual.strategies.0'), $data['recommendation']);
        $this->assertStringContainsString(config('sibel.categories.auditori.strategies.0'), $data['recommendation']);
    }

    public function test_a4_b4_c4_menghasilkan_seri_tiga_kategori(): void
    {
        $data = $this->compute('AAAA'.'BBBB'.'CCCC');

        // Skenario 6: ketiga kategori sama -> seri tiga kategori.
        $this->assertSame(['visual', 'auditori', 'kinestetik'], $data['dominant_categories']);
        $this->assertSame(SibelResult::STATUS_TIE, $data['result_status']);
        $this->assertCount(3, $data['dominant_categories']);
        $this->assertSame(33.3, $data['percentages']['visual']);
    }

    public function test_persentase_ketiga_kategori_berjumlah_sekitar_100(): void
    {
        $data = $this->compute('AAAAA'.'BBB'.'CC');

        $sum = $data['percentages']['visual'] + $data['percentages']['auditori'] + $data['percentages']['kinestetik'];

        $this->assertEqualsWithDelta(100.0, $sum, 0.1);
    }

    public function test_jawaban_wajib_yang_belum_lengkap_ditolak(): void
    {
        $questionnaire = $this->createQuestionnaire(5);
        $user = $this->createSibelUser('siswa');
        $student = $this->createStudentFor($user);
        // Hanya 4 dari 5 pertanyaan wajib terjawab.
        $attempt = $this->createAttempt($student, $questionnaire, 'AAAB');

        $this->expectException(IncompleteAttemptException::class);

        $this->scoring->compute($attempt);
    }

    public function test_skor_dan_versi_instrumen_tersimpan_pada_hasil(): void
    {
        $attempt = $this->buildAttempt('AAABBBCCC');

        $result = $this->scoring->scoreAndPersist($attempt);

        $this->assertDatabaseHas('results', [
            'attempt_id' => $attempt->id,
            'score_a' => 3,
            'score_b' => 3,
            'score_c' => 3,
            'questionnaire_version' => '1.0',
        ]);
        $this->assertSame(['visual', 'auditori', 'kinestetik'], $result->dominant_categories);
    }

    public function test_penilaian_ulang_pada_sesi_yang_sama_tidak_membuat_hasil_ganda(): void
    {
        $attempt = $this->buildAttempt(str_repeat('A', 3));

        $first = $this->scoring->scoreAndPersist($attempt);
        $second = $this->scoring->scoreAndPersist($attempt);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, SibelResult::count());
    }
}
