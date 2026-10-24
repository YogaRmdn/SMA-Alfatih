<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflAnswer extends Model
{
    protected $table = 'toefl_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_answer',
        'saved_at',
    ];

    protected function casts(): array
    {
        return [
            'saved_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ToeflAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ToeflQuestion::class, 'question_id');
    }
}
