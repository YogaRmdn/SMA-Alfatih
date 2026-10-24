<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\User;
use App\Services\LearningStyleScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Reader\XLSX\Reader;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Metode penilaian Skala Likert + unduhan hasil XLSX.
 */
class SibelLikertTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected User $guruBk;

    protected SibelQuestionnaire $questionnaire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->guruBk = $this->createSibelUser('guru_bk', 'Guru BK Likert');
        // 6 pernyataan = 2 per kategori (V-A-K berurutan), skor maks 8 per kategori.
        $this->questionnaire = $this->createLikertQuestionnaire(6);
    }

    /**
     * Sesi selesai + skor Likert tersimpan.
     */
    protected function scoredAttempt(string $name, string $pattern, ?SibelClass $class = null): SibelResult
    {
        $student = $this->createStandaloneStudent($name, $class);
        $attempt = $this->createAttempt($student, $this->questionnaire, $pattern);

        return app(LearningStyleScoringService::class)->scoreAndPersist($attempt);
    }

    public function test_skoring_likert_menjumlahkan_nilai_opsi_terhadap_skor_maksimum(): void
    {
        // P1 Visual(A=4), P2 Auditori(A=4), P3 Kine(B=3), P4 Visual(B=3),
        // P5 Auditori(C=2), P6 Kine(D=1) -> V=7, A=6, K=4; maks per kategori 8.
        $result = $this->scoredAttempt('Siti Aminah', 'AABBCD');

        $this->assertSame(7, $result->score_a);
        $this->assertSame(6, $result->score_b);
        $this->assertSame(4, $result->score_c);

        // Persentase terhadap skor maksimum (2 pernyataan x 4 poin = 8).
        $this->assertSame(87.5, $result->percentage_a);
        $this->assertSame(75.0, $result->percentage_b);
        $this->assertSame(50.0, $result->percentage_c);

        $this->assertSame(['visual'], $result->dominant_categories);
        $this->assertSame(SibelResult::STATUS_DOMINANT, $result->result_status);
        $this->assertStringContainsString('Kecenderungan utama: Visual', $result->interpretation);
    }

    public function test_skoring_likert_mendeteksi_seri_bila_skor_sama(): void
    {
        // Semua pernyataan dijawab "Sangat Sesuai" (nilai 4) -> seri 3 kategori.
        $result = $this->scoredAttempt('Rudi Hartono', 'AAAAAA');

        $this->assertSame(8, $result->score_a);
        $this->assertSame(8, $result->score_b);
        $this->assertSame(8, $result->score_c);
        $this->assertSame(SibelResult::STATUS_TIE, $result->result_status);
        $this->assertTrue($result->isTie());
    }

    public function test_siswa_bisa_mengisi_kuesioner_likert_lewat_portal_tanpa_login(): void
    {
        $this->questionnaire->update(['status' => SibelQuestionnaire::STATUS_ACTIVE]);

        $answers = $this->questionnaire->questions
            ->mapWithKeys(fn ($question) => [
                $question->id => $question->options->firstWhere('option_code', 'A')->id,
            ])
            ->all();

        $class = SibelClass::create(['name' => 'X-B', 'academic_year' => '2026/2027']);

        $response = $this->post(route('sibel.submit'), [
            'full_name' => 'Aisyah Putri',
            'class_id' => $class->id,
            'answers' => $answers,
        ]);

        $attempt = SibelAttempt::query()->where('status', SibelAttempt::STATUS_COMPLETED)->latest('id')->first();

        $this->assertNotNull($attempt);
        $response->assertRedirect(route('sibel.assessment.result', $attempt));

        // 6 pernyataan x 4 poin = 24 total, masing-masing kategori 8 (seri).
        $result = $attempt->result;
        $this->assertNotNull($result);
        $this->assertSame(8, $result->score_a);
        $this->assertSame(8, $result->score_b);
        $this->assertSame(8, $result->score_c);
    }

    public function test_admin_menambahkan_pertanyaan_likert_membuat_empat_opsi_skala_otomatis(): void
    {
        $this->actingAs($this->guruBk)
            ->post(route('sibel.questionnaires.questions.store', $this->questionnaire), [
                'question_text' => 'Saya lebih suka belajar dengan gambar dan diagram.',
                'category_code' => 'visual',
            ])
            ->assertRedirect(route('sibel.questionnaires.show', $this->questionnaire))
            ->assertSessionHas('success');

        $question = $this->questionnaire->questions()->latest('id')->first();

        $this->assertSame('visual', $question->category_code);
        $this->assertCount(4, $question->options);

        // Opsi skala tersusun sesuai konfigurasi: A=4 s.d. D=1.
        $this->assertSame(
            [4, 3, 2, 1],
            $question->options->pluck('value')->all()
        );
        $this->assertSame(
            ['Sangat Sesuai', 'Sesuai', 'Kurang Sesuai', 'Tidak Sesuai'],
            $question->options->pluck('option_text')->all()
        );
    }

    public function test_pertanyaan_likert_wajib_memiliki_kategori_indikator(): void
    {
        $this->actingAs($this->guruBk)
            ->post(route('sibel.questionnaires.questions.store', $this->questionnaire), [
                'question_text' => 'Pernyataan tanpa kategori.',
            ])
            ->assertSessionHasErrors('category_code');
    }

    public function test_unduhan_excel_menghasilkan_tiga_sheet_dengan_data_siswa(): void
    {
        $class = SibelClass::create(['name' => 'XI IPA 1', 'academic_year' => '2026/2027']);
        $this->scoredAttempt('Budi Santoso', 'AABBCD', $class);

        $response = $this->actingAs($this->guruBk)
            ->get(route('sibel.results.export'));

        $response->assertOk();
        $response->assertDownload();

        // Baca kembali isi XLSX untuk memastikan struktur profesionalnya.
        $file = tempnam(sys_get_temp_dir(), 'sibel-test-').'.xlsx';
        copy($response->baseResponse->getFile()->getPathname(), $file);

        $reader = new Reader;
        $reader->open($file);

        $sheetNames = [];
        $recapRows = [];
        $sheetIndex = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            $sheetNames[] = $sheet->getName();

            if ($sheetIndex === 0) {
                foreach ($sheet->getRowIterator() as $row) {
                    $recapRows[] = array_map(
                        fn ($cell) => $cell->getValue(),
                        $row->getCells()
                    );
                }
            }

            $sheetIndex++;
        }

        $reader->close();
        @unlink($file);

        // Tiga sheet sesuai desain laporan.
        $this->assertSame(['Rekap Hasil', 'Distribusi Kelas', 'Detail Jawaban'], $sheetNames);

        // Kop laporan memuat nama sekolah & judul.
        $this->assertSame(
            strtoupper((string) Setting::get('site_name', 'SMA IT Tahfizh Al-Fatih')),
            (string) $recapRows[0][0]
        );
        $this->assertStringContainsString('Laporan Hasil Penilaian Gaya Belajar', (string) $recapRows[1][0]);

        // Baris header tabel.
        $headers = $recapRows[4] ?? [];
        $this->assertContains('Nama Siswa', $headers);
        $this->assertContains('Kategori Dominan', $headers);

        // Data siswa terekspor beserta skor & dominannya.
        $flat = collect($recapRows)->flatten()->map(fn ($v) => (string) $v)->implode(' | ');
        $this->assertStringContainsString('Budi Santoso', $flat);
        $this->assertStringContainsString('Visual', $flat);
    }

    public function test_unduhan_excel_diblokir_bila_bukan_pengelola(): void
    {
        $siswa = $this->createSibelUser('siswa', 'Siswa Biasa');

        $this->actingAs($siswa)
            ->get(route('sibel.results.export'))
            ->assertForbidden();
    }
}
