<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman 404 khusus (resources/views/errors/404.blade.php) memakai layout
 * frontend — wajib bertema sama (navbar/footer sekolah), noindex, dan
 * membantu pengunjung kembali menelusuri situs.
 */
class NotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_path_tidak_dikenal_menampilkan_halaman_404_bertema(): void
    {
        $this->get('/halaman-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('Halaman Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda')
            ->assertSee('Atau kunjungi');

        // Path yang diminta ikut ditampilkan agar mudah didiagnosis.
        $this->get('/halaman/yang/tidak/ada')->assertNotFound()->assertSee('/halaman/yang/tidak/ada');
    }

    public function test_halaman_404_noindex_dan_memiliki_menu_cepat(): void
    {
        $this->get('/berita/tidak-ada')
            ->assertNotFound()
            ->assertSee('name="robots" content="noindex, nofollow"', false)
            ->assertSee('Berita')
            ->assertSee('Unduhan')
            ->assertSee('Kontak')
            ->assertSee(route('news.index'));
    }

    public function test_pengunjung_masuk_dapat_tombol_dashboard(): void
    {
        $user = new User;
        $user->name = 'Staf Sekolah';
        $user->email = 'staf@test.local';
        $user->password = 'password';
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertSee('Buka Dashboard')
            ->assertSee(route('dashboard'));
    }

    public function test_tombol_ppdb_di_halaman_404_mengikuti_pengaturan(): void
    {
        $this->get('/tidak-ada')->assertNotFound()->assertDontSee('Daftar PPDB', false);

        Setting::set('ppdb_open', '1');
        cache()->forget('site_settings');

        $this->get('/tidak-ada')
            ->assertNotFound()
            ->assertSee('Daftar PPDB')
            ->assertSee(route('ppdb.register'));
    }
}
