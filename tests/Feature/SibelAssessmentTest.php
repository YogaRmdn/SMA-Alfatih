<?php

namespace Tests\Feature;

use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\SibelStudent;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Alur pengisian kuesioner sisi siswa TANPA LOGIN (portal /sibel):
 * tulis nama + pilih kelas + jawab pertanyaan dalam satu halaman; hasil
 * langsung tampil & bisa diunduh begitu jawaban dikirim.
 */
class SibelAssessmentTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected SibelQuestionnaire $questionnaire;

    protected SibelClass $class;

    protected SibelStudent $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->questionnaire = $this->createQuestionnaire(4);
        $this->class = SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027']);
        $this->student = $this->createStandaloneStudent('Siswa Satu', $this->class);
    }

    /**
     * Payload kirim jawaban: identitas (nama + kelas) + jawaban lengkap
     * sesuai pola kode ("AAAB" dst — satu karakter per pertanyaan, berurutan).
     */
    protected function payload(string $name, string $pattern): array
    {
        $answers = [];

        foreach ($this->questionnaire->questions()->with('options')->get() as $i => $question) {
            $option = $question->options->firstWhere('option_code', strtoupper($pattern[$i] ?? 'A'));
            $answers[$question->id] = $option->id;
        }

        return ['full_name' => $name, 'class_id' => $this->class->id, 'answers' => $answers];
    }

    public function test_pengiriman_lengkap_menghasilkan_skor_benar(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'AAAB'))
            ->assertRedirect(route('sibel.assessment.result', SibelAttempt::firstOrFail()))
            ->assertSessionHas('sibel_student_id', $this->student->id);

        $attempt = SibelAttempt::firstOrFail();

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
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'AAAA'));

        $this->assertSame(1, SibelResult::count());

        // Percobaan kirim kedua (tanpa retake) dialihkan ke hasil lama.
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'BBBB'))
            ->assertRedirect(route('sibel.assessment.result', SibelAttempt::firstOrFail()));

        $this->assertSame(1, SibelResult::count());
        $this->assertSame(4, SibelAnswer::count());
        $this->assertSame(4, SibelResult::first()->score_a);
    }

    public function test_identitas_lain_tidak_bisa_membuka_hasil_orang_lain(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'AAAA'));

        $attempt = SibelAttempt::firstOrFail();
        $siswaLain = $this->createStandaloneStudent('Siswa Dua', SibelClass::first());

        $this->actingAsStudent($siswaLain)->get(route('sibel.assessment.result', $attempt))->assertForbidden();
        $this->actingAsStudent($siswaLain)->get(route('sibel.assessment.print', $attempt))->assertForbidden();

        // Tanpa ikatan sesi sama sekali pun tetap ditolak.
        $this->flushSession();
        $this->get(route('sibel.assessment.result', $attempt))->assertForbidden();

        // Pemilik sesi tetap boleh membuka miliknya sendiri.
        $this->actingAsStudent($this->student)->get(route('sibel.assessment.result', $attempt))->assertOk();
    }

    public function test_pengisian_ulang_mengikuti_aturan_guru_bk(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'AAAA'));

        // Default allow_retake=false: pengisian ulang ditolak.
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'BBBB'));

        $this->assertSame(1, SibelAttempt::count());

        // Guru BK membuka kembali aturan pengisian ulang.
        $this->questionnaire->update(['allow_retake' => true]);

        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'BBBB'))
            ->assertRedirect(route('sibel.assessment.result', SibelAttempt::orderByDesc('id')->first()));

        $this->assertSame(2, SibelAttempt::count());
        $this->assertSame(2, SibelResult::count());

        // Hasil terbaru mencerminkan jawaban pengisian ulang.
        $this->assertDatabaseHas('results', [
            'attempt_id' => SibelAttempt::orderByDesc('id')->first()->id,
            'score_b' => 4,
        ]);
    }

    public function test_hasil_langsung_tampil_dan_bisa_dicetak_tanpa_publikasi(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Siswa Satu', 'AAAA'));

        $attempt = SibelAttempt::firstOrFail();

        // Tanpa publikasi apa pun: siswa langsung melihat hasil lengkap.
        $this->actingAsStudent($this->student)
            ->get(route('sibel.assessment.result', $attempt))
            ->assertOk()
            ->assertSee('Kecenderungan utama')
            ->assertSee('Visual');

        // Unduh (cetak / simpan PDF) juga langsung tersedia.
        $this->actingAsStudent($this->student)
            ->get(route('sibel.assessment.print', $attempt))
            ->assertOk()
            ->assertSee('Laporan Hasil Asesmen Gaya Belajar Siswa');

        // Guru BK tetap bisa menambahkan catatan yang tampil di hasil siswa.
        $guru = $this->createSibelUser('guru_bk', 'Guru BK');
        $result = SibelResult::where('attempt_id', $attempt->id)->firstOrFail();

        $this->actingAs($guru)
            ->put(route('sibel.results.update', $result), [
                'teacher_note' => 'Teruskan ke orang tua untuk diskusi rutin.',
            ])
            ->assertRedirect(route('sibel.results.show', $result));

        $this->actingAsStudent($this->student)
            ->get(route('sibel.assessment.result', $attempt))
            ->assertOk()
            ->assertSee('Teruskan ke orang tua untuk diskusi rutin.');
    }

    public function test_jawaban_ganda_pada_pertanyaan_sama_dicegah_db(): void
    {
        $attempt = $this->createAttempt($this->student, $this->questionnaire, null, SibelAttempt::STATUS_IN_PROGRESS);
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
