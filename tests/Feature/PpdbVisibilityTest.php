<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Saat PPDB ditutup, seluruh titik pendaftaran di website publik harus ikut
 * hilang — navbar, hero, footer, CTA artikel, dan link dari halaman cek status.
 * Halaman berita & konten lain tetap normal.
 */
class PpdbVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
    }

    /**
     * Halaman yang punya CTA pendaftaran di layout/frontend.
     */
    public static function ppdbCtaPageProvider(): array
    {
        return [
            'beranda' => ['/'],
            'berita' => ['/berita'],
            'unduhan' => ['/unduhan'],
            'cek status' => ['/ppdb/status'],
            'tentang sejarah' => ['/tentang-sejarah'],
            'visi misi' => ['/visi-misi'],
            'struktur' => ['/struktur-organisasi'],
        ];
    }

    public static function newsArticleProvider(): array
    {
        return [
            'artikel berita' => ['/berita/siswi-juara-1-tahfizh'],
        ];
    }

    private function publishNews(): News
    {
        $category = Category::firstOrCreate(['slug' => 'prestasi'], ['name' => 'Prestasi']);

        return News::create([
            'title' => 'Siswi Raih Juara 1 Lomba Tahfizh',
            'slug' => 'siswi-juara-1-tahfizh',
            'excerpt' => 'Siswi kami meraih juara tingkat kota Pekanbaru.',
            'content' => 'Paragraf pertama. Paragraf kedua.',
            'category_id' => $category->id,
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);
    }

    private function setPpdbOpen(string $value): void
    {
        Setting::set('ppdb_open', $value);
        cache()->forget('site_settings');
    }

    #[DataProvider('ppdbCtaPageProvider')]
    public function test_cta_pendaftaran_muncul_saat_ppdb_dibuka(string $path): void
    {
        $this->setPpdbOpen('1');

        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertStringContainsString('Daftar PPDB', $html, "CTA daftar tidak muncul di {$path}");
    }

    #[DataProvider('ppdbCtaPageProvider')]
    public function test_cta_pendaftaran_hilang_saat_ppdb_ditutup(string $path): void
    {
        $this->setPpdbOpen('0');

        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertStringNotContainsString('Daftar PPDB', $html, "CTA daftar masih muncul di {$path}");
        $this->assertStringNotContainsString('Daftar Sekarang', $html, "CTA 'Daftar Sekarang' masih muncul di {$path}");
        $this->assertStringNotContainsString('Pendaftaran Peserta Didik Baru telah dibuka', $html);
    }

    #[DataProvider('ppdbCtaPageProvider')]
    public function test_tombol_cek_status_juga_hilang_saat_ppdb_ditutup(string $path): void
    {
        $this->setPpdbOpen('0');

        $html = $this->get($path)->assertSuccessful()->getContent();

        // Halaman /ppdb/status sendiri memang harus tetap bisa diakses
        // supaya pendaftar lama bisa cek hasilnya.
        if ($path === '/ppdb/status') {
            $this->assertStringContainsString('Cek Status', $html);

            return;
        }

        // Dicek per-URL, bukan per-teks: teks "Cek Status" di HTML Mata
        // diberi whitespace/newline, jadi pola ">Cek Status<" selalu gagal
        // cocok dan sempat membuat test ini false-pass.
        $this->assertStringNotContainsString(
            route('ppdb.status'),
            $html,
            "Link ke /ppdb/status masih muncul di {$path}"
        );
        $this->assertStringNotContainsString(
            route('ppdb.register'),
            $html,
            "Link ke /ppdb/register masih muncul di {$path}"
        );
    }

    #[DataProvider('ppdbCtaPageProvider')]
    public function test_link_ppdb_ada_di_semua_halaman_saat_ppdb_dibuka(string $path): void
    {
        $this->setPpdbOpen('1');

        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertStringContainsString(route('ppdb.register'), $html, "Link /ppdb/register tidak ada di {$path}");
        $this->assertStringContainsString(route('ppdb.status'), $html, "Link /ppdb/status tidak ada di {$path}");
    }

    #[DataProvider('newsArticleProvider')]
    public function test_cta_daftar_di_artikel_berita_hilang_saat_ppdb_ditutup(string $path): void
    {
        $this->publishNews();
        $this->setPpdbOpen('0');

        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertStringNotContainsString('Daftar PPDB Sekarang', $html);
    }

    #[DataProvider('newsArticleProvider')]
    public function test_berita_tetap_ada_saat_ppdb_ditutup(string $path): void
    {
        $news = $this->publishNews();
        $this->setPpdbOpen('0');

        $html = $this->get($path)->assertSuccessful()->getContent();

        $this->assertStringContainsString($news->title, $html, 'Konten berita harus tetap tampil');
        $this->assertStringContainsString($news->excerpt, $html);
    }

    public function test_halaman_berita_tetap_terindeks_saat_ppdb_ditutup(): void
    {
        $this->publishNews();
        $this->setPpdbOpen('0');

        $html = $this->get('/berita')->assertSuccessful()->getContent();

        $this->assertStringContainsString(
            'content="index, follow',
            $html,
            'Halaman berita harus tetap indexable saat PPDB ditutup'
        );
    }

    public function test_halaman_ppdb_menampilkan_pesan_ditutup(): void
    {
        $this->setPpdbOpen('0');

        $html = $this->get('/ppdb')->assertSuccessful()->getContent();

        $this->assertStringContainsString('Pendaftaran Sedang Ditutup', $html);
        $this->assertStringContainsString('name="robots" content="noindex, follow"', $html);
    }

    public function test_halaman_ppdb_tetap_terindeks_saat_dibuka(): void
    {
        $this->setPpdbOpen('1');

        $html = $this->get('/ppdb')->assertSuccessful()->getContent();

        $this->assertStringNotContainsString('name="robots" content="noindex', $html);
        $this->assertStringContainsString('Formulir Pendaftaran Peserta Didik Baru', $html);
    }

    public function test_sitemap_tidak_mencantumkan_ppdb_saat_ditutup(): void
    {
        $this->setPpdbOpen('0');

        $xml = $this->get('/sitemap.xml')->assertSuccessful()->getContent();

        // Cek per-tag <loc> supaya /ppdb tidak dianggap match dari /ppdb/status.
        $this->assertStringNotContainsString('<loc>'.url('/ppdb').'</loc>', $xml);
        // Cek status tetap berguna walau pendaftaran ditutup.
        $this->assertStringContainsString('<loc>'.url('/ppdb/status').'</loc>', $xml);
        $this->assertStringContainsString('<loc>'.url('/berita').'</loc>', $xml);
    }

    public function test_sitemap_mencantumkan_ppdb_saat_dibuka(): void
    {
        $this->setPpdbOpen('1');

        $xml = $this->get('/sitemap.xml')->assertSuccessful()->getContent();

        $this->assertStringContainsString('<loc>'.url('/ppdb').'</loc>', $xml);
    }

    public function test_admin_bisa_menyalakan_dan_mematikan_ppdb_lewat_pengaturan(): void
    {
        $this->setPpdbOpen('0');

        $this->actingAs($this->superAdmin());

        $this->put('/admin/settings', [
            'site_name' => 'SMA IT Tahfizh Al-Fatih',
            'ppdb_open' => true,
        ])->assertRedirect('/admin/settings');

        $this->assertSame('1', Setting::get('ppdb_open'));
        $this->assertStringContainsString('Daftar PPDB', $this->get('/')->getContent());
    }

    public function test_admin_bisa_mematikan_ppdb_lewat_pengaturan(): void
    {
        $this->setPpdbOpen('1');

        $this->actingAs($this->superAdmin());

        $this->put('/admin/settings', [
            'site_name' => 'SMA IT Tahfizh Al-Fatih',
        ])->assertRedirect('/admin/settings');

        $this->assertSame('0', Setting::get('ppdb_open'));
        $this->assertStringNotContainsString('Daftar PPDB', $this->get('/')->getContent());
    }

    public function test_halaman_pengaturan_menampilkan_status_ppdb(): void
    {
        $this->actingAs($this->superAdmin());

        $this->setPpdbOpen('1');
        $this->get('/admin/settings')->assertSuccessful()
            ->assertSee('Sedang Dibuka');

        $this->setPpdbOpen('0');
        $this->get('/admin/settings')->assertSuccessful()
            ->assertSee('Ditutup');
    }

    private function superAdmin(): User
    {
        $role = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.local',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $user->role_id = $role->id;
        $user->save();

        return $user;
    }
}
