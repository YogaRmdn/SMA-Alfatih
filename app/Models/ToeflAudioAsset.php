<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflAudioAsset extends Model
{
    protected $table = 'toefl_audio_assets';

    protected $fillable = [
        'section_id',
        'label',
        'storage_path',
        'duration_seconds',
        'mime_type',
        'size_bytes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'duration_seconds' => 'integer',
            'size_bytes' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ToeflSection::class, 'section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ToeflQuestion::class, 'audio_asset_id');
    }

    public function playEvents(): HasMany
    {
        return $this->hasMany(ToeflAudioPlayEvent::class, 'audio_asset_id');
    }

    /**
     * Jumlah pemutaran untuk sebuah attempt (dikunci saat menambah).
     */
    public function playsFor(int $attemptId): int
    {
        return $this->playEvents()->where('attempt_id', $attemptId)->count();
    }

    public function maxPlays(): int
    {
        return (int) config('toefl.audio.max_plays', 2);
    }
}
