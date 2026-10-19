<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelOption extends Model
{
    protected $table = 'options';

    protected $fillable = [
        'question_id',
        'option_code',
        'option_text',
        'category_code',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SibelQuestion::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SibelAnswer::class, 'option_id');
    }

    /**
     * Label kategori sesuai konfigurasi (mis. "Visual").
     */
    public function categoryLabel(): string
    {
        return config("sibel.categories.{$this->category_code}.label") ?? ucfirst($this->category_code);
    }
}
