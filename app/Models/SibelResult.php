<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SibelResult extends Model
{
    public const STATUS_DOMINANT = 'dominant';

    public const STATUS_TIE = 'tie';

    protected $table = 'results';

    protected $fillable = [
        'attempt_id',
        'score_a',
        'score_b',
        'score_c',
        'percentage_a',
        'percentage_b',
        'percentage_c',
        'dominant_categories',
        'result_status',
        'interpretation',
        'recommendation',
        'questionnaire_version',
        'teacher_note',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'dominant_categories' => 'array',
            'percentage_a' => 'float',
            'percentage_b' => 'float',
            'percentage_c' => 'float',
            'score_a' => 'integer',
            'score_b' => 'integer',
            'score_c' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(SibelAttempt::class, 'attempt_id');
    }

    public function isTie(): bool
    {
        return $this->result_status === self::STATUS_TIE;
    }

    /**
     * Daftar label kategori dominan (["Visual"], ["Visual", "Auditori"], dst).
     *
     * @return array<int, string>
     */
    public function dominantLabels(): array
    {
        return array_map(
            fn (string $key) => config("sibel.categories.{$key}.label") ?? ucfirst($key),
            $this->dominant_categories ?? []
        );
    }

    public function dominantLabel(): string
    {
        return implode(' & ', $this->dominantLabels());
    }

    /**
     * Skor satu kategori kunci konfigurasi ("visual" | "auditori" | "kinestetik").
     */
    public function scoreOf(string $categoryKey): int
    {
        $column = config("sibel.categories.{$categoryKey}.score");

        return $column ? (int) $this->{$column} : 0;
    }

    public function percentageOf(string $categoryKey): float
    {
        $column = config("sibel.categories.{$categoryKey}.percentage");

        return $column ? (float) $this->{$column} : 0.0;
    }
}
