<?php

namespace Tests\Feature;

use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Portal SIBEL tanpa login (/sibel): langsung halaman pengisian kuesioner
 * dengan kolom nama (tanpa memilih dari daftar). Jawaban dikirim -> sesi
 * terikat ke nama tersebut -> hasil tampil & bisa diunduh.
 */
class SibelPortalTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected SibelClass $class;

    protected SibelStudent $student;

    protected SibelQuestionnaire $questionnaire;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->class = SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027']);
        $this->student = $this->createStandaloneStudent('Aisyah Rahmawati', $this->class);
        $this->questionnaire = $this->createQuestionnaire(2);
    }

    /**
     * Payload kirim jawaban: nama + jawaban lengkap sesuai pola kode.
     */
    protected function payload(string $name, string $pattern = 'AA'): array
    {
        $answers = [];

        foreach ($this->questionnaire->questions()->with('options')->get() as $i => $question) {
            $option = $question->options->firstWhere('option_code', strtoupper($pattern[$i] ?? 'A'));
            $answers[$question->id] = $option->id;
        }

        return ['full_name' => $name, 'answers' => $answers];
    }

    public function test_portal_terbuka_tanpa_login_dan_langsung_menampilkan_kuesioner(): void
    {
        $this->get(route('sibel.portal'))
            ->assertOk()
            ->assertSee('Isi Kuesioner Gaya Belajar')
            ->assertSee('Nama Lengkap Anda')
            ->assertSee('Pertanyaan ke-1')
            ->assertSee('Pertanyaan ke-2')
            ->assertDontSee('Pilih Identitas Siswa');
    }

    public function test_mengirim_jawaban_dengan_nama_baru_membuat_identitas_dan_hasil(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Budi Santoso'))
            ->assertRedirect(route('sibel.assessment.result', SibelAttempt::firstOrFail()))
            ->assertSessionHas('sibel_student_id');

        $student = SibelStudent::where('full_name', 'Budi Santoso')->firstOrFail();

        $this->assertSame(SibelStudent::SOURCE_SELF, $student->source);
        $this->assertSame(SibelStudent::STATUS_ACTIVE, $student->status);
        $this->assertNull($student->class_id);

        $this->assertDatabaseHas('attempts', [
            'student_id' => $student->id,
            'questionnaire_id' => $this->questionnaire->id,
            'status' => SibelAttempt::STATUS_COMPLETED,
        ]);

        $this->assertSame(1, SibelResult::count());
    }

    public function test_nama_sama_dengan_siswa_terdaftar_dipakai_ulang(): void
    {
        $this->post(route('sibel.submit'), $this->payload('aisyah rahmawati'))
            ->assertSessionHas('sibel_student_id', $this->student->id);

        // Tidak membuat identitas duplikat — memakai profil yang sudah ada.
        $this->assertSame(1, SibelStudent::count());
        $this->assertDatabaseHas('attempts', ['student_id' => $this->student->id]);
    }

    public function test_nama_wajib_diisi(): void
    {
        $payload = $this->payload('Sementara');
        $payload['full_name'] = '';

        $this->post(route('sibel.submit'), $payload)
            ->assertSessionHasErrors('full_name');

        $this->assertSame(0, SibelAttempt::count());

        // Nama terlalu pendek juga ditolak.
        $payload['full_name'] = 'Ab';
        $this->post(route('sibel.submit'), $payload)
            ->assertSessionHasErrors('full_name');
    }

    public function test_jawaban_kosong_ditolak_dan_tidak_menyimpan_apa_pun(): void
    {
        $payload = $this->payload('Budi Santoso');
        $questionIds = $this->questionnaire->questions()->pluck('id');
        unset($payload['answers'][$questionIds->last()]); // satu pertanyaan kosong

        $this->post(route('sibel.submit'), $payload)
            ->assertSessionHasErrors(["answers.{$questionIds->last()}"]);

        $this->assertSame(0, SibelAttempt::count());
        $this->assertSame(0, SibelAnswer::count());
        $this->assertSame(0, SibelResult::count());
    }

    public function test_opsi_milik_pertanyaan_lain_ditolak(): void
    {
        $questions = $this->questionnaire->questions()->with('options')->get();
        $payload = $this->payload('Budi Santoso');

        // Opsi pertanyaan #2 dipaksakan ke pertanyaan #1.
        $payload['answers'][$questions[0]->id] = $questions[1]->options->first()->id;

        $this->post(route('sibel.submit'), $payload)
            ->assertSessionHasErrors(["answers.{$questions[0]->id}"]);

        $this->assertSame(0, SibelResult::count());
    }

    public function test_pengisian_ulang_diblokir_dan_mengarahkan_ke_hasil_lama(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Aisyah Rahmawati'));

        $latest = SibelAttempt::firstOrFail();

        // Default allow_retake=false: kirim ulang tidak membuat sesi baru.
        $this->post(route('sibel.submit'), $this->payload('Aisyah Rahmawati', 'BB'))
            ->assertRedirect(route('sibel.assessment.result', $latest));

        $this->assertSame(1, SibelAttempt::count());
        $this->assertSame(1, SibelResult::count());
    }

    public function test_sudah_mengisi_membuka_langsung_halaman_hasil(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Aisyah Rahmawati'));

        $attempt = SibelAttempt::firstOrFail();

        $this->actingAsStudent($this->student)
            ->get(route('sibel.portal'))
            ->assertRedirect(route('sibel.assessment.result', $attempt));
    }

    public function test_sesi_ditutup_menampilkan_halaman_kosong(): void
    {
        $this->questionnaire->update(['status' => SibelQuestionnaire::STATUS_CLOSED]);

        $this->get(route('sibel.portal'))
            ->assertOk()
            ->assertSee('Sesi Kuesioner Belum Dibuka');

        // Pengiriman pun ditolak selama sesi ditutup.
        $this->post(route('sibel.submit'), $this->payload('Budi Santoso'))
            ->assertRedirect(route('sibel.portal'))
            ->assertSessionHas('error');

        $this->assertSame(0, SibelAttempt::count());
    }

    public function test_mengganti_siswa_melepas_ikatan_sesi(): void
    {
        $this->post(route('sibel.submit'), $this->payload('Aisyah Rahmawati'));

        $this->post(route('sibel.forget'))
            ->assertRedirect(route('sibel.portal'));

        $this->assertNull(session('sibel_student_id'));

        // Tanpa ikatan sesi, halaman pengisian tetap terbuka untuk nama berikutnya.
        $this->get(route('sibel.portal'))
            ->assertOk()
            ->assertSee('Nama Lengkap Anda');
    }

    public function test_akun_siswa_lama_otomatis_terikat_ke_profilnya(): void
    {
        $legacy = $this->createSibelUser('siswa', 'Siswa Lama');
        $profile = $this->createStudentFor($legacy, $this->class);

        // Belum pernah mengisi -> halaman pengisian tampil, nama sudah terisi.
        $this->actingAs($legacy)
            ->get(route('sibel.portal'))
            ->assertOk()
            ->assertSee('Siswa Lama')
            ->assertSessionHas('sibel_student_id', $profile->id);

        // Sudah mengisi -> langsung ke halaman hasil.
        $attempt = $this->createAttempt($profile, $this->questionnaire, 'AA');

        $this->actingAs($legacy)
            ->get(route('sibel.portal'))
            ->assertRedirect(route('sibel.assessment.result', $attempt));
    }

    public function test_validasi_gagal_mengembalikan_jawaban_lama_dan_tanpa_kode_bocor(): void
    {
        $payload = $this->payload('Budi Santoso');
        $payload['full_name'] = ''; // nama kosong -> validasi gagal

        $this->post(route('sibel.submit'), $payload)
            ->assertSessionHasErrors('full_name');

        // Halaman pengisian tampil lagi: jawaban lama tetap terpilih (checked),
        // tanpa potongan kode JavaScript yang bocor ke badan halaman
        // (regresi: atribut x-data sempat putus karena escape kutip \" ).
        $response = $this->get(route('sibel.portal'))->assertOk();

        $response->assertDontSee('radio.name.match');
        $response->assertDontSee('querySelectorAll');
        $response->assertSee('x-data="{', false);

        $firstQuestion = $this->questionnaire->questions()->with('options')->get()->first();

        $this->assertMatchesRegularExpression(
            '/name="answers\[' . $firstQuestion->id . '\]"[^>]*checked/',
            $response->getContent()
        );
    }

    public function test_halaman_cetak_siswa_tanpa_kelas_tidak_error(): void
    {
        // Siswa hasil pengisian mandiri umumnya belum punya kelas (class_id null).
        $this->post(route('sibel.submit'), $this->payload('Budi Santoso', 'AA'));

        $attempt = SibelAttempt::firstOrFail();

        $this->assertNull($attempt->student->class_id);

        // Halaman hasil & cetak tetap tampil walau data kelas kosong
        // (kelas ditampilkan sebagai "—" — tanpa error properti null).
        $this->get(route('sibel.assessment.result', $attempt))
            ->assertOk()
            ->assertSee('Kelas')
            ->assertSee('—');

        $this->get(route('sibel.assessment.print', $attempt))
            ->assertOk()
            ->assertSee('Laporan Hasil Asesmen Gaya Belajar Siswa')
            ->assertSee('Budi Santoso');
    }

    public function test_pengelola_dari_portal_masuk_ke_dashboard_pengelola(): void
    {
        foreach (['guru_bk', 'admin'] as $roleSlug) {
            $manager = $this->createSibelUser($roleSlug);

            $this->actingAs($manager)
                ->get(route('sibel.portal'))
                ->assertRedirect(route('sibel.dashboard'));
        }
    }

    public function test_akun_siswa_lama_di_dashboard_website_masuk_ke_portal(): void
    {
        $legacy = $this->createSibelUser('siswa', 'Siswa Lama Dua');
        $this->createStudentFor($legacy, $this->class);

        $this->actingAs($legacy)
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.portal'));

        // Pengguna tanpa role tidak diarahkan ke SIBEL.
        $user = new User;
        $user->name = 'Wali Murid';
        $user->email = 'wali@test.local';
        $user->password = 'password';
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
