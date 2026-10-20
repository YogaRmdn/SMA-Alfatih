<?php

namespace Tests\Feature;

use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Portal SIBEL tanpa login (/sibel): tamu memilih identitas kelas + nama,
 * sesi browser terikat, lalu dashboard khusus pengisian kuesioner tampil.
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

    public function test_portal_terbuka_tanpa_login_dan_menampilkan_daftar_nama(): void
    {
        $this->get(route('sibel.portal'))
            ->assertOk()
            ->assertSee('Pilih Identitas Siswa')
            ->assertSee('Aisyah Rahmawati')
            ->assertSee(route('sibel.identify'));
    }

    public function test_memilih_nama_mengikat_sesi_dan_membuka_dashboard(): void
    {
        $this->post(route('sibel.identify'), ['student_id' => $this->student->id])
            ->assertRedirect(route('sibel.assessment.index'))
            ->assertSessionHas('sibel_student_id', $this->student->id);

        $this->get(route('sibel.assessment.index'))
            ->assertOk()
            ->assertSee('Halo, Aisyah')
            ->assertSee('Kuesioner Gaya Belajar');
    }

    public function test_nama_tidak_dipilih_ditolak(): void
    {
        $this->post(route('sibel.identify'), [])
            ->assertSessionHasErrors('student_id');

        $this->post(route('sibel.identify'), ['student_id' => 9999])
            ->assertSessionHasErrors('student_id');

        $this->assertNull(session('sibel_student_id'));
    }

    public function test_siswa_nonaktif_tidak_bisa_dipilih(): void
    {
        $inactive = $this->createStandaloneStudent('Budi Cadangan', $this->class);
        $inactive->update(['status' => SibelStudent::STATUS_INACTIVE]);

        $this->post(route('sibel.identify'), ['student_id' => $inactive->id])
            ->assertSessionHasErrors('student_id');

        $this->assertNull(session('sibel_student_id'));
    }

    public function test_mengganti_siswa_melepas_ikatan_sesi(): void
    {
        $this->actingAsStudent($this->student)
            ->post(route('sibel.identify'), ['student_id' => $this->student->id]);

        $this->actingAsStudent($this->student)
            ->post(route('sibel.forget'))
            ->assertRedirect(route('sibel.portal'));

        $this->assertNull(session('sibel_student_id'));

        // Kembali ke halaman kuesioner tanpa identitas -> portal lagi.
        $this->get(route('sibel.assessment.index'))->assertRedirect(route('sibel.portal'));
    }

    public function test_portal_menampilkan_hanya_siswa_aktif_pada_kelas_terpilih(): void
    {
        $otherClass = SibelClass::create(['name' => 'XI-IPA-1', 'academic_year' => '2026/2027']);
        $this->createStandaloneStudent('Zainab di Kelas Lain', $otherClass);

        $this->get(route('sibel.portal', ['kelas' => $this->class->id]))
            ->assertOk()
            ->assertSee('Aisyah Rahmawati')
            ->assertDontSee('Zainab di Kelas Lain');
    }

    public function test_akun_siswa_lama_otomatis_terikat_ke_profilnya(): void
    {
        $legacy = $this->createSibelUser('siswa', 'Siswa Lama');
        $profile = $this->createStudentFor($legacy, $this->class);

        $this->actingAs($legacy)
            ->get(route('sibel.portal'))
            ->assertRedirect(route('sibel.assessment.index'))
            ->assertSessionHas('sibel_student_id', $profile->id);

        $this->actingAs($legacy)
            ->get(route('sibel.assessment.index'))
            ->assertOk()
            ->assertSee('Halo, Siswa');
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
