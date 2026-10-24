<?php

namespace App\Http\Controllers\Toefl;

use App\Http\Controllers\Controller;
use App\Models\ToeflAttempt;
use App\Models\ToeflAudioAsset;
use App\Services\AudioPlayLimitExceededException;
use App\Services\ToeflAttemptService;
use App\Services\ToeflAudioLimitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pemutaran audio Listening — batas ketat di server.
 *
 * Endpoint `play` mencatat satu kali pemutaran (maks. {toefl.audio.max_plays}
 * per aset per attempt) LALU mengembalikan URL stream bertanda tangan yang
 * berumur pendek. Tanpa pencatatan -> tidak ada URL -> tidak ada pemutaran,
 * sehingga permintaan ganda/konkuren tidak dapat menembus batas.
 * File audio berada pada disk privat (di luar public/).
 */
class AudioController extends Controller
{
    public function __construct(
        protected ToeflAttemptService $attempts,
        protected ToeflAudioLimitService $audioLimit,
    ) {}

    /**
     * POST putar: validasi sesi + deadline, catat play, kembalikan URL stream.
     */
    public function play(Request $request, int $audioId): JsonResponse
    {
        $attempt = $this->attempt($request);

        if (! $attempt) {
            return response()->json(['ok' => false, 'message' => 'Sesi ujian tidak ditemukan.'], 401);
        }

        if ($attempt->isFinished()) {
            return response()->json(['ok' => false, 'message' => 'Ujian sudah dikumpulkan.'], 422);
        }

        $asset = ToeflAudioAsset::find($audioId);

        if (! $asset || $asset->section->exam_id !== $attempt->exam_id) {
            return response()->json(['ok' => false, 'message' => 'Audio tidak ditemukan.'], 404);
        }

        try {
            $play = $this->audioLimit->recordPlay($attempt, $asset);
        } catch (AudioPlayLimitExceededException $e) {
            return response()->json([
                'ok' => false,
                'limit_exceeded' => true,
                'message' => $e->getMessage(),
            ], 429);
        }

        $expiresAt = now()->addMinutes(10);
        $url = URL::temporarySignedRoute('toefl.audio.stream', $expiresAt, [
            'audio' => $asset->id,
            'attempt' => $attempt->id,
            'play' => $play->play_number,
        ]);

        $status = $this->audioLimit->statusFor($attempt, $asset);

        return response()->json([
            'ok' => true,
            'url' => $url,
            'played' => $status['played'],
            'max' => $status['max'],
            'remaining' => $status['remaining'],
        ]);
    }

    /**
     * GET stream: sahkan tanda tangan + pemilik attempt -> alirkan file privat.
     */
    public function stream(Request $request, int $audioId): StreamedResponse|JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['ok' => false, 'message' => 'Tautan tidak sah atau kedaluwarsa.'], 403);
        }

        $attemptId = (int) $request->query('attempt');
        $sessionAttempt = (int) $request->session()->get('toefl_attempt_id');

        // Stream hanya boleh diakses pemilik attempt yang sama (session aktif).
        if ($sessionAttempt <= 0 || $sessionAttempt !== $attemptId) {
            return response()->json(['ok' => false, 'message' => 'Sesi tidak sah.'], 403);
        }

        $asset = ToeflAudioAsset::findOrFail($audioId);
        $disk = (string) config('toefl.audio.disk', 'local');

        if (! Storage::disk($disk)->exists($asset->storage_path)) {
            return response()->json(['ok' => false, 'message' => 'File audio tidak tersedia.'], 404);
        }

        return Storage::disk($disk)->response(
            $asset->storage_path,
            'audio-'.$asset->id,
            [
                'Content-Type' => $asset->mime_type ?: 'audio/mpeg',
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    protected function attempt(Request $request): ?ToeflAttempt
    {
        $attemptId = (int) $request->session()->get('toefl_attempt_id');

        if ($attemptId <= 0) {
            return null;
        }

        $attempt = ToeflAttempt::with('exam')->find($attemptId);

        if ($attempt && $attempt->isInProgress() && $attempt->started_at !== null && $attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);
            $attempt->refresh();
        }

        return $attempt;
    }
}
