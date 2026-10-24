<?php

namespace Tests\Feature;

use App\Models\ToeflAttempt;
use App\Models\ToeflAudioAsset;
use App\Models\ToeflAudioPlayEvent;
use App\Models\ToeflExam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsToeflData;
use Tests\TestCase;

/**
 * Batas pemutaran audio Listening (server-authoritative):
 *  - maksimal 2 putaran per aset per attempt,
 *  - percobaan beruntun/konkuren tidak menembus batas (unique index),
 *  - stream hanya lewat URL bertanda tangan + session attempt pemilik.
 */
class ToeflAudioLimitTest extends TestCase
{
    use BuildsToeflData, RefreshDatabase;

    protected ToeflExam $exam;

    protected ToeflAttempt $attempt;

    protected int $audioId;

    protected function setUp(): void
    {
        parent::setUp();

        $built = $this->buildExam(1);
        $this->exam = $built['exam'];
        $code = $this->issueCode($this->exam);

        $this->attempt = $this->enterExam($code, $this->exam, 'Nadia Ayu', 'X-1');
        $this->beginExam($this->attempt);

        $audio = $this->buildAudio($built['sections']['listening']);
        $this->audioId = $audio->id;

        // Player hanya dirender bila soal menunjuk aset audio.
        $built['sections']['listening']->questions()->first()
            ->update(['audio_asset_id' => $audio->id]);
    }

    public function test_pemutaran_pertama_dan_kedua_diperbolehkan(): void
    {
        $first = $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('played', 1)
            ->assertJsonPath('remaining', 1);

        $this->assertNotEmpty($first->json('url'), 'Putaran pertama menghasilkan URL stream bertanda tangan.');

        $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertOk()
            ->assertJsonPath('played', 2)
            ->assertJsonPath('remaining', 0);

        $this->assertSame(2, ToeflAudioPlayEvent::where('attempt_id', $this->attempt->id)->count());
    }

    public function test_pemutaran_ketiga_ditolak_dengan_batas(): void
    {
        $this->postJson(route('toefl.audio.play', $this->audioId))->assertOk();
        $this->postJson(route('toefl.audio.play', $this->audioId))->assertOk();

        $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertStatus(429)
            ->assertJsonPath('limit_exceeded', true);

        // Batas tetap 2 — permintaan tambahan tidak menambah penghitung.
        $this->assertSame(2, ToeflAudioPlayEvent::where('attempt_id', $this->attempt->id)->count());
    }

    public function test_permintaan_beruntun_tidak_menembus_batas(): void
    {
        // 10 permintaan beruntun (semula "konkuren") — hanya 2 yang boleh tercatat.
        $ok = 0;
        $rejected = 0;

        for ($i = 0; $i < 10; $i++) {
            $status = $this->postJson(route('toefl.audio.play', $this->audioId))->status();
            $status === 200 ? $ok++ : $rejected++;
        }

        $this->assertSame(2, $ok, 'Hanya 2 pemutaran yang tercatat.');
        $this->assertSame(8, $rejected);
        $this->assertSame(2, ToeflAudioPlayEvent::where('attempt_id', $this->attempt->id)->count());
    }

    public function test_audio_ujian_lain_ditolak(): void
    {
        $foreign = $this->buildExam(1, ['title' => 'Ujian Asing'])['exam'];
        $foreignAudio = $this->buildAudio($foreign->sections()->first());

        $this->postJson(route('toefl.audio.play', $foreignAudio->id))
            ->assertStatus(404);

        $this->assertSame(0, ToeflAudioPlayEvent::count());
    }

    public function test_pemutaran_tanpa_session_ditolak(): void
    {
        // Lepas session attempt (mis. tab siswa lain).
        $this->flushSession();

        $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertStatus(401);
    }

    public function test_pemutaran_setelah_pengumpulan_ditolak(): void
    {
        $this->post(route('toefl.exam.submit'));

        $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertStatus(422);

        $this->assertSame(0, ToeflAudioPlayEvent::count());
    }

    public function test_stream_memerlukan_tanda_tangan_url(): void
    {
        // Tanpa signature -> ditolak.
        $this->get(route('toefl.audio.stream', $this->audioId))
            ->assertStatus(403);
    }

    public function test_stream_url_sah_mengalirkan_file_privat(): void
    {
        Storage::fake('local');

        $asset = ToeflAudioAsset::findOrFail($this->audioId);
        Storage::disk('local')->put($asset->storage_path, 'AUDIO-BYTES-UJI');

        $play = $this->postJson(route('toefl.audio.play', $this->audioId))
            ->assertOk()
            ->json();

        $stream = $this->get($play['url'])->assertOk();

        $this->assertSame('AUDIO-BYTES-UJI', $stream->streamedContent());
    }

    public function test_stream_url_tanpa_session_pemilik_ditolak(): void
    {
        Storage::fake('local');

        $asset = ToeflAudioAsset::findOrFail($this->audioId);
        Storage::disk('local')->put($asset->storage_path, 'RAHASIA');

        $play = $this->postJson(route('toefl.audio.play', $this->audioId))->json();

        // Lepas session (siswa lain / tab berbeda) -> signature sah tapi session beda.
        $this->flushSession();

        $this->get($play['url'])->assertStatus(403);
    }

    public function test_status_sisa_putaran_dikutip_di_halaman_ujian(): void
    {
        $this->postJson(route('toefl.audio.play', $this->audioId))->assertOk();

        // Halaman Listening memuat status pemutaran (sisa 1 dari 2).
        $this->get(route('toefl.exam.section', 'listening'))
            ->assertOk()
            ->assertSee('Sisa putar');
    }
}
