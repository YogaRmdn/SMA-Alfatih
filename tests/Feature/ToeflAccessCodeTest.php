<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\ToeflAccessCode;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Kode akses TOEFL (tanpa registrasi & login): validasi server untuk kode
 * sah / salah / kedaluwarsa / dicabut, penolakan kode lintas ujian, kewajiban
 * identitas (nama + kelas), dan pengikatan attempt ke session browser.
 */
class ToeflAccessCodeTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected ToeflExam $exam;

    protected string $code;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exam = $this->buildExam()['exam'];
        $this->code = $this->issueCode($this->exam);
    }

    /** Masukkan identitas + kode — helper agar asersi validasi tidak berulang. */
    private function enter(array $overrides = []): TestResponse
    {
        return $this->post(route('toefl.enter'), array_merge([
            'full_name' => 'Cahya Pratama',
            'class_name' => 'XII-1',
            'exam_id' => $this->exam->id,
            'code' => $this->code,
        ], $overrides));
    }

    public function test_portal_terbuka_tanpa_login_dan_menampilkan_daftar_ujian(): void
    {
        $this->assertGuest();

        $this->get(route('toefl.portal'))
            ->assertOk()
            ->assertSee('Mulai Ujian')
            ->assertSee('Nama Lengkap')
            ->assertSee('Kelas')
            ->assertSee($this->exam->title)
            ->assertSee('Riwayat Ujian');
    }

    public function test_kode_sah_membuat_attempt_dan_mengikat_session(): void
    {
        $this->enter()
            ->assertRedirect(route('toefl.instructions'))
            ->assertSessionHas('toefl_attempt_id')
            ->assertSessionHas('toefl_student_id');

        $attempt = ToeflAttempt::firstOrFail();

        $this->assertSame($this->exam->id, $attempt->exam_id);
        $this->assertSame(ToeflAttempt::STATUS_IN_PROGRESS, $attempt->status);
        $this->assertNull($attempt->started_at, 'Waktu belum boleh berjalan sebelum konfirmasi Mulai.');
        $this->assertNotNull($attempt->access_code_id);

        // Identitas peserta dibuat dari input, tanpa akun login.
        $this->assertDatabaseHas('toefl_students', [
            'id' => $attempt->student_id,
            'full_name' => 'Cahya Pratama',
            'class_name' => 'XII-1',
            'user_id' => null,
        ]);
        $this->assertGuest();
    }

    public function test_identitas_wajib_diisi(): void
    {
        $this->enter(['full_name' => ''])->assertSessionHasErrors('full_name');
        $this->enter(['class_name' => ''])->assertSessionHasErrors('class_name');

        $this->assertSame(0, ToeflAttempt::count());
    }

    public function test_kode_salah_ditolak_dan_tidak_membuat_attempt(): void
    {
        $this->enter(['code' => 'WRONG-1234'])
            ->assertSessionHasErrors('code')
            ->assertSessionMissing('toefl_attempt_id');

        $this->assertSame(0, ToeflAttempt::count());
    }

    public function test_kode_dengan_format_bersih_pun_diterima(): void
    {
        // Format tanpa tanda hubung harus identik dengan kode berformat.
        $this->enter(['code' => str_replace('-', '', $this->code)])
            ->assertSessionHas('toefl_attempt_id');
    }

    public function test_kode_kedaluwarsa_ditolak(): void
    {
        ToeflAccessCode::where('exam_id', $this->exam->id)
            ->update(['expires_at' => now()->subMinute()]);

        $this->enter()
            ->assertSessionHasErrors('code')
            ->assertSessionHasInput('code', $this->code);

        $this->assertSame(0, ToeflAttempt::count());
    }

    public function test_kode_dicabut_ditolak(): void
    {
        ToeflAccessCode::where('exam_id', $this->exam->id)
            ->update(['revoked_at' => now()]);

        $this->enter()->assertSessionHasErrors('code');

        $this->assertSame(0, ToeflAttempt::count());
    }

    public function test_kode_ujian_lain_ditolak(): void
    {
        $other = $this->buildExam(1, ['title' => 'Ujian Lain', 'exam_code' => 'LAIN1234'])['exam'];

        // Kode sah milik $this->exam tidak berlaku untuk ujian lain.
        $this->enter(['exam_id' => $other->id])->assertSessionHasErrors('code');
    }

    public function test_ujian_tutup_ditolak(): void
    {
        $this->exam->update(['status' => ToeflExam::STATUS_CLOSED]);

        $this->enter()->assertSessionHasErrors('code');
    }

    public function test_peserta_nonaktif_ditolak(): void
    {
        // Identitas sudah diblokir pengawas lewat panel admin.
        $this->buildStudent('Cahya Pratama', [
            'class_name' => 'XII-1',
            'status' => ToeflStudent::STATUS_INACTIVE,
        ]);

        $this->enter()->assertSessionHasErrors('code');

        $this->assertSame(0, ToeflAttempt::count());
    }

    public function test_kode_disimpan_sebagai_hash_bukan_plaintext(): void
    {
        $this->enter();

        $record = ToeflAccessCode::firstOrFail();

        $this->assertStringNotContainsString(str_replace('-', '', $this->code), $record->code_hash);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $record->code_hash);
    }

    public function test_route_ujian_memerlukan_session_attempt(): void
    {
        $this->get(route('toefl.exam.section', 'listening'))
            ->assertRedirect(route('toefl.portal'));

        $this->post(route('toefl.exam.submit'))
            ->assertRedirect(route('toefl.portal'));
    }

    public function test_session_attempt_yang_sudah_dihapus_dibersihkan(): void
    {
        // Id attempt tidak ada lagi (mis. data ujian dihapus pengawas).
        $this->withSession(['toefl_attempt_id' => 999999])
            ->get(route('toefl.exam.section', 'listening'))
            ->assertRedirect(route('toefl.portal'));

        // Ikatan session yang basi langsung dibersihkan.
        $this->assertNull(session('toefl_attempt_id'));
    }

    public function test_keluar_sesi_melepas_identitas_dan_attempt(): void
    {
        $this->enter()->assertSessionHas('toefl_attempt_id');

        $this->post(route('toefl.leave'))
            ->assertRedirect(route('toefl.portal'))
            ->assertSessionMissing('toefl_attempt_id')
            ->assertSessionMissing('toefl_student_id');
    }

    public function test_login_admin_website_mengalihkan_ke_panel_kelola(): void
    {
        $this->app['auth']->forgetGuards();

        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->forceFill(['role_id' => Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin'])->id])->save();

        $this->actingAs($admin, 'web');

        $this->get(route('toefl.portal'))->assertRedirect(route('toefl.admin.dashboard'));
    }
}
