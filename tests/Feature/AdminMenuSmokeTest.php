<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Album;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Download;
use App\Models\Extracurricular;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Ppdb;
use App\Models\Program;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMenuSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Setting::set('site_name', 'SMA IT Tahfizh Al-Fatih');
    }

    private function admin(): User
    {
        return $this->userWithRole('admin');
    }

    private function superAdmin(): User
    {
        return $this->userWithRole('super_admin');
    }

    private function userWithRole(string $slug): User
    {
        return User::factory()->create([
            'role_id' => Role::where('slug', $slug)->value('id'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Semua halaman menu admin (index + create) yang harus bisa diakses.
     */
    public static function menuPages(): array
    {
        return [
            'dashboard' => '/admin',
            'news.index' => '/admin/news',
            'news.create' => '/admin/news/create',
            'categories.index' => '/admin/categories',
            'categories.create' => '/admin/categories/create',
            'announcements.index' => '/admin/announcements',
            'announcements.create' => '/admin/announcements/create',
            'albums.index' => '/admin/albums',
            'albums.create' => '/admin/albums/create',
            'galleries.index' => '/admin/galleries',
            'galleries.create' => '/admin/galleries/create',
            'achievements.index' => '/admin/achievements',
            'achievements.create' => '/admin/achievements/create',
            'teachers.index' => '/admin/teachers',
            'teachers.create' => '/admin/teachers/create',
            'staffs.index' => '/admin/staffs',
            'staffs.create' => '/admin/staffs/create',
            'facilities.index' => '/admin/facilities',
            'facilities.create' => '/admin/facilities/create',
            'extracurriculars.index' => '/admin/extracurriculars',
            'extracurriculars.create' => '/admin/extracurriculars/create',
            'programs.index' => '/admin/programs',
            'programs.create' => '/admin/programs/create',
            'banners.index' => '/admin/banners',
            'banners.create' => '/admin/banners/create',
            'downloads.index' => '/admin/downloads',
            'downloads.create' => '/admin/downloads/create',
            'testimonials.index' => '/admin/testimonials',
            'testimonials.create' => '/admin/testimonials/create',
            'partners.index' => '/admin/partners',
            'partners.create' => '/admin/partners/create',
            'faqs.index' => '/admin/faqs',
            'faqs.create' => '/admin/faqs/create',
            'pages.index' => '/admin/pages',
            'pages.create' => '/admin/pages/create',
            'comments.index' => '/admin/comments',
            'ppdb.index' => '/admin/ppdb',
            'contact.edit' => '/admin/contact',
            'settings.edit' => '/admin/settings',
            'profile.about' => '/admin/profil/tentang-sejarah',
            'profile.visi' => '/admin/profil/visi-misi',
            'profile.structure' => '/admin/profil/struktur-organisasi',
            'users.index' => '/admin/users',
            'users.create' => '/admin/users/create',
        ];
    }

    public function test_admin_can_open_every_menu_page(): void
    {
        $this->actingAs($this->admin());

        foreach (self::menuPages() as $name => $uri) {
            if (str_starts_with($uri, '/admin/users')) {
                $this->withExceptionHandling()->get($uri)->assertForbidden();

                continue;
            }

            try {
                $this->withoutExceptionHandling()->get($uri)->assertOk();
            } catch (\Throwable $e) {
                $this->fail("{$name} @ {$uri}: ".$e->getMessage());
            }
        }
    }

    public function test_super_admin_can_open_every_menu_page(): void
    {
        $this->actingAs($this->superAdmin());

        foreach (self::menuPages() as $name => $uri) {
            $this->withoutExceptionHandling()->get($uri)->assertOk();
        }
    }

    public function test_guest_is_redirected_to_login_for_admin_pages(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/news')->assertRedirect(route('login'));
    }

    public function test_profil_sekolah_menus_are_visible_in_sidebar(): void
    {
        $this->actingAs($this->superAdmin());

        $this->get('/admin')
            ->assertSee('/admin/profil/tentang-sejarah')
            ->assertSee('/admin/profil/visi-misi')
            ->assertSee('/admin/profil/struktur-organisasi');
    }

    public function test_profil_sekolah_sections_create_and_update_pages(): void
    {
        $this->actingAs($this->superAdmin());

        $this->put('/admin/profil/tentang-sejarah', [
            'title' => 'Tentang & Sejarah',
            'content' => "Berdiri tahun 2020.\nBerikutnya tahun 2021.",
            'is_active' => true,
        ])->assertRedirect('/admin/profil/tentang-sejarah');

        $this->put('/admin/profil/visi-misi', [
            'title' => 'Visi & Misi',
            'content' => 'Menjadi sekolah tahfizh unggulan.',
            'is_active' => true,
        ])->assertRedirect('/admin/profil/visi-misi');

        $this->assertDatabaseHas('pages', ['slug' => 'tentang-sejarah', 'section' => 'profile']);
        $this->assertDatabaseHas('pages', ['slug' => 'visi-misi']);

        $this->assertEquals("Berdiri tahun 2020.\nBerikutnya tahun 2021.", Page::where('slug', 'tentang-sejarah')->first()->content);
    }

    public function test_profile_pages_are_rendered_on_public_home(): void
    {
        $this->actingAs($this->superAdmin());

        $this->put('/admin/profil/tentang-sejarah', [
            'title' => 'Tentang & Sejarah',
            'content' => 'SEJARAH-TENTANG-UNIK',
            'is_active' => true,
        ]);

        $this->put('/admin/profil/sambutan', [
            'title' => 'Sambutan Kepala Sekolah',
            'content' => 'SAMBUTAN-UNIK-KEPALA-SEKOLAH',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertDontSee('SEJARAH-TENTANG-UNIK')
            ->assertSee('SAMBUTAN-UNIK-KEPALA-SEKOLAH')
            ->assertSee(route('profil.tentang'));
    }

    public function test_teachers_carousel_renders_on_public_home(): void
    {
        Teacher::create([
            'name' => 'Bapak Guru Uji',
            'position' => 'Guru Matematika',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertSee('Dewan Guru')
            ->assertSee('Bapak Guru Uji')
            ->assertSee('Guru Matematika');
    }

    public function test_facility_uploaded_photo_becomes_card_background_on_home(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put(
            'facilities/fasilitas-uji.jpg',
            file_get_contents(public_path('img/16.jpeg'))
        );

        Facility::create([
            'name' => 'Asrama Putra & Putri',
            'image' => 'facilities/fasilitas-uji.jpg',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Asrama Putra & Putri')
            ->assertSee('storage/facilities/fasilitas-uji.jpg');
    }

    public function test_profile_welcome_photos_can_be_set_from_settings(): void
    {
        Storage::fake('public');

        $this->actingAs($this->superAdmin());

        $this->put('/admin/settings', [
            'site_name' => 'SMA IT Tahfizh Al-Fatih',
            'profil_photo_1' => new \Illuminate\Http\UploadedFile(
                public_path('img/16.jpeg'),
                'gedung-kiri.jpg',
                'image/jpeg',
                null,
                true
            ),
        ])->assertRedirect('/admin/settings');

        $photo = Setting::get('profil_photo_1');
        $this->assertNotNull($photo);

        $this->get('/')
            ->assertOk()
            ->assertSee('storage/settings/'.basename($photo));
    }

    /**
     * Siklus CRUD lengkap di setiap modul menu admin.
     */
    public function test_full_crud_cycle_for_every_module(): void
    {
        $this->actingAs($this->superAdmin());

        $modules = [
            'news' => [News::class, ['title' => 'Berita Uji', 'content' => 'Konten berita uji.']],
            'categories' => [Category::class, ['name' => 'Kategori Uji']],
            'announcements' => [Announcement::class, ['title' => 'Pengumuman Uji', 'content' => 'Isi pengumuman uji.']],
            'albums' => [Album::class, ['title' => 'Album Uji']],
            'galleries' => [Gallery::class, ['type' => 'photo']],
            'achievements' => [Achievement::class, ['title' => 'Prestasi Uji']],
            'teachers' => [Teacher::class, ['name' => 'Guru Uji']],
            'staffs' => [Staff::class, ['name' => 'Staff Uji', 'position' => 'Tata Usaha']],
            'facilities' => [Facility::class, ['name' => 'Fasilitas Uji']],
            'extracurriculars' => [Extracurricular::class, ['name' => 'Ekskul Uji']],
            'programs' => [Program::class, ['name' => 'Program Uji', 'type' => 'unggulan']],
            'banners' => [Banner::class, ['title' => 'Banner Uji', 'position' => 'hero']],
            'downloads' => [Download::class, ['title' => 'Unduhan Uji']],
            'testimonials' => [Testimonial::class, ['name' => 'Testimoni Uji', 'content' => 'Isi testimoni uji.']],
            'partners' => [Partner::class, ['name' => 'Mitra Uji']],
            'faqs' => [Faq::class, ['question' => 'Pertanyaan uji?', 'answer' => 'Jawaban uji.']],
            'pages' => [Page::class, ['title' => 'Halaman Uji', 'section' => 'profile']],
        ];

        foreach ($modules as $prefix => [$modelClass, $payload]) {
            if ($prefix === 'downloads') {
                $this->post("/admin/{$prefix}", $payload + ['file' => UploadedFile::fake()->create('unduhan.pdf', 10, 'application/pdf')])
                    ->assertRedirect("/admin/{$prefix}");
            } else {
                $this->post("/admin/{$prefix}", $payload)->assertRedirect("/admin/{$prefix}");
            }

            $item = $modelClass::first();
            $this->assertNotNull($item, "{$prefix}: data gagal dibuat.");

            $this->get(route("admin.{$prefix}.edit", $item))->assertOk();
            $this->put(route("admin.{$prefix}.update", $item), $payload)->assertRedirect("/admin/{$prefix}");
            $this->delete(route("admin.{$prefix}.destroy", $item))->assertRedirect("/admin/{$prefix}");

            $usesSoftDeletes = in_array(
                \Illuminate\Database\Eloquent\SoftDeletes::class,
                class_uses_recursive($modelClass),
                true
            );

            if ($usesSoftDeletes) {
                $record = $modelClass::withTrashed()->find($item->id);
                $this->assertTrue($record && $record->trashed(), "{$prefix}: data tidak terhapus (soft delete).");
            } else {
                $this->assertDatabaseMissing($modelClass::newModelInstance()->getTable(), ['id' => $item->id]);
            }
        }
    }

    public function test_comments_moderation_works(): void
    {
        $author = $this->superAdmin();
        $this->actingAs($author);

        $news = News::create(['title' => 'Berita Komentar', 'content' => 'Isi', 'user_id' => $author->id, 'is_published' => true]);
        $comment = Comment::create(['news_id' => $news->id, 'name' => 'Pengunjung', 'email' => 'a@b.com', 'content' => 'Komentar uji']);

        $this->post("/admin/comments/{$comment->id}/approve")->assertRedirect();
        $this->assertTrue($comment->fresh()->is_approved);

        $this->get('/admin/comments')->assertOk();

        $this->delete("/admin/comments/{$comment->id}")->assertRedirect('/admin/comments');
    }

    public function test_ppdb_admin_flow_works(): void
    {
        $this->actingAs($this->superAdmin());

        $ppdb = Ppdb::create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Calon Siswa',
            'gender' => 'L',
            'status' => 'pending',
        ]);

        $this->get('/admin/ppdb')->assertOk();
        $this->get("/admin/ppdb/{$ppdb->id}")->assertOk();
        $this->assertEquals('Menunggu Verifikasi', $ppdb->statusLabel());

        $this->put("/admin/ppdb/{$ppdb->id}/status", ['status' => 'accepted', 'admin_notes' => 'Selamat!'])->assertRedirect();
        $this->assertEquals('accepted', $ppdb->fresh()->status);
        $this->assertEquals('Diterima', $ppdb->fresh()->statusLabel());

        $this->delete("/admin/ppdb/{$ppdb->id}")->assertRedirect('/admin/ppdb');
        $this->assertDatabaseMissing('ppdb', ['id' => $ppdb->id]);
    }

    public function test_settings_and_contact_pages_persist(): void
    {
        $this->actingAs($this->superAdmin());

        $this->get('/admin/contact')->assertOk();
        $this->get('/admin/settings')->assertOk();

        $contact = \App\Models\Contact::firstOrCreate(['id' => 1]);

        $this->put("/admin/contact/{$contact->id}", [
            'address' => 'Jl. Uji No. 1',
            'whatsapp' => '6281234567890',
        ])->assertRedirect('/admin/contact');

        $this->assertDatabaseHas('contacts', ['address' => 'Jl. Uji No. 1']);

        $this->put('/admin/settings', ['site_name' => 'SMA Uji Baru'])->assertRedirect('/admin/settings');
        $this->assertEquals('SMA Uji Baru', Setting::get('site_name'));
    }
}