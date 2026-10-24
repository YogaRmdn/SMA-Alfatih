<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflExam extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    protected $table = 'toefl_exams';

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'status',
        'total_questions',
        'duration_seconds',
        'start_at',
        'end_at',
        'show_result_to_student',
        'allow_review',
        'exam_code',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'show_result_to_student' => 'boolean',
            'allow_review' => 'boolean',
            'total_questions' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ToeflSection::class, 'exam_id')->orderBy('section_order');
    }

    public function accessCodes(): HasMany
    {
        return $this->hasMany(ToeflAccessCode::class, 'exam_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ToeflAttempt::class, 'exam_id');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Ujian sedang bisa diikuti (status dibuka + dalam jadwal).
     */
    public function isOpen(): bool
    {
        if (! $this->isPublished()) {
            return false;
        }

        if ($this->start_at && now()->lt($this->start_at)) {
            return false;
        }

        if ($this->end_at && now()->gt($this->end_at)) {
            return false;
        }

        return true;
    }

    public function statusLabel(): string
    {
        return config('toefl.exam_statuses.'.$this->status) ?? ucfirst($this->status);
    }

    /**
     * Total jumlah pertanyaan seluruh bagian (dihitung dari data soal).
     */
    public function questionCount(): int
    {
        return (int) $this->sections()
            ->withCount('questions')
            ->get()
            ->sum('questions_count');
    }
}
