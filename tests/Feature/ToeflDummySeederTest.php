<?php

namespace Tests\Feature;

use App\Models\ToeflAnswer;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflQuestion;
use App\Models\ToeflResult;
use App\Models\ToeflStudent;
use Database\Seeders\ToeflDummySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Data dummy TOEFL: struktur yang dijanjikan seeder (3 ujian, bank soal
 * sesuai spesifikasi baku, attempt bernilai nyata) + sifat idempoten.
 */
class ToeflDummySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_menanam_ujian_penuh_spesifikasi_dan_rangkaian_attempt(): void
    {
        $this->seed(ToeflDummySeeder::class);

        $closed = ToeflExam::where('title', 'like', '%Gelombang 1%')->firstOrFail();
        $open = ToeflExam::where('title', 'like', '%Gelombang 2%')->firstOrFail();
        $draft = ToeflExam::where('title', 'like', '%Paket C%')->firstOrFail();

        $this->assertSame(ToeflExam::STATUS_CLOSED, $closed->status);
        $this->assertSame(ToeflExam::STATUS_PUBLISHED, $open->status);
        $this->assertSame(ToeflExam::STATUS_DRAFT, $draft->status);

        foreach ([$closed, $open] as $exam) {
            $this->assertSame(140, $exam->total_questions);
            $this->assertSame(6900, $exam->duration_seconds);

            // Listening 50/35 mnt, Structure 40/25 mnt, Reading 50/55 mnt.
            $this->assertSame(
                ['listening' => 50, 'structure' => 40, 'reading' => 50],
                $exam->sections->mapWithKeys(fn ($section) => [
                    $section->section_type => $section->questions()->count(),
                ])->all()
            );
        }

        // Ujian draf hanya berisi kerangka bagian (soal belum diisi).
        $this->assertSame(0, $draft->total_questions);
        $this->assertSame(3, $draft->sections()->count());

        // 30 identitas peserta — semuanya tanpa akun login.
        $this->assertSame(30, ToeflStudent::count());
        $this->assertSame(0, ToeflStudent::whereNotNull('user_id')->count());

        $this->assertSame(16, $closed->attempts()->count());
        $this->assertSame(15, $open->attempts()->count());
        $this->assertSame(3, ToeflAttempt::where('status', ToeflAttempt::STATUS_IN_PROGRESS)->count());

        // Seluruh attempt yang selesai sudah dinilai mesin penilaian asli.
        $finished = ToeflAttempt::where('status', '!=', ToeflAttempt::STATUS_IN_PROGRESS)->count();
        $this->assertSame($finished, ToeflResult::count());
        $this->assertGreaterThan(0, ToeflAnswer::count());

        $scaled = ToeflResult::pluck('scaled_score');
        $this->assertGreaterThanOrEqual((int) config('toefl.scoring.min_scaled'), $scaled->min());
        $this->assertLessThanOrEqual((int) config('toefl.scoring.max_scaled'), $scaled->max());

        // Sesi berjalan belum dinilai dan masih dalam batas waktu server.
        foreach (ToeflAttempt::where('status', ToeflAttempt::STATUS_IN_PROGRESS)->get() as $attempt) {
            $this->assertNull($attempt->result);
            $this->assertTrue($attempt->deadline_at->isFuture());
        }

        // Ujian terbuka memiliki kode akses yang masih berlaku.
        $this->assertGreaterThan(0, $open->accessCodes()->whereNull('revoked_at')->count());
    }

    public function test_seeder_idempoten(): void
    {
        $this->seed(ToeflDummySeeder::class);

        $before = $this->counts();

        $this->seed(ToeflDummySeeder::class);

        $this->assertSame($before, $this->counts());
    }

    /**
     * @return array<int, int>
     */
    protected function counts(): array
    {
        return [
            ToeflExam::count(),
            ToeflQuestion::count(),
            ToeflStudent::count(),
            ToeflAttempt::count(),
            ToeflResult::count(),
        ];
    }
}
