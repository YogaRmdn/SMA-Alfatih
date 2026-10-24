<?php

namespace Tests\Feature;

use App\Models\ToeflAnswer;
use App\Models\ToeflAttempt;
use App\Models\ToeflEvent;
use App\Models\ToeflExam;
use App\Models\ToeflResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Alur ujian penuh tanpa login: identitas + kode -> instruksi -> mulai
 * (deadline server) -> kerjakan (auto-save) -> peristiwa browser ->
 * kumpulkan -> hasil terscore di server.
 */
class ToeflExamFlowTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected ToeflExam $exam;

    protected string $code;

    protected string $name = 'Cahya Pratama';

    protected function setUp(): void
    {
        parent::setUp();

        $this->exam = $this->buildExam(3)['exam'];
        $this->code = $this->issueCode($this->exam);
    }

    public function test_instruksi_ditampilkan_sebelum_waktu_dimulai(): void
    {
        $this->enterExam($this->code, $this->exam, $this->name, 'XII-1');

        $this->get(route('toefl.instructions'))
            ->assertOk()
            ->assertSee('Instruksi Ujian')
            ->assertSee($this->name)
            ->assertSee((string) config('toefl.audio.max_plays'));
    }

    public function test_konfirmasi_mulai_menyetel_deadline_dari_server(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $attempt->refresh();

        $this->assertNotNull($attempt->started_at);
        $this->assertNotNull($attempt->deadline_at);

        $examDuration = $this->exam->refresh()->duration_seconds;
        $expectedDeadline = $attempt->started_at->copy()
            ->addSeconds($examDuration + (int) config('toefl.timer.grace_seconds'));

        $this->assertEqualsWithDelta(
            $expectedDeadline->timestamp,
            $attempt->deadline_at->timestamp,
            5,
            'Deadline harus = mulai + durasi ujian + grace (dari timestamp server).'
        );
    }

    public function test_halaman_bagian_menampilkan_soal_tanpa_kunci_jawaban(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $response = $this->get(route('toefl.exam.section', 'listening'));

        $response->assertOk()->assertSee('Listening Comprehension');

        // Kunci jawaban tidak pernah bocor ke halaman siswa.
        foreach ($this->allQuestions($this->exam) as $question) {
            $response->assertDontSee('"answer_key":"'.$question->answer_key.'"');
        }
        $response->assertDontSee('answer_key');
    }

    public function test_auto_save_jawaban_persisten_pada_sesi_ini(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $question = $this->allQuestions($this->exam)->first();

        $this->postJson(route('toefl.exam.answer'), [
            'question_id' => $question->id,
            'selected_answer' => 'B',
        ])->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('toefl_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'selected_answer' => 'B',
        ]);

        // Muat ulang halaman -> jawaban tetap ada (auto-save).
        $this->get(route('toefl.exam.section', $question->section->section_type))
            ->assertOk();

        $this->assertSame(1, ToeflAnswer::where('attempt_id', $attempt->id)->count());
    }

    public function test_simpan_jawaban_pada_soal_ujian_lain_ditolak(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        // Ujian lain + soalnya.
        $otherExam = $this->buildExam(1, ['title' => 'Ujian Asing'])['exam'];
        $foreignQuestion = $this->allQuestions($otherExam)->first();

        $this->postJson(route('toefl.exam.answer'), [
            'question_id' => $foreignQuestion->id,
            'selected_answer' => 'A',
        ])->assertStatus(422);

        $this->assertSame(0, ToeflAnswer::count());
    }

    public function test_simpan_jawaban_tanpa_session_ditolak(): void
    {
        $question = $this->allQuestions($this->exam)->first();

        $this->postJson(route('toefl.exam.answer'), [
            'question_id' => $question->id,
            'selected_answer' => 'A',
        ])->assertStatus(401);
    }

    public function test_pilihan_jawaban_diluar_kode_sah_ditolak(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $question = $this->allQuestions($this->exam)->first();

        $this->postJson(route('toefl.exam.answer'), [
            'question_id' => $question->id,
            'selected_answer' => 'Z',
        ])->assertStatus(422);
    }

    public function test_peristiwa_browser_dicatat(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $this->postJson(route('toefl.exam.event'), ['event_type' => 'tab_hidden'])
            ->assertOk();

        $this->postJson(route('toefl.exam.event'), ['event_type' => 'fullscreen_exit'])
            ->assertOk();

        $this->assertSame(2, ToeflEvent::where('attempt_id', $attempt->id)->count());
    }

    public function test_peristiwa_diluar_daftar_jenis_ditolak(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $this->postJson(route('toefl.exam.event'), ['event_type' => 'cheating_proof'])
            ->assertStatus(422);
    }

    public function test_pengumpulan_menghitung_skor_di_server(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $questions = $this->allQuestions($this->exam);

        // Jawab: sepertiga pertama benar (A), sisanya salah (B).
        foreach ($questions as $i => $question) {
            $this->postJson(route('toefl.exam.answer'), [
                'question_id' => $question->id,
                'selected_answer' => $i < 3 ? 'A' : 'B',
            ])->assertOk();
        }

        $this->post(route('toefl.exam.submit'))
            ->assertRedirect(route('toefl.result'));

        $attempt->refresh();

        $this->assertSame(ToeflAttempt::STATUS_SUBMITTED, $attempt->status);
        $this->assertNotNull($attempt->submitted_at);

        $result = ToeflResult::where('attempt_id', $attempt->id)->firstOrFail();

        // allQuestions() terurut id: soal Listening (3) dibuat pertama, lalu
        // Structure (3), lalu Reading (3) -> total 9 soal.
        // 3 soal pertama (= seluruh Listening) dijawab 'A' (kunci), sisanya 'B'.
        $this->assertSame(9, $result->total_questions);
        $this->assertSame(3, $result->total_correct);
        $this->assertSame(3, $result->listening_raw_score);
        $this->assertSame(0, $result->structure_raw_score);
        $this->assertSame(0, $result->reading_raw_score);

        // Skor scaled harus pada rentang aproksimasi 310-680.
        $this->assertGreaterThanOrEqual(310, $result->scaled_score);
        $this->assertLessThanOrEqual(680, $result->scaled_score);
    }

    public function test_hasil_tampil_di_halaman_siswa(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        foreach ($this->allQuestions($this->exam) as $question) {
            $this->postJson(route('toefl.exam.answer'), [
                'question_id' => $question->id,
                'selected_answer' => 'A',
            ])->assertOk();
        }

        $this->post(route('toefl.exam.submit'));

        $this->get(route('toefl.result'))
            ->assertOk()
            ->assertSee('Hasil Ujian TOEFL')
            ->assertSee($attempt->refresh()->result->scaled_score.'');

        $result = $attempt->result;
        $this->assertSame($result->total_questions, $result->total_correct, 'Semua jawaban A = kunci A -> semua benar.');
    }

    public function test_ujian_dikumpulkan_ulang_setelah_selesai_tidak_menilai_ulang(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $this->post(route('toefl.exam.submit'));
        $firstScoredAt = $attempt->refresh()->result->completed_at;

        // Submit kedua — idempoten, tidak mengubah hasil.
        $this->post(route('toefl.exam.submit'))->assertRedirect(route('toefl.result'));

        $this->assertSame(
            $firstScoredAt->timestamp,
            $attempt->refresh()->result->refresh()->completed_at->timestamp
        );
        $this->assertSame(1, ToeflResult::count());
    }
}
