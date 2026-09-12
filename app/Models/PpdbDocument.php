<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PpdbDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'ppdb_id',
        'name',
        'type',
        'file_path',
    ];

    public function ppdb(): BelongsTo
    {
        return $this->belongsTo(Ppdb::class);
    }
}
