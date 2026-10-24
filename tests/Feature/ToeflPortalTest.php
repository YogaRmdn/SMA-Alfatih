<?php

namespace Tests\Feature;

use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Portal TOEFL tanpa registrasi & login:
 *  - tidak ada route/mekanisme login & register siswa (404, guard dihapus),
 *  - identitas ditulis peserta (nama + kelas) dan diikat ke session browser,
 *  - nama + kelas sama -> identitas & attempt yang sama (resume, bukan ganda),
 *  - satu perangkat bisa bergantian lewat "Keluar Sesi".
 */
class ToeflPortalTest extends TestCase
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

    public function test_halaman_login_dan_register_tidak_ada_lagi(): void
    {
        $this->get('/toefl/login')->assertNotFound();
        $this->get('/toefl/register')->assertNotFound();
        $this->post('/toefl/login', [])->assertNotFound();

        // Guard khusus siswa sudah dihapus — TOEFL tidak lagi berbasis akun.
        $this->assertNull(config('auth.guards.toefl'));
    }

    public function test_portal_bisa_dibuka_tanpa_sesi_dan_tanpa_login(): void
    {
        $this->assertGuest();

        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertDontSee('/toefl/login')
            ->assertDontSee('/toefl/register');
    }

    public function test_identitas_sama_menghasilkan_satu_attempt_saja(): void
    {
        $first = $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');
        $this->beginExam($first);

        $this->flushSession();

        // Masuk ulang dengan nama + kelas sama (huruf beda kapital) -> attempt yang sama.
        $second = $this->enterExam($this->code, $this->exam, 'ALYA ZAHRA', 'xi-2');

        $this->assertSame($first->id, $second->id, 'Resume attempt, bukan membuat sesi baru.');
        $this->assertSame(1, ToeflAttempt::count());
        $this->assertSame(1, ToeflStudent::count());
    }

    public function test_identitas_berbeda_mendapat_attempt_terpisah(): void
    {
        $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');
        $this->flushSession();

        $other = $this->enterExam($this->code, $this->exam, 'Bima Saputra', 'XI-2');

        $this->assertSame(2, ToeflAttempt::count());
        $this->assertSame(2, ToeflStudent::count());
        $this->assertNotSame('Alya Zahra', $other->student->full_name);
    }

    public function test_identitas_terdaftar_pengawas_dipakai_ulang(): void
    {
        $registered = $this->buildStudent('Alya Zahra', ['class_name' => 'XI-2']);

        $attempt = $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');

        $this->assertSame($registered->id, $attempt->student_id, 'Identitas admin dipakai, bukan duplikat.');
        $this->assertSame(1, ToeflStudent::count());
    }

    public function test_riwayat_muncul_untuk_identitas_yang_terikat_session(): void
    {
        $attempt = $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');

        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertSee('Alya Zahra')
            ->assertSee($this->exam->title)
            ->assertSee($attempt->statusLabel());
    }

    public function test_keluar_sesi_menghapus_riwayat_dari_perangkat(): void
    {
        $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');
        $this->post(route('toefl.leave'))->assertRedirect(route('toefl.portal'));

        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertDontSee('Alya Zahra')
            ->assertSee('Riwayat muncul setelah Anda masuk');
    }

    public function test_instruksi_dan_hasil_mengikuti_identitas_session(): void
    {
        // Tanpa masuk -> halaman instruksi & hasil dialihkan ke portal.
        $this->get(route('toefl.instructions'))->assertRedirect(route('toefl.portal'));
        $this->get(route('toefl.result'))->assertRedirect(route('toefl.portal'));

        $attempt = $this->enterExam($this->code, $this->exam, 'Alya Zahra', 'XI-2');

        $this->get(route('toefl.instructions'))
            ->assertOk()
            ->assertSee('Alya Zahra');
    }

    public function test_route_masuk_menggunakan_throttle_pencegah_tebakan_kode(): void
    {
        $route = app('router')->getRoutes()->getByName('toefl.enter');

        $this->assertNotNull($route);
        $this->assertStringContainsString('throttle', implode(',', $route->gatherMiddleware()));
    }

    public function test_tombol_demo_hanya_tampil_saat_debug(): void
    {
        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertSee('Masuk sebagai Peserta Demo');

        config(['app.debug' => false]);

        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertDontSee('Masuk sebagai Peserta Demo');
    }

    public function test_masuk_demo_melompati_kode_dan_mengikat_sesi(): void
    {
        $this->post(route('toefl.demo'))
            ->assertRedirect(route('toefl.instructions'));

        $this->assertDatabaseHas('toefl_students', [
            'full_name' => 'Peserta Demo',
            'class_name' => 'Demo',
        ]);

        $this->assertSame(1, ToeflAttempt::count());
        $this->assertNotNull(session('toefl_student_id'));
        $this->assertNotNull(session('toefl_attempt_id'));

        // Kode demo deterministik, dibuat sekali per ujian (bisa dipakai ulang).
        $this->assertSame(1, $this->exam->accessCodes()->where('code_prefix', 'DEMO')->count());

        $this->post(route('toefl.demo'))->assertRedirect(route('toefl.instructions'));
        $this->assertSame(1, ToeflAttempt::count());
        $this->assertSame(1, $this->exam->accessCodes()->where('code_prefix', 'DEMO')->count());
    }

    public function test_route_demo_ditolak_bila_debug_mati(): void
    {
        config(['app.debug' => false]);

        $this->post(route('toefl.demo'))->assertNotFound();

        $this->assertSame(0, ToeflAttempt::count());
        $this->assertSame(0, ToeflStudent::count());
    }
}
