<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ToeflSection extends Model
{
    protected $table = 'toefl_sections';

    protected $fillable = [
        'exam_id',
        'section_type',
        'title',
        'duration_seconds',
        'section_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'section_order' => 'integer',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(ToeflExam::class, 'exam_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ToeflQuestion::class, 'section_id')->orderBy('question_order');
    }

    public function passages(): HasMany
    {
        return $this->hasMany(ToeflPassage::class, 'section_id')->orderBy('passage_order');
    }

    public function audioAssets(): HasMany
    {
        return $this->hasMany(ToeflAudioAsset::class, 'section_id');
    }

    public function typeLabel(): string
    {
        return config('toefl.sections.'.$this->section_type.'.label') ?? ucfirst($this->section_type);
    }

    /**
     * Format durasi ke jam:menit.
     */
    public function durationLabel(): string
    {
        $hours = intdiv($this->duration_seconds, 3600);
        $minutes = intdiv($this->duration_seconds % 3600, 60);

        return $hours > 0 ? sprintf('%dh %dm', $hours, $minutes) : sprintf('%dm', $minutes);
    }
}
