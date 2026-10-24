<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelQuestionnaire extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const SCORING_METHOD_CHOICE = 'choice';

    public const SCORING_METHOD_LIKERT = 'likert';

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
        'scoring_method',
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

    /**
     * Kuesioner dinilai dengan Skala Likert (skor = jumlah nilai opsi).
     */
    public function isLikert(): bool
    {
        return $this->scoring_method === self::SCORING_METHOD_LIKERT;
    }

    public function scoringMethodLabel(): string
    {
        return config("sibel.scoring_methods.{$this->scoring_method}") ?? ucfirst($this->scoring_method);
    }

    /**
     * Nilai maksimum opsi Likert (mis. 4 pada skala 4 tingkat).
     */
    public function likertMaxValue(): int
    {
        return (int) collect(config('sibel.likert.levels'))->max('value');
    }

    /**
     * Jumlah pernyataan per kategori indikator (untuk skala maksimum per kategori).
     *
     * @return array<string, int>
     */
    public function itemCountsByCategory(): array
    {
        return $this->questions
            ->filter(fn (SibelQuestion $question) => $question->category_code !== null)
            ->countBy('category_code')
            ->all();
    }

    /**
     * Skor maksimum suatu kategori pada kuesioner Likert
     * (jumlah pernyataan kategori × nilai maksimum opsi).
     */
    public function maxLikertScore(?string $categoryKey): int
    {
        if ($categoryKey === null) {
            return 0;
        }

        return (int) ($this->itemCountsByCategory()[$categoryKey] ?? 0) * $this->likertMaxValue();
    }

    /**
     * Kuesioner yang bisa langsung diisi siswa di /sibel:
     * sesi aktif terbaru yang sudah punya pertanyaan.
     * Pertanyaan + opsi ikut dimuat (dipakai form isian & validasi).
     */
    public static function activeForFilling(): ?self
    {
        return static::query()
            ->where('status', self::STATUS_ACTIVE)
            ->whereHas('questions')
            ->with('questions.options')
            ->orderByDesc('id')
            ->first();
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
