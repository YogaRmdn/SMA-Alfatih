<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SibelClass;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pemisahan alur: dashboard admin vs pengisian kuesioner siswa.
 */
class SibelLoginRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function siswa(): User
    {
        $role = Role::firstOrCreate(['slug' => 'siswa'], ['name' => 'Siswa']);
        $class = SibelClass::create(['name' => 'XI IPA 1', 'academic_year' => '2026/2027']);

        $user = new User;
        $user->name = 'Budi Santoso';
        $user->email = 'budi@siswa.test';
        $user->password = 'rahasia123';
        $user->email_verified_at = now();
        $user->role_id = $role->id;
        $user->save();

        SibelStudent::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'full_name' => 'Budi Santoso',
            'status' => SibelStudent::STATUS_ACTIVE,
            'source' => SibelStudent::SOURCE_SELF,
        ]);

        return $user;
    }

    private function admin(): User
    {
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);

        $user = new User;
        $user->name = 'Admin Sekolah';
        $user->email = 'admin@kontrol.test';
        $user->password = 'rahasia123';
        $user->email_verified_at = now();
        $user->role_id = $role->id;
        $user->save();

        return $user;
    }

    public function test_halaman_login_bisa_dibuka_saatsudah_login_untuk_ganti_akun(): void
    {
        $this->actingAs($this->admin())
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk');
    }

    public function test_ganti_akun_dari_admin_ke_siswa_mendarat_di_kuesioner(): void
    {
        $admin = $this->admin();
        $siswa = $this->siswa();

        $this->actingAs($admin);

        // Admin mengisi form login dengan akun siswa — sesi berpindah ke siswa.
        $this->post(route('login'), [
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($siswa);

        // /dashboard kini bercabang ke halaman pengisian, bukan dashboard admin.
        $this->get(route('dashboard'))
            ->assertRedirect(route('sibel.assessment.index'));

        // Siswa tetap ditolak total dari panel admin.
        $this->get('/admin')->assertForbidden();
    }

    public function test_login_siswa_dengan_tujuan_admin_tidak_dilempar_ke_admin(): void
    {
        $siswa = $this->siswa();

        // Sesi pernah menyimpan tujuan /admin (mis. pernah membuka saat tamu).
        $this->withSession(['url.intended' => url('/admin')]);

        $this->post(route('login'), [
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('sibel.assessment.index'));
    }

    public function test_login_siswa_tujuan_ruang_pengelola_sibel_dibuang(): void
    {
        $this->siswa();

        $this->withSession(['url.intended' => url('/sibel/hasil')]);

        $this->post(route('login'), [
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_login_siswa_tujuan_halaman_sendiri_dihormati(): void
    {
        $this->siswa();

        $this->withSession(['url.intended' => url('/sibel/kuesioner-saya')]);

        $this->post(route('login'), [
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
        ])->assertRedirect(url('/sibel/kuesioner-saya'));
    }

    public function test_login_admin_dengan_tujuan_admin_dihormati(): void
    {
        $this->admin();

        $this->withSession(['url.intended' => url('/admin')]);

        $this->post(route('login'), [
            'email' => 'admin@kontrol.test',
            'password' => 'rahasia123',
        ])->assertRedirect(url('/admin'));
    }

    public function test_tujuan_halaman_publik_tetap_dihormati_untuk_siswa(): void
    {
        $this->siswa();

        $this->withSession(['url.intended' => url('/berita/siswi-juara-1-tahfizh')]);

        $this->post(route('login'), [
            'email' => 'budi@siswa.test',
            'password' => 'rahasia123',
        ])->assertRedirect(url('/berita/siswi-juara-1-tahfizh'));
    }
}
