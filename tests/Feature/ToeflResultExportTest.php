<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Ekspor hasil TOEFL ke XLSX.
 *
 * Dua bug pernah membuat endpoint ini balik 500 dan dijaga agar tidak kembali:
 *  - `new Cell(...)` — di OpenSpout v4 `Cell` abstract, harus `Cell::fromValue()`,
 *  - tipe kembalian `download()` yang salah (`StreamedResponse` padahal
 *    `response()->download()` mengembalikan `BinaryFileResponse`).
 */
class ToeflResultExportTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected const XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    protected ToeflExam $exam;

    protected string $code;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createToeflRoles();

        $this->exam = $this->buildExam(2)['exam'];
        $this->code = $this->issueCode($this->exam);
    }

    protected function manager(): User
    {
        $user = User::create([
            'name' => 'Admin TOEFL Uji',
            'email' => 'admin.toefl@uji.test',
            'password' => Hash::make('password'),
        ]);

        // role_id tidak ada di $fillable — diisi langsung.
        $user->role_id = Role::where('slug', 'admin_toefl')->value('id');
        $user->email_verified_at = now();
        $user->save();

        return $user->refresh();
    }

    /**
     * Selesaikan satu attempt lengkap dengan jawaban sehingga hasil bernilai.
     */
    protected function finishAttempt(): ToeflAttempt
    {
        $attempt = $this->enterExam($this->code, $this->exam);
        $this->beginExam($attempt);

        foreach ($this->allQuestions($this->exam) as $i => $question) {
            $this->postJson(route('toefl.exam.answer'), [
                'question_id' => $question->id,
                'selected_answer' => $i < 3 ? 'A' : 'B',
            ])->assertOk();
        }

        $this->post(route('toefl.exam.submit'))->assertRedirect(route('toefl.result'));

        return $attempt->refresh();
    }

    public function test_ekspor_hasil_menghasilkan_file_xlsx_valid(): void
    {
        $attempt = $this->finishAttempt();

        $response = $this->actingAs($this->manager())
            ->get(route('toefl.admin.results.export'));

        $response->assertOk();
        $response->assertHeader('content-type', self::XLSX);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        // XLSX adalah arsip ZIP — tandanya dua byte pertama "PK".
        $file = $response->baseResponse->getFile()->getPathname();
        $this->assertFileExists($file);
        $this->assertSame('PK', substr((string) file_get_contents($file), 0, 2));
    }

    public function test_ekspor_tetap_valid_walaupun_belum_ada_hasil(): void
    {
        $response = $this->actingAs($this->manager())
            ->get(route('toefl.admin.results.export'));

        $response->assertOk();
        $response->assertHeader('content-type', self::XLSX);

        $file = $response->baseResponse->getFile()->getPathname();
        $this->assertSame('PK', substr((string) file_get_contents($file), 0, 2));
    }

    public function test_ekspor_hanya_untuk_pengelola_toefl(): void
    {
        $this->finishAttempt();

        // Tamu (peserta tanpa akun) dialihkan ke halaman login.
        $this->get(route('toefl.admin.results.export'))->assertRedirect(route('login'));

        $this->actingAs($this->manager())->get(route('toefl.admin.results.export'))->assertOk();

        // Role lain di luar pengelola TOEFL ditolak.
        $siswa = Role::firstOrCreate(['slug' => 'siswa'], ['name' => 'Siswa']);

        $guest = User::create([
            'name' => 'Siswa Uji',
            'email' => 'siswa@uji.test',
            'password' => Hash::make('password'),
        ]);
        $guest->role_id = $siswa->id;
        $guest->email_verified_at = now();
        $guest->save();

        $this->actingAs($guest->refresh())->get(route('toefl.admin.results.export'))->assertForbidden();
    }
}
