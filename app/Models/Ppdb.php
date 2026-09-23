<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Ppdb extends Model
{
    use HasFactory;

    protected $table = 'ppdb';

    protected $fillable = [
        'registration_number',
        'access_code',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'address',
        'phone',
        'email',
        'nisn',
        'origin_school',
        'father_name',
        'mother_name',
        'father_job',
        'mother_job',
        'family_income',
        'photo',
        'kk',
        'birth_certificate',
        'diploma',
        'report_card',
        'status',
        'admin_notes',
        'academic_year',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PpdbDocument::class);
    }

    public function document(string $type): HasOne
    {
        return $this->hasOne(PpdbDocument::class)->where('type', $type);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'lulus_administrasi' => 'Lulus Administrasi',
            'rejected' => 'Ditolak',
            'accepted' => 'Diterima',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-700',
            'verified' => 'bg-emerald-100 text-emerald-700',
            'lulus_administrasi' => 'bg-orange-100 text-orange-700',
            'rejected' => 'bg-red-100 text-red-700',
            'accepted' => 'bg-emerald-600 text-white',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    public static function generateRegistrationNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'PPDB-'.$year.'-';

        $latest = static::query()
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('registration_number');

        $sequence = $latest ? ((int) substr($latest, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function generateAccessCode(): string
    {
        return strtoupper(Str::random(8));
    }
}
