<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ToeflAccessCode extends Model
{
    protected $table = 'toefl_access_codes';

    protected $fillable = [
        'student_id',
        'exam_id',
        'code_hash',
        'code_prefix',
        'expires_at',
        'revoked_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(ToeflStudent::class, 'student_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(ToeflExam::class, 'exam_id');
    }

    public function attempt(): HasOne
    {
        return $this->hasOne(ToeflAttempt::class, 'access_code_id');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    /**
     * Status kelayakan kode (null = valid).
     */
    public function invalidReason(): ?string
    {
        if ($this->isRevoked()) {
            return 'Kode akses telah dicabut. Hubungi pengawas ujian.';
        }

        if ($this->isExpired()) {
            return 'Kode akses sudah kedaluwarsa. Hubungi pengawas ujian.';
        }

        return null;
    }
}
