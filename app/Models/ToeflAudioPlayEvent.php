<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflAudioPlayEvent extends Model
{
    protected $table = 'toefl_audio_play_events';

    protected $fillable = [
        'attempt_id',
        'audio_asset_id',
        'play_number',
        'started_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'play_number' => 'integer',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ToeflAttempt::class, 'attempt_id');
    }

    public function audioAsset(): BelongsTo
    {
        return $this->belongsTo(ToeflAudioAsset::class, 'audio_asset_id');
    }
}
