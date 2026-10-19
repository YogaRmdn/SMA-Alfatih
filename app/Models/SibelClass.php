<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'academic_year',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(SibelStudent::class, 'class_id');
    }

    public function label(): string
    {
        return $this->name.($this->academic_year ? ' — '.$this->academic_year : '');
    }
}
