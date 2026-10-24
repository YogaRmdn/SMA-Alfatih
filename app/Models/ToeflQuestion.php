<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflQuestion extends Model
{
    protected $table = 'toefl_questions';

    protected $fillable = [
        'section_id',
        'passage_id',
        'audio_asset_id',
        'question_type',
        'question_text',
        'options',
        'answer_key',
        'points',
        'question_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'points' => 'integer',
            'question_order' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ToeflSection::class, 'section_id');
    }

    public function passage(): BelongsTo
    {
        return $this->belongsTo(ToeflPassage::class, 'passage_id');
    }

    public function audio(): BelongsTo
    {
        return $this->belongsTo(ToeflAudioAsset::class, 'audio_asset_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ToeflAnswer::class, 'question_id');
    }

    public function typeLabel(): string
    {
        return config('toefl.question_types.'.$this->question_type) ?? ucfirst($this->question_type);
    }

    /**
     * Data aman untuk sisi siswa — TANPA kunci jawaban (answer_key).
     *
     * @return array{id: int, question_type: string, question_text: string, options: array, question_order: int, passage_id: int|null, audio_asset_id: int|null}
     */
    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'question_type' => $this->question_type,
            'question_text' => $this->question_text,
            'options' => $this->options ?? [],
            'question_order' => $this->question_order,
            'passage_id' => $this->passage_id,
            'audio_asset_id' => $this->audio_asset_id,
        ];
    }
}
