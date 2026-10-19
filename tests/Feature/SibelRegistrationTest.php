<?php

namespace Tests\Feature;

use App\Models\Role;
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
        $this->assertSame(SibelStudent::SOURCE_SELF, $student->source);
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

    public function test_form_daftar_bisa_dibuka_saat_sudah_login(): void
    {
        $user = User::create(['name' => 'Admin', 'email' => 'admin@contoh.test', 'password' => 'rahasia123']);

        // Admin yang sedang login tetap bisa melihat form (tidak dilempar ke dashboard).
        $this->actingAs($user)
            ->get(route('sibel.register'))
            ->assertOk()
            ->assertSee('Daftar Akun Siswa');
    }

    public function test_pendaftaran_saat_sudah_login_tidak_mengganti_sesi(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('sibel.register.store'), $this->validPayload())
            ->assertRedirect(route('sibel.register'));

        // Sesi admin tidak dibajak; akun siswa tetap terbuat & berlabel daftar mandiri.
        $this->assertAuthenticatedAs($admin);
        $this->assertDatabaseHas('students', [
            'full_name' => 'Budi Santoso',
            'source' => SibelStudent::SOURCE_SELF,
        ]);
    }

    public function test_admin_melihat_lencana_daftar_mandiri_di_data_siswa(): void
    {
        $this->post(route('sibel.register.store'), $this->validPayload());

        $this->actingAs($this->adminUser())
            ->get(route('sibel.students.index'))
            ->assertOk()
            ->assertSee('Daftar Mandiri');
    }

    public function test_filter_asal_membedakan_pendaftar_mandiri_dan_buatan_admin(): void
    {
        // Satu pendaftar mandiri.
        $this->post(route('sibel.register.store'), $this->validPayload());

        // Satu siswa dibuat admin lewat menu Data Siswa.
        $admin = $this->adminUser();
        $class = SibelClass::create(['name' => 'XI IPA 1', 'academic_year' => '2026/2027']);
        $this->actingAs($admin)
            ->post(route('sibel.students.store'), [
                'full_name' => 'Siti Aminah',
                'class_id' => $class->id,
                'status' => SibelStudent::STATUS_ACTIVE,
            ])
            ->assertRedirect(route('sibel.students.index'));
        $this->assertDatabaseHas('students', [
            'full_name' => 'Siti Aminah',
            'source' => SibelStudent::SOURCE_MANUAL,
        ]);

        $this->get(route('sibel.students.index', ['source' => 'self']))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Siti Aminah');

        $this->get(route('sibel.students.index', ['source' => 'manual']))
            ->assertOk()
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Santoso');
    }

    public function test_tombol_daftar_sibel_muncul_di_navbar(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Daftar SIBEL')
            ->assertSee(route('sibel.register'));
    }

    private function adminUser(): User
    {
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);

        $user = new User;
        $user->name = 'Admin Sekolah';
        $user->email = 'admin@kontrol.test';
        $user->password = 'rahasia123';
        $user->role_id = $role->id;
        $user->save();

        return $user;
    }
}
