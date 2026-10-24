<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflPassage extends Model
{
    protected $table = 'toefl_passages';

    protected $fillable = [
        'section_id',
        'title',
        'passage_text',
        'passage_order',
    ];

    protected function casts(): array
    {
        return [
            'passage_order' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ToeflSection::class, 'section_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ToeflQuestion::class, 'passage_id')->orderBy('question_order');
    }
}
