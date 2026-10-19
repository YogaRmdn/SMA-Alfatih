<?php

namespace Tests\Feature;

use App\Models\SibelClass;
use App\Models\SibelOption;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\User;
use App\Services\LearningStyleScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Laporan & rekap SIBEL (rencana proyek — bagian 11, skenario 11, 14, 15).
 */
class SibelReportTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected User $guruBk;

    protected SibelQuestionnaire $questionnaire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->guruBk = $this->createSibelUser('guru_bk', 'Guru BK Laporan');
        $this->questionnaire = $this->createQuestionnaire(4);
    }

    /**
     * Siswa + sesi selesai sesuai pola; hasil langsung dipublikasikan.
     */
    protected function completedResult(
        string $name,
        string $pattern,
        ?SibelClass $class = null,
        bool $published = true
    ): SibelResult {
        $user = $this->createSibelUser('siswa', $name);
        $student = $this->createStudentFor($user, $class ?? SibelClass::create(['name' => 'X-A']));
        $attempt = $this->createAttempt($student, $this->questionnaire, $pattern);

        $result = app(LearningStyleScoringService::class)->scoreAndPersist($attempt);

        if ($published) {
            $result->update(['is_published' => true]);
        }

        return $result;
    }

    public function test_laporan_cetak_memuat_identitas_skor_kesimpulan_dan_rekomendasi(): void
    {
        $result = $this->completedResult('Budi Santoso', 'AAAB');

        $this->actingAs($this->guruBk)
            ->get(route('sibel.results.print', $result))
            ->assertOk()
            // Identitas.
            ->assertSee('Laporan Hasil Asesmen Gaya Belajar Siswa')
            ->assertSee('Budi Santoso')
            ->assertSee('X-A')
            // Skor & persentase (3 visual dari 4 jawaban = 75%).
            ->assertSee('Total jawaban valid')
            ->assertSee('75%')
            ->assertSee('25%')
            // Kesimpulan, interpretasi, rekomendasi.
            ->assertSee('Kesimpulan')
            ->assertSee('Kecenderungan utama: Visual.')
            ->assertSee('Rekomendasi Strategi Belajar')
            // Etika & blok tanda tangan guru BK.
            ->assertSee('bukan diagnosis psikologis')
            ->assertSee('Guru Bimbingan dan Konseling');
    }

    public function test_cetak_rekap_memuat_tabel_siswa_dan_distribusi(): void
    {
        $this->completedResult('Siti Aminah', 'AAAA');

        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.print'))
            ->assertOk()
            ->assertSee('Rekapitulasi Hasil Gaya Belajar Siswa')
            ->assertSee('Siti Aminah')
            ->assertSee('bukan diagnosis psikologis');
    }

    public function test_pembaruan_instrumen_tidak_mengubah_hasil_historis(): void
    {
        $result = $this->completedResult('Andi Wijaya', 'AAAB', null, false);

        $interpretationBefore = $result->fresh()->interpretation;
        $recommendationBefore = $result->fresh()->recommendation;

        // Guru BK memperbarui instrumen: judul, versi, teks opsi, tambah pertanyaan.
        $this->questionnaire->update([
            'title' => 'Kuesioner Gaya Belajar (Revisi 2)',
            'version' => '2.0',
        ]);

        SibelOption::whereIn('question_id', $this->questionnaire->questions()->pluck('id'))
            ->update(['option_text' => 'TEKS OPSI YANG BARU']);

        $newQuestion = SibelQuestion::create([
            'questionnaire_id' => $this->questionnaire->id,
            'question_text' => 'Pertanyaan tambahan hasil revisi.',
            'order_number' => 99,
            'is_required' => true,
        ]);

        foreach (config('sibel.code_category') as $code => $category) {
            SibelOption::create([
                'question_id' => $newQuestion->id,
                'option_code' => $code,
                'option_text' => "Baru {$code}",
                'category_code' => $category,
            ]);
        }

        // Skenario 14: hasil historis tetap persis seperti saat dihitung.
        $this->assertDatabaseHas('results', [
            'attempt_id' => $result->attempt_id,
            'score_a' => 3,
            'score_b' => 1,
            'score_c' => 0,
            'questionnaire_version' => '1.0',
        ]);
        $this->assertSame($interpretationBefore, $result->fresh()->interpretation);
        $this->assertSame($recommendationBefore, $result->fresh()->recommendation);
        $this->assertSame(1, SibelResult::count());
    }

    public function test_rekap_selaras_dengan_data_hasil_terkirim(): void
    {
        $this->completedResult('Rina Marlina', 'AAAA');
        $this->completedResult('Dedi Kurniawan', 'AAAA');
        $this->completedResult('Maya Sari', 'BBBB');
        $this->completedResult('Rudi Hartono', 'BBCC'); // seri B & C

        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.index'))
            ->assertOk()
            ->assertViewHas('total', 4)
            ->assertViewHas('distribution', ['visual' => 2, 'auditori' => 1, 'kinestetik' => 0])
            ->assertViewHas('ties', 1)
            ->assertSee('Rina Marlina')
            ->assertSee('Rudi Hartono')
            ->assertSee('Seri');
    }

    public function test_rekap_menggunakan_hasil_terakhir_tiap_siswa(): void
    {
        $user = $this->createSibelUser('siswa', 'Ganda Pratama');
        $student = $this->createStudentFor($user);

        $scoring = app(LearningStyleScoringService::class);

        // Riwayat 1: visual. Riwayat 2 (terbaru): kinestetik.
        $first = $this->createAttempt($student, $this->questionnaire, 'AAAA');
        $scoring->scoreAndPersist($first);

        $second = $this->createAttempt($student, $this->questionnaire, 'CCCC');
        $scoring->scoreAndPersist($second);

        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.index'))
            ->assertOk()
            ->assertViewHas('total', 1)
            ->assertViewHas('distribution', ['visual' => 0, 'auditori' => 0, 'kinestetik' => 1]);
    }

    public function test_rekap_dapat_difilter_per_kelas_dan_periode(): void
    {
        $kelasA = SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027']);
        $kelasB = SibelClass::create(['name' => 'X-B', 'academic_year' => '2026/2027']);

        $this->completedResult('Agus Kelas A', 'AAAA', $kelasA);
        $this->completedResult('Bagas Kelas B', 'BBBB', $kelasB);

        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.index', ['class_id' => $kelasA->id]))
            ->assertOk()
            ->assertViewHas('total', 1)
            ->assertSee('Agus Kelas A')
            ->assertDontSee('Bagas Kelas B');

        // Periode bulan berjalan: kedua hasil masuk.
        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.index', ['period' => now()->format('Y-m')]))
            ->assertOk()
            ->assertViewHas('total', 2);

        // Periode lama: tidak ada hasil.
        $this->actingAs($this->guruBk)
            ->get(route('sibel.recap.index', ['period' => '2000-01']))
            ->assertOk()
            ->assertViewHas('total', 0);
    }

    public function test_daftar_hasil_dapat_disaring_catatan_dan_publikasi_disimpan(): void
    {
        $result = $this->completedResult('Citra Dewi', 'AAAA', null, false);
        $this->completedResult('Eko Prasetyo', 'BBBB');

        $this->actingAs($this->guruBk)
            ->get(route('sibel.results.index', ['q' => 'Citra']))
            ->assertOk()
            ->assertViewHas('results', fn ($results) => $results->total() === 1)
            ->assertSee('Citra Dewi')
            ->assertDontSee('Eko Prasetyo');

        // Simpan catatan + publikasikan.
        $this->actingAs($this->guruBk)
            ->put(route('sibel.results.update', $result), [
                'is_published' => 1,
                'teacher_note' => 'Dampingi dengan peta konsep visual.',
            ])
            ->assertRedirect(route('sibel.results.show', $result));

        $this->assertDatabaseHas('results', [
            'id' => $result->id,
            'teacher_note' => 'Dampingi dengan peta konsep visual.',
            'is_published' => true,
        ]);

        // Batalkan publikasi — siswa kembali tidak melihat hasil.
        $this->actingAs($this->guruBk)
            ->put(route('sibel.results.update', $result), [
                'is_published' => 0,
                'teacher_note' => 'Dampingi dengan peta konsep visual.',
            ]);

        $this->assertDatabaseHas('results', ['id' => $result->id, 'is_published' => false]);
    }

    public function test_halaman_detail_hasil_menampilkan_jawaban_per_pertanyaan(): void
    {
        $result = $this->completedResult('Fajar Nugroho', 'AAAB');

        $this->actingAs($this->guruBk)
            ->get(route('sibel.results.show', $result))
            ->assertOk()
            ->assertSee('Fajar Nugroho')
            ->assertSee('Review Jawaban')
            ->assertSee('Pertanyaan ke-1')
            ->assertSee('Tindak Lanjut Guru BK');
    }

    public function test_pembuatan_kuesioner_baru_membawa_status_draft_dan_perlu_verifikasi(): void
    {
        $this->actingAs($this->guruBk)
            ->post(route('sibel.questionnaires.store'), [
                'title' => 'Kuesioner Baru Uji',
                'instructions' => 'Baca petunjuk dengan teliti.',
                'version' => '1.0',
                'status' => SibelQuestionnaire::STATUS_DRAFT,
                'allow_retake' => 0,
                'needs_verification' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('questionnaires', [
            'title' => 'Kuesioner Baru Uji',
            'status' => SibelQuestionnaire::STATUS_DRAFT,
            'needs_verification' => true,
        ]);
    }
}
