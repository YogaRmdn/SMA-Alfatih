<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file',
        'file_type',
        'file_size',
        'category',
        'downloads',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'downloads' => 'integer',
        ];
    }
}
