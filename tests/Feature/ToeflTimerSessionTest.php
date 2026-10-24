<?php

namespace Tests\Feature;

use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflResult;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Timer berbasis server, auto-finalize kedaluwarsa, dan sesi tunggal:
 *  - deadline dihitung dari timestamp server (bukan jam peserta),
 *  - attempt lewat deadline otomatis dinilai & ditutup,
 *  - satu attempt per (peserta, ujian) — unique index menolak duplikasi,
 *  - koneksi terputus -> masuk ulang dengan nama + kelas sama, resume
 *    attempt yang sama (tanpa attempt ganda).
 */
class ToeflTimerSessionTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected ToeflExam $exam;

    protected string $code;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exam = $this->buildExam(2)['exam'];
        $this->code = $this->issueCode($this->exam);
    }

    public function test_sesi_ganda_ditolak_database_satu_attempt_per_peserta_per_ujian(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);

        // Percobaan membuat attempt kedua untuk (exam, student) yang sama
        // langsung ke database -> dilanggar unique index.
        $this->expectException(UniqueConstraintViolationException::class);

        ToeflAttempt::create([
            'exam_id' => $attempt->exam_id,
            'student_id' => $attempt->student_id,
            'status' => ToeflAttempt::STATUS_IN_PROGRESS,
        ]);
    }

    public function test_koneksi_terputus_mengembalikan_attempt_yang_sama(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);
        $versionBefore = $attempt->refresh()->session_version;

        // Simulasikan koneksi putus: session hilang -> masuk ulang dengan kode sama.
        $this->flushSession();

        $resumed = $this->enterExam($this->code, $this->exam);

        $this->assertSame($attempt->id, $resumed->id, 'Resume, bukan attempt baru.');
        $this->assertSame(1, ToeflAttempt::count());
        $this->assertGreaterThan(
            $versionBefore,
            $resumed->refresh()->session_version,
            'Versi sesi naik -> indikator resume terjadi.'
        );
    }

    public function test_masuk_ulang_setelah_mulai_langsung_ke_bagian_ujian(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $this->flushSession();

        // Masuk ulang (identitas sama): langsung diteruskan ke bagian terakhir (bukan instruksi).
        $this->post(route('toefl.enter'), [
            'full_name' => 'Siswa Uji',
            'class_name' => 'X-1',
            'exam_id' => $this->exam->id,
            'code' => $this->code,
        ])->assertRedirect(route('toefl.exam.section', 'listening'));
    }

    public function test_deadline_lewat_mengauto_finalisasi_pada_akses_berikutnya(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        // Mundurkan deadline ke masa lalu (jam server).
        ToeflAttempt::whereKey($attempt->id)->update(['deadline_at' => now()->subMinute()]);

        $this->get(route('toefl.exam.section', 'listening'))
            ->assertRedirect(route('toefl.result'));

        $attempt->refresh();

        $this->assertSame(ToeflAttempt::STATUS_EXPIRED, $attempt->status);
        $this->assertNotNull($attempt->submitted_at);

        // Hasil tetap dihitung dari jawaban tersimpan
        // (2 soal x 3 bagian = 6 soal; tidak ada jawaban tersimpan).
        $this->assertNotNull($attempt->result);
        $this->assertSame(6, $attempt->result->total_questions);
        $this->assertSame(0, $attempt->result->total_correct);
    }

    public function test_jawaban_setelah_deadline_ditolak_dan_hasil_dikunci(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        $question = $this->allQuestions($this->exam)->first();
        ToeflAttempt::whereKey($attempt->id)->update(['deadline_at' => now()->subMinute()]);

        $this->postJson(route('toefl.exam.answer'), [
            'question_id' => $question->id,
            'selected_answer' => 'A',
        ])
            ->assertStatus(422)
            ->assertJsonPath('expired', true);

        // Attempt sudah dinilai; jawaban terlambat TIDAK masuk.
        $this->assertSame(ToeflAttempt::STATUS_EXPIRED, $attempt->refresh()->status);
        $this->assertSame(0, $attempt->answers()->count());
        $this->assertSame(0, $attempt->result->total_correct);
    }

    public function test_submit_manual_setelah_deadline_berstatus_expired(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        ToeflAttempt::whereKey($attempt->id)->update(['deadline_at' => now()->subMinute()]);

        $this->post(route('toefl.exam.submit'))->assertRedirect(route('toefl.result'));

        $this->assertSame(ToeflAttempt::STATUS_EXPIRED, $attempt->refresh()->status);
        $this->assertSame(1, ToeflResult::where('attempt_id', $attempt->id)->count());
    }

    public function test_hasil_tidak_ditampilkan_saat_ujian_masih_berjalan(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        // Belum dikumpulkan -> halaman hasil mengalihkan ke bagian ujian.
        $this->get(route('toefl.result'))
            ->assertRedirect(route('toefl.exam.section', 'listening'));
    }

    public function test_aksi_ujian_ditolak_bila_ujian_ditutup_pengawas(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        // Pengawas menutup ujian di tengah sesi -> submit tetap menilai
        // (finalize tidak memeriksa status ujian; keputusan ada di server),
        // tetapi percobaan masuk ulang lewat kode DITOLAK.
        $this->exam->update(['status' => ToeflExam::STATUS_CLOSED]);
        $this->flushSession();

        $this->post(route('toefl.enter'), [
            'full_name' => 'Siswa Uji',
            'class_name' => 'X-1',
            'exam_id' => $this->exam->id,
            'code' => $this->code,
        ])->assertSessionHasErrors('code');
    }
}
