<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ToeflAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_EXPIRED = 'expired';

    protected $table = 'toefl_attempts';

    protected $fillable = [
        'exam_id',
        'student_id',
        'access_code_id',
        'status',
        'started_at',
        'deadline_at',
        'submitted_at',
        'current_section_type',
        'session_version',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'deadline_at' => 'datetime',
            'submitted_at' => 'datetime',
            'session_version' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(ToeflExam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(ToeflStudent::class, 'student_id');
    }

    public function accessCode(): BelongsTo
    {
        return $this->belongsTo(ToeflAccessCode::class, 'access_code_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ToeflAnswer::class, 'attempt_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(ToeflResult::class, 'attempt_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ToeflEvent::class, 'attempt_id');
    }

    public function audioPlayEvents(): HasMany
    {
        return $this->hasMany(ToeflAudioPlayEvent::class, 'attempt_id');
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isFinished(): bool
    {
        return $this->status !== self::STATUS_IN_PROGRESS;
    }

    /**
     * Batas waktu terlewati (dihitung dari timestamp server — bukan jam siswa).
     */
    public function hasExpired(): bool
    {
        return $this->deadline_at !== null && now()->gt($this->deadline_at);
    }

    /**
     * Sisa detik sampai deadline server (negatif bila sudah lewat).
     */
    public function secondsRemaining(): int
    {
        if ($this->deadline_at === null) {
            return 0;
        }

        return (int) now()->diffInSeconds($this->deadline_at, false);
    }

    public function statusLabel(): string
    {
        return config('toefl.attempt_statuses.'.$this->status) ?? ucfirst($this->status);
    }
}
