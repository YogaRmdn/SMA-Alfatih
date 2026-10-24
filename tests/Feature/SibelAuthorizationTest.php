<?php

namespace Tests\Feature;

use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelResult;
use App\Models\SibelStudent;
use App\Models\User;
use App\Services\LearningStyleScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsSibelData;
use Tests\TestCase;

/**
 * Otorisasi role SIBEL (rencana proyek — bagian 11, skenario 9 & 10):
 * siswa hanya area kuesioner miliknya; guru BK/admin mengelola data.
 */
class SibelAuthorizationTest extends TestCase
{
    use BuildsSibelData, RefreshDatabase;

    protected User $guruBk;

    protected User $admin;

    protected User $adminSibel;

    protected User $siswa;

    protected SibelStudent $student;

    protected SibelQuestionnaire $questionnaire;

    protected SibelResult $result;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createSibelRoles();

        $this->guruBk = $this->createSibelUser('guru_bk', 'Guru BK Uji');
        $this->admin = $this->createSibelUser('admin', 'Admin Uji');
        $this->adminSibel = $this->createSibelUser('admin_sibel', 'Admin SIBEL Uji');
        $this->siswa = $this->createSibelUser('siswa', 'Siswa Uji');

        $class = SibelClass::create(['name' => 'XI-IPA-1', 'academic_year' => '2026/2027']);
        $this->student = $this->createStudentFor($this->siswa, $class);

        $this->questionnaire = $this->createQuestionnaire(3);
        $attempt = $this->createAttempt($this->student, $this->questionnaire, 'AAA');
        $this->result = app(LearningStyleScoringService::class)->scoreAndPersist($attempt);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function managerRoutes(): array
    {
        return [
            'sibel.dashboard' => [],
            'sibel.students.index' => [],
            'sibel.students.create' => [],
            'sibel.students.show' => [$this->student],
            'sibel.students.edit' => [$this->student],
            'sibel.classes.index' => [],
            'sibel.classes.create' => [],
            'sibel.classes.edit' => [SibelClass::first()],
            'sibel.questionnaires.index' => [],
            'sibel.questionnaires.create' => [],
            'sibel.questionnaires.show' => [$this->questionnaire],
            'sibel.questionnaires.edit' => [$this->questionnaire],
            'sibel.results.index' => [],
            'sibel.results.show' => [$this->result],
            'sibel.recap.index' => [],
        ];
    }

    public function test_tamu_dapat_membuka_portal_tanpa_login(): void
    {
        // Portal siswa terbuka untuk siapa pun (tanpa login).
        $this->get('/sibel')->assertOk();

        // Area pengelola tetap dilindungi login website.
        $this->get('/sibel/hasil')->assertRedirect(route('login'));
        $this->get('/sibel/kelola')->assertRedirect(route('login'));

        // Area non-SIBEL tetap ke login website.
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_guru_bk_dapat_mengakses_seluruh_layanan_pengelola(): void
    {
        foreach ($this->managerRoutes() as $route => $parameters) {
            $this->actingAs($this->guruBk)
                ->get(route($route, $parameters))
                ->assertOk();
        }
    }

    public function test_admin_dapat_mengakses_layanan_pengelola(): void
    {
        foreach (['sibel.dashboard', 'sibel.students.index', 'sibel.questionnaires.index', 'sibel.results.index', 'sibel.recap.index'] as $route) {
            $this->actingAs($this->admin)->get(route($route))->assertOk();
        }
    }

    public function test_admin_sibel_dapat_mengakses_seluruh_layanan_pengelola(): void
    {
        foreach ($this->managerRoutes() as $route => $parameters) {
            $this->actingAs($this->adminSibel)
                ->get(route($route, $parameters))
                ->assertOk();
        }
    }

    /**
     * admin_sibel khusus SIBEL: diblokir dari panel website & panel TOEFL.
     */
    public function test_admin_sibel_diblokir_dari_panel_admin_dan_toefl(): void
    {
        foreach (['/admin', '/admin/news', '/admin/users', '/toefl/kelola', '/toefl/kelola/ujian'] as $uri) {
            $this->actingAs($this->adminSibel)
                ->get($uri)
                ->assertForbidden();
        }
    }

    public function test_pengalihan_dashboard_admin_sibel_menuju_panel_sibel(): void
    {
        $this->actingAs($this->adminSibel)
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.dashboard'));

        // Membuka /sibel juga langsung ke dashboard SIBEL, bukan form pengisian.
        $this->actingAs($this->adminSibel)
            ->get(route('sibel.portal'))
            ->assertRedirect(route('sibel.dashboard'));
    }

    /**
     * Tautan pindah panel "Panel Admin" hanya untuk role yang berhak
     * membuka /admin — guru BK & admin SIBEL tidak melihatnya.
     */
    public function test_tautan_pindah_panel_hanya_untuk_yang_berhak(): void
    {
        $this->actingAs($this->adminSibel)
            ->get(route('sibel.dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'), false);

        $this->actingAs($this->guruBk)
            ->get(route('sibel.dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.dashboard'), false);

        $this->actingAs($this->admin)
            ->get(route('sibel.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.dashboard'), false);
    }

    public function test_siswa_diblokir_dari_seluruh_layanan_pengelola(): void
    {
        foreach ($this->managerRoutes() as $route => $parameters) {
            $this->actingAs($this->siswa)
                ->get(route($route, $parameters))
                ->assertForbidden();
        }
    }

    public function test_pengguna_tanpa_role_diblokir_dari_area_pengelola(): void
    {
        $tanpaRole = new User;
        $tanpaRole->name = 'Pengguna Biasa';
        $tanpaRole->email = 'biasa@test.local';
        $tanpaRole->password = 'password';
        $tanpaRole->email_verified_at = now();
        $tanpaRole->save();

        $this->actingAs($tanpaRole)->get(route('sibel.dashboard'))->assertForbidden();
    }

    public function test_area_pengisian_terbuka_tanpa_login(): void
    {
        // Halaman pengisian siswa terbuka untuk siapa pun (tanpa login).
        $this->get(route('sibel.portal'))->assertOk();

        // Pengelola yang membuka /sibel diarahkan ke dashboard miliknya.
        $this->actingAs($this->guruBk)
            ->get(route('sibel.portal'))
            ->assertRedirect(route('sibel.dashboard'));
    }

    public function test_siswa_tidak_bisa_membuka_detail_hasil_orang_lain(): void
    {
        $siswaLain = $this->createSibelUser('siswa', 'Siswa Lain');
        $this->createStudentFor($siswaLain, SibelClass::first());

        // Lewat rute pengelola (diblokir middleware role).
        $this->actingAs($siswaLain)
            ->get(route('sibel.results.show', $this->result))
            ->assertForbidden();

        $this->actingAs($siswaLain)
            ->get(route('sibel.results.print', $this->result))
            ->assertForbidden();
    }

    public function test_pengalihan_dashboard_mengikuti_role(): void
    {
        // Akun siswa lama diarahkan ke portal (identitas mengikat via session).
        $this->actingAs($this->siswa)
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.portal'));

        $this->actingAs($this->guruBk)
            ->get(route('dashboard'))
            ->assertRedirect(route('sibel.dashboard'));

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_membuka_sesi_dengan_id_asing_mendapat_403(): void
    {
        $siswaLain = $this->createStandaloneStudent('Siswa Ketiga', SibelClass::first());

        $attempt = SibelAttempt::first();

        $this->actingAsStudent($siswaLain)->get(route('sibel.assessment.result', $attempt))->assertForbidden();
        $this->actingAsStudent($siswaLain)->get(route('sibel.assessment.print', $attempt))->assertForbidden();

        // Pemilik sesi tetap boleh membuka miliknya sendiri.
        $this->actingAsStudent($this->student)->get(route('sibel.assessment.result', $attempt))->assertOk();
    }
}
