<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SibelAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_LABELS = [
        self::STATUS_IN_PROGRESS => 'Sedang Mengerjakan',
        self::STATUS_COMPLETED => 'Selesai',
    ];

    protected $table = 'attempts';

    protected $fillable = [
        'student_id',
        'questionnaire_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(SibelStudent::class, 'student_id');
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(SibelQuestionnaire::class, 'questionnaire_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SibelAnswer::class, 'attempt_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(SibelResult::class, 'attempt_id');
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }
}
