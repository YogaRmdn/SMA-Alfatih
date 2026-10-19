<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelQuestionnaire extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_ACTIVE => 'Sesi Dibuka',
        self::STATUS_CLOSED => 'Sesi Ditutup',
    ];

    protected $table = 'questionnaires';

    protected $fillable = [
        'title',
        'instructions',
        'version',
        'status',
        'allow_retake',
        'needs_verification',
    ];

    protected function casts(): array
    {
        return [
            'allow_retake' => 'boolean',
            'needs_verification' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SibelQuestion::class, 'questionnaire_id')
            ->orderBy('order_number')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(SibelAttempt::class, 'questionnaire_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'bg-emerald-100 text-emerald-700',
            self::STATUS_CLOSED => 'bg-slate-100 text-slate-600',
            default => 'bg-amber-100 text-amber-700',
        };
    }
}
