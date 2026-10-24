<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToeflResult extends Model
{
    protected $table = 'toefl_results';

    protected $fillable = [
        'attempt_id',
        'listening_raw_score',
        'structure_raw_score',
        'reading_raw_score',
        'total_correct',
        'total_questions',
        'scaled_score',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'listening_raw_score' => 'integer',
            'structure_raw_score' => 'integer',
            'reading_raw_score' => 'integer',
            'total_correct' => 'integer',
            'total_questions' => 'integer',
            'scaled_score' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ToeflAttempt::class, 'attempt_id');
    }

    /**
     * Persentase benar total (0-100).
     */
    public function percentage(): float
    {
        if ($this->total_questions <= 0) {
            return 0.0;
        }

        return round(($this->total_correct / $this->total_questions) * 100, 1);
    }

    /**
     * Skor per bagian dalam bentuk array untuk tampilan.
     *
     * @return array<string, array{raw: int, label: string}>
     */
    public function sectionsBreakdown(): array
    {
        $sections = config('toefl.sections', []);
        $map = [
            'listening' => $this->listening_raw_score,
            'structure' => $this->structure_raw_score,
            'reading' => $this->reading_raw_score,
        ];

        $breakdown = [];
        foreach ($map as $type => $raw) {
            $breakdown[$type] = [
                'raw' => (int) $raw,
                'label' => $sections[$type]['label'] ?? ucfirst($type),
            ];
        }

        return $breakdown;
    }
}
