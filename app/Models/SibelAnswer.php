<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SibelAnswer extends Model
{
    protected $table = 'answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SibelAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SibelQuestion::class, 'question_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SibelOption::class, 'option_id');
    }
}
