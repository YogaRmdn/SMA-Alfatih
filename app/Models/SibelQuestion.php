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
