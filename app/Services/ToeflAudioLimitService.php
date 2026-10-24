<?php

namespace App\Services;

use App\Models\ToeflAttempt;
use App\Models\ToeflAudioAsset;
use App\Models\ToeflAudioPlayEvent;
use Illuminate\Support\Facades\DB;

/**
 * Pembatas pemutaran audio TOEFL (server-authoritative).
 *
 * Aturan: maksimal N (default 2) PEMUTARAN per aset per attempt.
 * Penghitung dijadwalkan dengan transaksi + kunci baris (unique index pada
 * (attempt, asset, play_number)) sehingga permintaan GANDA yang berbarengan
 * tidak dapat menembus batas — hanya satu permintaan yang berhasil mencatat
 * play_number tertentu, sisanya menerima penolakan.
 */
class ToeflAudioLimitService
{
    /**
     * Catat satu kali pemutaran. Melempar AudioPlayLimitExceededException
     * bila batas sudah tercapai.
     *
     * @throws AudioPlayLimitExceededException
     */
    public function recordPlay(ToeflAttempt $attempt, ToeflAudioAsset $asset): ToeflAudioPlayEvent
    {
        return DB::transaction(function () use ($attempt, $asset) {
            $max = $asset->maxPlays();

            $played = ToeflAudioPlayEvent::where('attempt_id', $attempt->id)
                ->where('audio_asset_id', $asset->id)
                ->lockForUpdate()
                ->count();

            if ($played >= $max) {
                throw new AudioPlayLimitExceededException($max);
            }

            return ToeflAudioPlayEvent::create([
                'attempt_id' => $attempt->id,
                'audio_asset_id' => $asset->id,
                'play_number' => $played + 1,
                'started_at' => now(),
            ]);
        });
    }

    /**
     * Sisa kesempatan pemutaran untuk sebuah aset.
     *
     * @return array{played: int, max: int, remaining: int}
     */
    public function statusFor(ToeflAttempt $attempt, ToeflAudioAsset $asset): array
    {
        $max = $asset->maxPlays();
        $played = ToeflAudioPlayEvent::where('attempt_id', $attempt->id)
            ->where('audio_asset_id', $asset->id)
            ->count();

        return [
            'played' => $played,
            'max' => $max,
            'remaining' => max(0, $max - $played),
        ];
    }
}
