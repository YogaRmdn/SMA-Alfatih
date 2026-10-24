<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelQuestion extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'questionnaire_id',
        'question_text',
        'category_code',
        'order_number',
        'is_required',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'order_number' => 'integer',
        ];
    }

    /**
     * Kuesioner dinilai dengan Skala Likert.
     */
    public function isLikert(): bool
    {
        return $this->questionnaire
            ? $this->questionnaire->isLikert()
            : false;
    }

    /**
     * Label kategori indikator (untuk pernyataan Likert), atau "-" bila tidak ada.
     */
    public function categoryLabel(): string
    {
        if ($this->category_code === null || $this->category_code === '') {
            return '—';
        }

        return config("sibel.categories.{$this->category_code}.label") ?? ucfirst($this->category_code);
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(SibelQuestionnaire::class, 'questionnaire_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(SibelOption::class, 'question_id')->orderBy('option_code');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SibelAnswer::class, 'question_id');
    }

    public function hasAnswers(): bool
    {
        return $this->answers()->exists();
    }
}
