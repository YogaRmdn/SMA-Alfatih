<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflEvent extends Model
{
    protected $table = 'toefl_events';

    protected $fillable = [
        'attempt_id',
        'event_type',
        'event_timestamp',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'event_timestamp' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ToeflAttempt::class, 'attempt_id');
    }

    public function typeLabel(): string
    {
        return config('toefl.event_types.'.$this->event_type) ?? ucfirst($this->event_type);
    }
}
