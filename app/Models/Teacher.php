<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    public const GENDER_L = 'L';

    public const GENDER_P = 'P';

    /**
     * Label gender untuk form admin.
     *
     * @var array<string, string>
     */
    public const GENDER_LABELS = [
        self::GENDER_L => 'Laki-laki',
        self::GENDER_P => 'Perempuan',
    ];

    protected $fillable = [
        'name',
        'gender',
        'nip',
        'photo',
        'subject',
        'position',
        'education',
        'phone',
        'email',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Urutkan guru: laki-laki dulu, lalu perempuan, lalu yang gender-nya
     * belum diisi (data lama sebelum kolom gender ada).
     *
     * Admin tidak perlu khawatir soal urutan input data. Cukup pilih gender
     * saat menambah guru, tampilannya otomatis rapi. `sort_order` jadi urutan
     * kedua supaya urutan pilihan admin di dalam masing-masing kelompok tetap
     * dihormati.
     *
     * Kalau kolom `gender` belum ada (kode ter-deploy duluan sebelum
     * migration dijalankan), falling back ke urutan lama supaya halaman
     * publik tetap bisa dibuka.
     *
     * @param  Builder<$this>  $query
     */
    public function scopeOrderedByGender(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        if (! Schema::hasColumn($table, 'gender')) {
            return $query
                ->orderBy($table.'.sort_order')
                ->orderBy($table.'.name');
        }

        return $query
            ->orderByRaw(
                'CASE '.$table.'.gender'
                .' WHEN ? THEN 0'
                .' WHEN ? THEN 1'
                .' ELSE 2 END',
                [self::GENDER_L, self::GENDER_P]
            )
            ->orderBy($table.'.sort_order')
            ->orderBy($table.'.name');
    }

    public function getGenderLabelAttribute(): ?string
    {
        if ($this->gender === null || $this->gender === '') {
            return null;
        }

        return self::GENDER_LABELS[$this->gender] ?? null;
    }
}
