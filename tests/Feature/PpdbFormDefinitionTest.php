<?php

namespace Tests\Feature;

use App\Models\Ppdb;
use App\Models\PpdbDocument;
use App\Models\PpdbFormField;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\PpdbFormFieldSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Formulir PPDB digerakkan tabel `ppdb_form_fields`, bukan field hardcoded.
 * Test ini mengunci perilaku yang dijanjikan school: admin mengubah form,
 * pendaftar mengikutinya, dan dokumen tetap privat.
 */
class PpdbFormDefinitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        $this->seed(PpdbFormFieldSeeder::class);

        Setting::set('ppdb_open', '1');
        cache()->forget('site_settings');
    }

    public function test_konfigurasi_bawaan_sesuai_google_form(): void
    {
        $keys = PpdbFormField::query()->ordered()->pluck('key')->all();

        $this->assertSame([
            'full_name',
            'gender',
            'birth_place',
            'birth_date',
            'address',
            'origin_school',
            'program',
            'info_source',
            'phone',
            'payment_proof',
        ], $keys);
    }

    public function test_halaman_pendaftaran_menampilkan_semua_pertanyaan_aktif(): void
    {
        $html = $this->get('/ppdb')->assertSuccessful()->getContent();

        foreach ([
            'Nama Lengkap Siswa',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Alamat Lengkap',
            'Sekolah Asal Siswa',
            'Program yang Dipilih',
            'Sumber Informasi Mengenai Al-Fatih',
            'No. HP/WA Orang Tua atau Wali Siswa',
            'Bukti Pembayaran Formulir Pendaftaran',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Pertanyaan '{$label}' tidak tampil");
        }
    }

    public function test_field_dimatikan_tidak_lagi_tampil(): void
    {
        PpdbFormField::where('key', 'origin_school')->update(['is_active' => false]);

        $html = $this->get('/ppdb')->assertSuccessful()->getContent();

        $this->assertStringNotContainsString('name="origin_school"', $html);
        $this->assertStringContainsString('name="full_name"', $html);
    }

    public function test_pendaftar_bisa_mengirim_formulir_lengkap(): void
    {
        Storage::fake('local');

        $response = $this->post('/ppdb', [
            'full_name' => 'Aisyah Rahma',
            'gender' => 'P',
            'birth_place' => 'Pekanbaru',
            'birth_date' => '2012-04-17',
            'address' => 'Jl. Melati No. 12',
            'origin_school' => 'SMPN 1yming',
            'program' => 'BOARDING',
            'info_source' => 'instagram',
            'phone' => '081234567890',
            'payment_proof' => UploadedFile::fake()->create('bukti.jpg', 120, 'image/jpeg'),
        ]);

        $ppdb = Ppdb::query()->sole();

        $response->assertRedirect(route('ppdb.success', $ppdb));
        $this->assertSame('Aisyah Rahma', $ppdb->full_name);
        $this->assertSame('BOARDING', $ppdb->program);
        $this->assertSame('instagram', $ppdb->info_source);
        $this->assertSame('pending', $ppdb->status);

        Storage::disk('local')->assertExists($ppdb->document('payment_proof')->value('file_path'));
    }

    public function test_pendaftaran_ditolak_saat_field_wajib_kosong(): void
    {
        Storage::fake('local');

        $this->post('/ppdb', [
            'full_name' => 'Aisyah Rahma',
        ])->assertSessionHasErrors([
            'gender', 'birth_place', 'birth_date', 'address', 'origin_school', 'program', 'info_source', 'phone', 'payment_proof',
        ]);

        $this->assertSame(0, Ppdb::query()->count());
    }

    public function test_pilihan_program_tidak_valid_ditolak(): void
    {
        Storage::fake('local');

        $this->post('/ppdb', $this->validPayload(['program' => 'REGULER']))->assertSessionHasErrors('program');
    }

    public function test_field_kustom_baru_langsung_berfungsi(): void
    {
        Storage::fake('local');

        PpdbFormField::create([
            'key' => 'nama_wali',
            'label' => 'Nama Wali Murid',
            'type' => 'text',
            'width' => 'full',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 900,
        ]);

        $this->get('/ppdb')->assertSuccessful()->assertSee('Nama Wali Murid');

        $this->post('/ppdb', $this->validPayload())->assertSessionHasErrors('nama_wali');

        $this->post('/ppdb', $this->validPayload(['nama_wali' => 'Bapak Sulaiman']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Bapak Sulaiman', Ppdb::query()->sole()->answer('nama_wali'));
    }

    public function test_dokumen_hanya_bisa_diakses_pemilik_pendaftaran(): void
    {
        Storage::fake('local');

        $ppdb = Ppdb::query()->create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'status' => 'pending',
        ]);

        $path = $ppdb->documents()->create([
            'type' => 'payment_proof',
            'name' => 'Bukti Pembayaran',
            'file_path' => UploadedFile::fake()->create('bukti.jpg', 120, 'image/jpeg')->store('ppdb', 'local'),
        ])->file_path;

        $url = route('ppdb.document', [$ppdb, 'payment_proof']);

        // Pengunjung yang tidak punya sesi pendaftaran tidak boleh melihat berkas.
        $this->get($url)->assertForbidden();

        $this->withSession(['ppdb_registration_id' => $ppdb->id])
            ->get($url)->assertSuccessful();

        $this->withSession(['ppdb_verified_id' => $ppdb->id])
            ->get($url)->assertSuccessful();

        Storage::disk('local')->assertExists($path);
    }

    public function test_dokumen_menolak_tipe_di_luar_daftar(): void
    {
        Storage::fake('local');

        $ppdb = Ppdb::query()->create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'status' => 'pending',
        ]);

        $this->withSession(['ppdb_registration_id' => $ppdb->id])
            ->get(route('ppdb.document', [$ppdb, 'bebas']))
            ->assertNotFound();
    }

    public function test_admin_bisa_menambah_pertanyaan_baru(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/ppdb-form-fields', [
            'key' => 'nama_wali',
            'label' => 'Nama Wali Murid',
            'type' => 'select',
            'width' => 'full',
            'options_text' => "ayah = Ayah\nibu = Ibu",
            'is_active' => '1',
            'is_required' => '1',
        ])->assertRedirect('/admin/ppdb-form-fields');

        $field = PpdbFormField::where('key', 'nama_wali')->sole();

        $this->assertSame(['ayah' => 'Ayah', 'ibu' => 'Ibu'], $field->optionList());
        $this->assertTrue($field->is_active);
    }

    public function test_admin_ditolak_saat_pertanyaan_kurang_valid(): void
    {
        $this->actingAs($this->admin());

        $this->post('/admin/ppdb-form-fields', [
            'key' => 'Key Tidak Valid',
            'label' => 'Tanpa Opsi',
            'type' => 'select',
            'width' => 'full',
        ])->assertSessionHasErrors(['key', 'options_text']);

        $this->assertSame(0, PpdbFormField::where('key', 'nama_wali')->count());
    }

    public function test_admin_tidak_boleh_mengganti_key_dan_tipe(): void
    {
        $this->actingAs($this->admin());

        $field = PpdbFormField::where('key', 'phone')->sole();

        $this->put("/admin/ppdb-form-fields/{$field->id}", [
            'key' => 'nomor_hp_baru',
            'label' => 'No. HP/WA Orang Tua',
            'type' => 'text',
            'width' => 'full',
            'is_active' => '1',
        ])->assertRedirect('/admin/ppdb-form-fields');

        $field->refresh();

        $this->assertSame('phone', $field->key);
        $this->assertSame('tel', $field->type);
    }

    public function test_admin_tidak_boleh_menghapus_pertanyaan_inti(): void
    {
        $this->actingAs($this->admin());

        $field = PpdbFormField::where('key', 'payment_proof')->sole();

        $this->delete("/admin/ppdb-form-fields/{$field->id}")->assertRedirect();

        $this->assertSame(1, PpdbFormField::where('key', 'payment_proof')->count());
    }

    public function test_admin_toggle_menyembunyikan_pertanyaan(): void
    {
        $this->actingAs($this->admin());

        $field = PpdbFormField::where('key', 'origin_school')->sole();

        $this->patch("/admin/ppdb-form-fields/{$field->id}/toggle")->assertRedirect();

        $this->assertFalse($field->fresh()->is_active);
    }

    public function test_admin_bisa_mengatur_urutan(): void
    {
        $this->actingAs($this->admin());

        $fields = PpdbFormField::query()->ordered()->get();
        $reversed = $fields->pluck('id')->reverse()->values()->all();

        $this->put('/admin/ppdb-form-fields/reorder', ['order' => $reversed])->assertRedirect();

        $this->assertSame(
            $reversed,
            PpdbFormField::query()->ordered()->pluck('id')->all()
        );
    }

    public function test_admin_bisa_mengunggah_dokumen_verifikasi_manual(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin());

        $ppdb = Ppdb::create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'gender' => 'P',
            'status' => 'pending',
        ]);

        $this->post("/admin/ppdb/{$ppdb->id}/documents", [
            'type' => 'kk',
            'file' => UploadedFile::fake()->create('kk.jpg', 120, 'image/jpeg'),
        ])->assertRedirect();

        $document = PpdbDocument::query()->sole();

        $this->assertSame('kk', $document->type);
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_admin_ditolak_untuk_tipe_dokumen_di_luar_daftar(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin());

        $ppdb = Ppdb::create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'status' => 'pending',
        ]);

        $this->post("/admin/ppdb/{$ppdb->id}/documents", [
            'type' => 'virus',
            'file' => UploadedFile::fake()->create('virus.jpg', 120, 'image/jpeg'),
        ])->assertSessionHasErrors('type');
    }

    public function test_admin_bisa_mengisi_data_verifikasi_manual(): void
    {
        $this->actingAs($this->admin());

        $ppdb = Ppdb::create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'status' => 'pending',
        ]);

        $this->put("/admin/ppdb/{$ppdb->id}/details", [
            'nisn' => '1234567890',
            'religion' => 'Islam',
            'father_name' => 'Bapak Sulaiman',
            'mother_name' => 'Ibu Rahma',
            'admin_notes' => 'Dokumen sudah dicek.',
        ])->assertRedirect();

        $ppdb->refresh();

        $this->assertSame('1234567890', $ppdb->nisn);
        $this->assertSame('Bapak Sulaiman', $ppdb->father_name);
    }

    public function test_admin_bisa_membuat_field_berkas_kustom(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin());

        $this->post('/admin/ppdb-form-fields', [
            'key' => 'surat_kelulusan',
            'label' => 'Surat Keterangan Lulus',
            'type' => 'file',
            'width' => 'full',
            'accept' => 'pdf',
            'max_kb' => 2048,
            'is_active' => '1',
            'is_required' => '1',
        ])->assertRedirect('/admin/ppdb-form-fields');

        // Pendaftar wajib mengunggah field berkas baru ini.
        $this->post('/ppdb', $this->validPayload())->assertSessionHasErrors('surat_kelulusan');

        $this->post('/ppdb', $this->validPayload([
            'surat_kelulusan' => UploadedFile::fake()->create('skl.pdf', 200, 'application/pdf'),
        ]))->assertSessionHasNoErrors();

        $ppdb = Ppdb::query()->sole();

        $this->assertTrue($ppdb->hasDocument('surat_kelulusan'));
        $this->assertTrue($ppdb->hasDocument('payment_proof'));

        // Pendaftar boleh melihat berkas sendiri lewat sesi pendaftaran.
        $this->withSession(['ppdb_registration_id' => $ppdb->id])
            ->get(route('ppdb.document', [$ppdb, 'surat_kelulusan']))
            ->assertSuccessful();

        // Admin melihat keduanya sebagai tautan, bukan path mentah.
        // Admin melihat keduanya sebagai tautan, bukan path mentah.
        $html = $this->get("/admin/ppdb/{$ppdb->id}")->assertSuccessful()->getContent();

        $this->assertStringContainsString(route('admin.ppdb.document', [$ppdb, 'surat_kelulusan']), $html);
        $this->assertStringNotContainsString('ppdb/documents/'.$ppdb->id.'/', $html);
    }

    public function test_motto_sekolah_tampil_di_bawah_nama_di_hero_beranda(): void
    {
        $html = $this->get('/')->assertSuccessful()->getContent();

        $motto = Setting::get('site_motto');
        $name = Setting::get('site_name');

        $this->assertSame('The Best Way For Shaping The Future', $motto);
        $this->assertStringContainsString($motto, $html);

        // Motto harus muncul setelah nama sekolah, bukan di atasnya.
        $this->assertGreaterThan(
            strpos($html, $name),
            strpos($html, $motto),
            'Motto harus tampil di bawah nama sekolah pada hero'
        );
    }

    public function test_admin_bisa_mengganti_motto_sekolah(): void
    {
        $this->actingAs($this->admin());

        $this->put('/admin/settings', [
            'site_name' => 'SMAIT Tahfizh Al-Fatih Pekanbaru',
            'site_motto' => 'The Best Way For Shaping The Future',
        ])->assertRedirect('/admin/settings');

        $this->assertStringContainsString(
            'The Best Way For Shaping The Future',
            $this->get('/')->assertSuccessful()->getContent()
        );
    }

    public function test_halaman_detail_admin_menampilkan_tanggal_lahir_dengan_benar(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin());

        $this->post('/ppdb', $this->validPayload([
            'birth_date' => '2012-04-17',
        ]));

        $ppdb = Ppdb::query()->sole();

        $this->get("/admin/ppdb/{$ppdb->id}")
            ->assertSuccessful()
            ->assertSee('17 Apr 2012');

        // Halaman sukses pendaftar juga merender tanggal ini.
        $this->withSession(['ppdb_registration_id' => $ppdb->id])
            ->get(route('ppdb.success', $ppdb))
            ->assertSuccessful()
            ->assertSee('17 Apr 2012');
    }

    public function test_halaman_kelola_formulir_dapat_dirender(): void
    {
        $this->actingAs($this->admin());

        $field = PpdbFormField::where('key', 'program')->sole();

        $this->get('/admin/ppdb-form-fields')
            ->assertSuccessful()
            ->assertSee('Kelola Formulir PPDB')
            ->assertSee('Program yang Dipilih');

        $this->get('/admin/ppdb-form-fields/create')->assertSuccessful();

        $this->get("/admin/ppdb-form-fields/{$field->id}/edit")
            ->assertSuccessful()
            ->assertSee('TAKHOSUS');
    }

    public function test_halaman_detail_admin_menampilkan_jawaban_formulir(): void
    {
        $this->actingAs($this->admin());

        $ppdb = Ppdb::create([
            'registration_number' => 'PPDB-2026-0001',
            'access_code' => 'ABC12345',
            'full_name' => 'Aisyah Rahma',
            'gender' => 'P',
            'program' => 'TAKHOSUS',
            'info_source' => 'instagram',
            'status' => 'pending',
        ]);

        $html = $this->get("/admin/ppdb/{$ppdb->id}")->assertSuccessful()->getContent();

        $this->assertStringContainsString('Program yang Dipilih', $html);
        $this->assertStringContainsString('Takhousus', $html);
        $this->assertStringContainsString('Sumber Informasi Mengenai Al-Fatih', $html);
        $this->assertStringContainsString('Instagram', $html);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'full_name' => 'Aisyah Rahma',
            'gender' => 'P',
            'birth_place' => 'Pekanbaru',
            'birth_date' => '2012-04-17',
            'address' => 'Jl. Melati No. 12',
            'origin_school' => 'SMPN 1 urethra',
            'program' => 'FULLDAY',
            'info_source' => 'website',
            'phone' => '081234567890',
            'payment_proof' => UploadedFile::fake()->create('bukti.jpg', 120, 'image/jpeg'),
            ...$overrides,
        ];
    }

    public function test_pencarian_dan_filter_status_ppdb_tidak_saling_menimpa(): void
    {
        $this->actingAs($this->admin());

        $cocokPending = $this->pendaftar(['full_name' => 'Budi Santoso', 'status' => 'pending']);
        $cocokAccepted = $this->pendaftar(['full_name' => 'Budi Wijaya', 'status' => 'accepted']);
        $lain = $this->pendaftar(['full_name' => 'Siti Aminah', 'status' => 'pending']);

        $response = $this->get('/admin/ppdb?q=Budi&status=pending')->assertSuccessful();

        $response->assertSee($cocokPending->registration_number);
        $response->assertDontSee($cocokAccepted->registration_number);
        $response->assertDontSee($lain->registration_number);
    }

    public function test_unggah_dokumen_manual_mengganti_dokumen_lama_tanpa_meninggalkan_baris_duplikat(): void
    {
        Storage::fake('local');

        $ppdb = $this->pendaftar();
        $this->actingAs($this->admin());

        $ppdb->documents()->create([
            'type' => 'kk',
            'name' => 'Kartu Keluarga (KK)',
            'file_path' => 'ppdb/lama/kk.pdf',
        ]);

        Storage::disk('local')->put('ppdb/lama/kk.pdf', 'isi-lama');

        $this->post("/admin/ppdb/{$ppdb->id}/documents", [
            'type' => 'kk',
            'file' => UploadedFile::fake()->create('kk.pdf', 50, 'application/pdf'),
        ])->assertRedirect();

        // Hanya satu baris untuk tipe ini, dan berkas lama sudah dibersihkan.
        $this->assertSame(1, $ppdb->documents()->where('type', 'kk')->count());
        Storage::disk('local')->assertMissing('ppdb/lama/kk.pdf');
    }

    private function pendaftar(array $overrides = []): Ppdb
    {
        return Ppdb::create([
            'registration_number' => 'PPDB-'.now()->year.'-'.str_pad((string) (Ppdb::count() + 1), 4, '0', STR_PAD_LEFT),
            'access_code' => strtoupper(Str::random(8)),
            'full_name' => 'Pendaftar',
            'status' => 'pending',
            ...$overrides,
        ]);
    }

    private function admin(): User
    {
        $role = Role::firstOrCreate(['slug' => 'super_admin'], ['name' => 'Super Admin']);

        $user = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin-form-test@test.local',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $user->role_id = $role->id;
        $user->save();

        return $user;
    }
}
