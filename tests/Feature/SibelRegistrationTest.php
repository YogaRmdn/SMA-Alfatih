<?php

namespace Tests\Feature;

use App\Models\SibelClass;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SibelRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ], $overrides);
    }

    public function test_tamu_dapat_membuka_halaman_daftar(): void
    {
        $class = SibelClass::create(['name' => 'XI IPA 1', 'academic_year' => '2026/2027']);

        $this->get(route('sibel.register'))
            ->assertOk()
            ->assertSee('Daftar Akun Siswa')
            ->assertSee($class->name)
            ->assertSee(route('sibel.register.store'));
    }

    public function test_pendaftaran_membuat_akun_siswa_dan_login_otomatis(): void
    {
        $class = SibelClass::create(['name' => 'XI IPA 1', 'academic_year' => '2026/2027']);

        $this->post(route('sibel.register.store'), $this->validPayload([
            'class_id' => $class->id,
            'student_number' => '2026001',
        ]))
            ->assertRedirect(route('sibel.assessment.index'));

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@siswa.test')->firstOrFail();
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertNotNull($user->email_verified_at);

        $student = SibelStudent::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Budi Santoso', $student->full_name);
        $this->assertSame($class->id, $student->class_id);
        $this->assertSame('2026001', $student->student_number);
        $this->assertSame(SibelStudent::STATUS_ACTIVE, $student->status);
    }

    public function test_pendaftaran_tanpa_kelas_diperbolehkan(): void
    {
        $this->post(route('sibel.register.store'), $this->validPayload())
            ->assertRedirect(route('sibel.assessment.index'));

        $this->assertAuthenticated();

        $student = SibelStudent::where('full_name', 'Budi Santoso')->firstOrFail();
        $this->assertNull($student->class_id);
    }

    public function test_setelah_daftar_dashboard_mengarahkan_ke_kuesioner(): void
    {
        $this->post(route('sibel.register.store'), $this->validPayload());

        $this->get(route('dashboard'))
            ->assertRedirect(route('sibel.assessment.index'));
    }

    public function test_email_sudah_dipakai_ditolak(): void
    {
        User::create(['name' => 'Sudah Ada', 'email' => 'budi@siswa.test', 'password' => 'rahasia123']);

        $this->post(route('sibel.register.store'), $this->validPayload())
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseCount('students', 0);
    }

    public function test_input_tidak_lengkap_ditolak(): void
    {
        $this->post(route('sibel.register.store'), [])
            ->assertSessionHasErrors(['full_name', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_password_pendek_ditolak(): void
    {
        $this->post(route('sibel.register.store'), $this->validPayload([
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ]))
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_kelas_tidak_dikenal_ditolak(): void
    {
        $this->post(route('sibel.register.store'), $this->validPayload(['class_id' => 999]))
            ->assertSessionHasErrors('class_id');

        $this->assertGuest();
    }

    public function test_pengguna_sudah_login_dialihkan_dari_halaman_daftar(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@contoh.test', 'password' => 'rahasia123']);

        $this->actingAs($user)
            ->get(route('sibel.register'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_tombol_daftar_sibel_muncul_di_navbar(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Daftar SIBEL')
            ->assertSee(route('sibel.register'));
    }
}
