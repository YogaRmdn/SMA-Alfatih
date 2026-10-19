<?php

namespace Tests\Feature;

use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Alur pengisian kuesioner sisi siswa (rencana proyek — bagian 11:
 * skenario 7, 8, 13, aturan pengisian ulang, dan gerbang publikasi).
 */
class SibelAssessmentTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected SibelQuestionnaire $questionnaire;

    protected SibelStudent $student;

    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->questionnaire = $this->createQuestionnaire(4);
        $this->siswa = $this->createSibelUser('siswa', 'Siswa Satu');
        $this->student = $this->createStudentFor(
            $this->siswa,
            SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027'])
        );
    }

    /**
     * Payload jawaban lengkap sesuai pola kode ("AAAB" dst).
     */
    protected function payload(string $pattern): array
    {
        $answers = [];

        foreach ($this->questionnaire->questions()->with('options')->get() as $i => $question) {
            $option = $question->options->firstWhere('option_code', strtoupper($pattern[$i] ?? 'A'));
            $answers[$question->id] = $option->id;
        }

        return ['answers' => $answers];
    }

    protected function startAttempt(): SibelAttempt
    {
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire));

        return SibelAttempt::firstOrFail();
    }

    public function test_siswa_tanpa_profil_melihat_pemberitahuan(): void
    {
        $tanpaProfil = $this->createSibelUser('siswa', 'Siswa Tanpa Data');

        $this->actingAs($tanpaProfil)
            ->get(route('sibel.assessment.index'))
            ->assertOk()
            ->assertSee('Data Siswa Belum Terdaftar');
    }

    public function test_memulai_kuesioner_membuat_sesi_tunggal(): void
    {
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire))
            ->assertRedirect(route('sibel.assessment.show', SibelAttempt::first()));

        $this->assertDatabaseHas('attempts', [
            'student_id' => $this->student->id,
            'questionnaire_id' => $this->questionnaire->id,
            'status' => SibelAttempt::STATUS_IN_PROGRESS,
        ]);

        // Klik ganda / muat ulang: tidak membuat sesi kedua.
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire));

        $this->assertSame(1, SibelAttempt::count());
    }

    public function test_jawaban_kosong_ditolak_dan_sesi_tetap_berjalan(): void
    {
        $attempt = $this->startAttempt();

        $payload = $this->payload('AAAA');
        $questionIds = $this->questionnaire->questions()->pluck('id');
        unset($payload['answers'][$questionIds->last()]); // satu pertanyaan kosong

        // Buka form dulu agar alur mirip browser (redirect kembali ke form).
        $this->actingAs($this->siswa)->get(route('sibel.assessment.show', $attempt));

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $payload)
            ->assertRedirect(route('sibel.assessment.show', $attempt))
            ->assertSessionHasErrors(["answers.{$questionIds->last()}"]);

        // Skenario 7: pengiriman tidak lengkap ditolak — sesi belum selesai, tanpa hasil.
        $this->assertSame(SibelAttempt::STATUS_IN_PROGRESS, $attempt->fresh()->status);
        $this->assertSame(0, SibelResult::count());
        $this->assertSame(0, SibelAnswer::count());
    }

    public function test_pengiriman_lengkap_menghasilkan_skor_benar(): void
    {
        $attempt = $this->startAttempt();

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAB'))
            ->assertRedirect(route('sibel.assessment.result', $attempt));

        $attempt->refresh();

        $this->assertSame(SibelAttempt::STATUS_COMPLETED, $attempt->status);
        $this->assertNotNull($attempt->completed_at);

        $this->assertDatabaseHas('results', [
            'attempt_id' => $attempt->id,
            'score_a' => 3,
            'score_b' => 1,
            'score_c' => 0,
            'result_status' => SibelResult::STATUS_DOMINANT,
            'questionnaire_version' => '1.0',
            'is_published' => false,
        ]);
    }

    public function test_kirim_berulang_tidak_membuat_hasil_ganda(): void
    {
        $attempt = $this->startAttempt();

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAA'));

        $this->assertSame(1, SibelResult::count());

        // Skenario 13: percobaan kirim kedua tidak mengubah apa pun.
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAA'))
            ->assertRedirect(route('sibel.assessment.index'));

        $this->assertSame(1, SibelResult::count());
        $this->assertSame(4, SibelAnswer::count());
        $this->assertSame(4, SibelResult::first()->score_a);
    }

    public function test_opsi_milik_pertanyaan_lain_ditolak(): void
    {
        $attempt = $this->startAttempt();

        $questions = $this->questionnaire->questions()->with('options')->get();
        $payload = $this->payload('AAAA');

        // Skenario 8: opsi pertanyaan #2 dipaksakan ke pertanyaan #1.
        $payload['answers'][$questions[0]->id] = $questions[1]->options->first()->id;

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $payload)
            ->assertSessionHasErrors(["answers.{$questions[0]->id}"]);

        $this->assertSame(SibelAttempt::STATUS_IN_PROGRESS, $attempt->fresh()->status);
        $this->assertSame(0, SibelResult::count());
    }

    public function test_siswa_lain_tidak_bisa_membuka_sesi_orang_lain(): void
    {
        $attempt = $this->startAttempt();

        $siswaLain = $this->createSibelUser('siswa', 'Siswa Dua');
        $this->createStudentFor($siswaLain, SibelClass::first());

        $this->actingAs($siswaLain)->get(route('sibel.assessment.show', $attempt))->assertForbidden();
        $this->actingAs($siswaLain)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAA'))
            ->assertForbidden();
        $this->actingAs($siswaLain)->get(route('sibel.assessment.result', $attempt))->assertForbidden();
        $this->actingAs($siswaLain)->get(route('sibel.assessment.print', $attempt))->assertForbidden();
    }

    public function test_sesi_ditutup_tidak_bisa_dimulai(): void
    {
        $this->questionnaire->update(['status' => SibelQuestionnaire::STATUS_CLOSED]);

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire))
            ->assertRedirect(route('sibel.assessment.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, SibelAttempt::count());
    }

    public function test_pengisian_ulang_mengikuti_atasan_guru_bk(): void
    {
        $attempt = $this->startAttempt();
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAA'));

        // Default allow_retake=false: pengisian ulang ditolak.
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire))
            ->assertRedirect(route('sibel.assessment.index'))
            ->assertSessionHas('error');

        $this->assertSame(1, SibelAttempt::count());

        // Guru BK membuka kembali aturan pengisian ulang.
        $this->questionnaire->update(['allow_retake' => true]);

        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.start', $this->questionnaire))
            ->assertRedirect();

        $this->assertSame(2, SibelAttempt::count());
        $this->assertSame(
            SibelAttempt::STATUS_IN_PROGRESS,
            SibelAttempt::orderByDesc('id')->first()->status
        );
    }

    public function test_hasil_tersembunyi_sampai_guru_bk_mempublikasikan(): void
    {
        $attempt = $this->startAttempt();
        $this->actingAs($this->siswa)
            ->post(route('sibel.assessment.submit', $attempt), $this->payload('AAAA'));

        // Sebelum publikasi: halaman status, interpretasi tidak tampil.
        $this->actingAs($this->siswa)
            ->get(route('sibel.assessment.result', $attempt))
            ->assertOk()
            ->assertSee('sudah kami terima dan sedang ditinjau')
            ->assertDontSee('Kecenderungan utama');

        // Cetak juga diblokir sebelum publikasi.
        $this->actingAs($this->siswa)
            ->get(route('sibel.assessment.print', $attempt))
            ->assertForbidden();

        // Guru BK mempublikasikan hasil.
        $guru = $this->createSibelUser('guru_bk', 'Guru BK');
        $result = SibelResult::where('attempt_id', $attempt->id)->firstOrFail();

        $this->actingAs($guru)
            ->put(route('sibel.results.update', $result), [
                'is_published' => 1,
                'teacher_note' => 'Teruskan ke orang tua untuk diskusi rutin.',
            ])
            ->assertRedirect(route('sibel.results.show', $result));

        $this->assertTrue($result->fresh()->is_published);

        // Setelah publikasi: siswa melihat hasil lengkap dan bisa mencetak.
        $this->actingAs($this->siswa)
            ->get(route('sibel.assessment.result', $attempt))
            ->assertOk()
            ->assertSee('Kecenderungan utama')
            ->assertSee('Visual');

        $this->actingAs($this->siswa)
            ->get(route('sibel.assessment.print', $attempt))
            ->assertOk()
            ->assertSee('Laporan Hasil Asesmen Gaya Belajar Siswa');
    }

    public function test_jawaban_ganda_pada_pertanyaan_sama_dicegah_db(): void
    {
        $attempt = $this->startAttempt();
        $question = $this->questionnaire->questions()->with('options')->get()->first();
        $option = $question->options->first();

        SibelAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $option->id,
        ]);

        $this->expectException(QueryException::class);

        // Unique constraint (attempt_id, question_id) menolak jawaban ganda.
        SibelAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'option_id' => $question->options->last()->id,
        ]);
    }
}
