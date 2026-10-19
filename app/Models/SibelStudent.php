<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SibelStudent extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /** Asal data: pendaftaran mandiri lewat /sibel/daftar. */
    public const SOURCE_SELF = 'self';

    /** Asal data: dibuat guru BK/admin lewat menu Data Siswa. */
    public const SOURCE_MANUAL = 'manual';

    public const STATUS_LABELS = [
        self::STATUS_ACTIVE => 'Aktif',
        self::STATUS_INACTIVE => 'Tidak Aktif',
    ];

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'class_id',
        'student_number',
        'full_name',
        'status',
        'source',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SibelClass::class, 'class_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(SibelAttempt::class, 'student_id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Sesi pengisian terakhir yang sudah selesai beserta hasilnya.
     */
    public function latestCompletedAttempt(): ?SibelAttempt
    {
        return $this->attempts()
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->latest('id')
            ->first();
    }

    public function hasCompletedAttempt(): bool
    {
        return $this->attempts()->where('status', SibelAttempt::STATUS_COMPLETED)->exists();
    }

    public function inProgressAttempt(): ?SibelAttempt
    {
        return $this->attempts()
            ->where('status', SibelAttempt::STATUS_IN_PROGRESS)
            ->latest('id')
            ->first();
    }
}
