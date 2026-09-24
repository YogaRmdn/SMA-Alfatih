<?php

namespace Tests\Feature;

use App\Models\Download;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Setting::set('site_name', 'SMA IT Tahfizh Al-Fatih');
    }

    private function makeDownload(array $overrides = []): Download
    {
        return Download::create(array_merge([
            'title' => 'Formulir PPDB 2026',
            'description' => 'Formulir pendaftaran peserta didik baru.',
            'file' => 'downloads/formulir-ppdb.pdf',
            'file_type' => 'pdf',
            'file_size' => 204800,
            'category' => 'Formulir',
            'downloads' => 0,
        ], $overrides));
    }

    public function test_public_page_lists_downloadable_files(): void
    {
        $this->makeDownload();

        $this->get(route('downloads.index'))
            ->assertOk()
            ->assertSee('Formulir PPDB 2026')
            ->assertSee('Unduh PDF')
            ->assertSee('peserta didik baru');
    }

    public function test_public_page_shows_empty_state_when_no_files(): void
    {
        $this->get(route('downloads.index'))
            ->assertOk()
            ->assertSee('Belum ada file');
    }

    public function test_download_increments_counter_and_streams_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('downloads/formulir-ppdb.pdf', '%PDF-1.4 content');

        $download = $this->makeDownload(['downloads' => 3]);

        $this->get(route('downloads.download', $download))
            ->assertOk();

        $this->assertSame(4, $download->fresh()->downloads);
    }

    public function test_download_returns_404_when_file_missing(): void
    {
        Storage::fake('public');

        $download = $this->makeDownload();

        $this->get(route('downloads.download', $download))->assertNotFound();
    }

    public function test_admin_upload_accepts_only_pdf(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->value('id')]);

        $this->actingAs($admin)
            ->post(route('admin.downloads.store'), [
                'title' => 'Formulir',
                'file' => UploadedFile::fake()->createWithContent('form.pdf', '%PDF-1.4 test'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('downloads', ['title' => 'Formulir', 'file_type' => 'pdf']);

        $this->actingAs($admin)
            ->post(route('admin.downloads.store'), [
                'title' => 'Formulir Salah',
                'file' => UploadedFile::fake()->create('form.docx', 10),
            ])
            ->assertSessionHasErrors('file');
    }
}