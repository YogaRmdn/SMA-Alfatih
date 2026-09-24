<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tentang_sejarah_page_shows_content_when_active(): void
    {
        Page::create([
            'title' => 'Tentang & Sejarah',
            'slug' => 'tentang-sejarah',
            'section' => 'profile',
            'content' => 'Sekolah kami berdiri sejak tahun 1980.',
            'is_active' => true,
        ]);

        $this->get(route('profil.tentang'))
            ->assertOk()
            ->assertSee('Sekolah kami berdiri sejak tahun 1980.');
    }

    public function test_visi_misi_page_shows_content_when_active(): void
    {
        Page::create([
            'title' => 'Visi & Misi',
            'slug' => 'visi-misi',
            'section' => 'profile',
            'content' => "Visi: Menjadi sekolah tahfizh unggulan.\n\nMisi: Menyelenggarakan pendidikan berbasis Al-Qur'an.",
            'is_active' => true,
        ]);

        $this->get(route('profil.visi-misi'))
            ->assertOk()
            ->assertSee('Menjadi sekolah tahfizh unggulan.')
            ->assertSee('Visi:', false);
    }

    public function test_struktur_organisasi_page_shows_image_when_active(): void
    {
        Page::create([
            'title' => 'Struktur Organisasi',
            'slug' => 'struktur-organisasi',
            'section' => 'profile',
            'content' => 'Bagan organisasi sekolah.',
            'image' => 'pages/struktur.png',
            'is_active' => true,
        ]);

        $this->get(route('profil.struktur'))
            ->assertOk()
            ->assertSee('storage/pages/struktur.png')
            ->assertSee('Bagan organisasi sekolah.');
    }

    public function test_page_shows_empty_state_when_content_not_created_yet(): void
    {
        $this->get(route('profil.tentang'))
            ->assertOk()
            ->assertSee('Konten belum tersedia');
    }

    public function test_page_returns_404_when_inactive(): void
    {
        Page::create([
            'title' => 'Tentang & Sejarah',
            'slug' => 'tentang-sejarah',
            'section' => 'profile',
            'content' => 'Konten rahasia',
            'is_active' => false,
        ]);

        $this->get(route('profil.tentang'))->assertNotFound();
    }

    public function test_tentang_sejarah_page_displays_image_and_text_side_by_side(): void
    {
        Page::create([
            'title' => 'Tentang & Sejarah',
            'slug' => 'tentang-sejarah',
            'section' => 'profile',
            'content' => 'Sejarah sekolah kami dimulai dari sebuah pesantren kecil.',
            'image' => 'pages/sejarah.png',
            'is_active' => true,
        ]);

        $html = $this->get(route('profil.tentang'))->assertOk()->getContent();

        $this->assertStringContainsString('storage/pages/sejarah.png', $html);
        $this->assertStringContainsString('lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]', $html);
        $this->assertStringContainsString('clip-path: polygon(0 0, 100% 0, calc(100% - 2.5rem) 100%, 0 100%)', $html);
        $this->assertStringContainsString('clip-path: polygon(0 0, 100% 0, 100% 100%, 2.5rem 100%)', $html);
        $this->assertStringContainsString('Sejarah sekolah kami dimulai dari sebuah pesantren kecil.', $html);
    }

    public function test_profile_links_are_rendered_in_navbar(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Tentang & Sejarah')
            ->assertSee('Struktur Organisasi');
    }
}