<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $action,
        ?User $user = null,
        string $description = '',
        ?string $ip = null,
        ?string $userAgent = null,
    ): self {
        try {
            return static::create([
                'user_id' => $user?->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => $ip,
                'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
            ]);
        } catch (Throwable) {
            // Jangan sampai pencatatan log mengganggu proses login/logout.
            return new self;
        }
    }
}