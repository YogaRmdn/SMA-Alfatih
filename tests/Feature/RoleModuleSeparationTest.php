<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Pemisahan modul SIBEL & TOEFL di level hak akses:
 *  - admin_toefl khusus panel TOEFL (tanpa SIBEL & panel website),
 *  - guru_bk & admin_sibel khusus panel SIBEL (tanpa TOEFL & panel website),
 *  - setiap role mendarat di dashboard modulnya sendiri setelah login,
 *  - tujuan intended lintas modul dibuang, bukan dilempar ke halaman 403,
 *  - menu sidebar hanya menampilkan modul sesuai role.
 */
class RoleModuleSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'guru_bk' => 'Guru BK',
            'admin_sibel' => 'Admin SIBEL',
            'admin_toefl' => 'Admin TOEFL',
        ] as $slug => $name) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }

    protected function userWithRole(string $slug): User
    {
        $user = User::create([
            'name' => 'Pengguna '.$slug,
            'email' => $slug.'@alfatih.test',
            'password' => Hash::make('password'),
        ]);

        // role_id tidak ada di $fillable — diisi langsung, bukan lewat mass assignment.
        $user->role_id = Role::where('slug', $slug)->value('id');
        $user->email_verified_at = now();
        $user->save();

        return $user->refresh();
    }

    public function test_admin_toefl_hanya_bisa_membuka_panel_toefl(): void
    {
        $user = $this->userWithRole('admin_toefl');

        $this->actingAs($user)->get(route('toefl.admin.dashboard'))->assertOk();

        // Tidak berhak atas modul SIBEL maupun panel website.
        $this->actingAs($user)->get(route('sibel.dashboard'))->assertForbidden();
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/admin/news')->assertForbidden();
    }

    public function test_guru_bk_dan_admin_sibel_diblokir_dari_panel_toefl(): void
    {
        foreach (['guru_bk', 'admin_sibel'] as $slug) {
            $user = $this->userWithRole($slug);

            $this->actingAs($user)->get(route('sibel.dashboard'))->assertOk();
            $this->actingAs($user)->get(route('toefl.admin.dashboard'))->assertForbidden();
            $this->actingAs($user)->get('/admin')->assertForbidden();
        }
    }

    public function test_super_admin_dan_admin_membuka_kedua_panel(): void
    {
        foreach (['super_admin', 'admin'] as $slug) {
            $user = $this->userWithRole($slug);

            $this->actingAs($user)->get(route('sibel.dashboard'))->assertOk();
            $this->actingAs($user)->get(route('toefl.admin.dashboard'))->assertOk();
        }
    }

    public function test_login_mengantar_ke_dashboard_modul_masing_masing(): void
    {
        $this->actingAs($this->userWithRole('admin_toefl'))
            ->get(route('dashboard'))
            ->assertRedirect(route('toefl.admin.dashboard'));

        $this->actingAs($this->userWithRole('guru_bk'))
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.dashboard'));

        $this->actingAs($this->userWithRole('admin_sibel'))
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.dashboard'));

        $this->actingAs($this->userWithRole('super_admin'))
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_tujuan_intended_panel_website_dibuang_untuk_admin_toefl(): void
    {
        $user = $this->userWithRole('admin_toefl');

        // Tamu membuka panel website — tujuan tersimpan sementara di session.
        $this->get('/admin/news')->assertRedirect(route('login'));

        // Karena admin_toefl tidak berhak atas /admin, tujuan itu dibuang dan
        // ia mendarat di pendaratan bawaan, bukan halaman yang akan menolaknya.
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_tujuan_intended_panel_toefl_dibuang_untuk_guru_bk(): void
    {
        $user = $this->userWithRole('guru_bk');

        $this->get(route('toefl.admin.dashboard'))->assertRedirect(route('login'));

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
    }

    public function test_menu_sidebar_admin_toefl_hanya_menampilkan_modul_toefl(): void
    {
        $this->actingAs($this->userWithRole('admin_toefl'))
            ->get(route('toefl.admin.dashboard'))
            ->assertOk()
            ->assertSee('TOEFL ITP Online')
            ->assertDontSee('SIBEL Gaya Belajar');
    }
}
