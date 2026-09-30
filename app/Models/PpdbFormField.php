<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PpdbFormField extends Model
{
    public const TYPES = ['text', 'textarea', 'tel', 'date', 'select', 'radio', 'file'];

    public const TYPE_LABELS = [
        'text' => 'Teks Pendek',
        'textarea' => 'Teks Panjang',
        'tel' => 'Nomor HP / WhatsApp',
        'date' => 'Tanggal',
        'select' => 'Dropdown',
        'radio' => 'Pilihan Radio',
        'file' => 'Unggah Berkas',
    ];

    public const WIDTHS = ['full', 'half'];

    /**
     * Key yang disimpan ke kolom nyata tabel `ppdb`.
     * Key di luar daftar ini disimpan ke kolom JSON `answers`.
     */
    public const COLUMN_KEYS = [
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
        'father_job',
        'mother_name',
        'mother_job',
        'family_income',
        'program',
        'info_source',
    ];

    /**
     * Key bertipe file yang disimpan ke kolom `photo`, bukan ke `ppdb_documents`.
     */
    public const PHOTO_KEY = 'photo';

    public const DEFAULT_MAX_KB = 4096;

    protected $fillable = [
        'key',
        'label',
        'type',
        'group_label',
        'width',
        'placeholder',
        'help_text',
        'options',
        'is_required',
        'accept',
        'max_kb',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'max_kb' => 'integer',
        ];
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['select', 'radio'], true);
    }

    public function isColumnKey(): bool
    {
        return in_array($this->key, self::COLUMN_KEYS, true) || $this->key === self::PHOTO_KEY;
    }

    /**
     * Daftar opsi sebagai pasangan nilai => label.
     * Mendukung dua format penyimpanan: daftar nilai (["Laki-laki"]) maupun
     * peta nilai => label ({"L": "Laki-laki"}).
     *
     * @return array<string, string>
     */
    public function optionList(): array
    {
        $options = $this->options;

        if (! is_array($options) || $options === []) {
            return [];
        }

        if (array_is_list($options)) {
            return array_combine($options, $options) ?: [];
        }

        return array_map('strval', $options);
    }

    public function maxKb(): int
    {
        return $this->max_kb ?: self::DEFAULT_MAX_KB;
    }

    public function maxLength(): int
    {
        return match ($this->type) {
            'textarea' => 2000,
            'tel' => 30,
            default => 150,
        };
    }

    /**
     * Daftar ekstensi yang diizinkan, diturunkan dari kolom `accept`.
     *
     * @return array<int, string>
     */
    public function acceptedExtensions(): array
    {
        $accept = strtolower((string) $this->accept);

        if ($accept === '') {
            return $this->key === self::PHOTO_KEY
                ? ['jpg', 'jpeg', 'png', 'webp']
                : ['jpg', 'jpeg', 'png', 'pdf'];
        }

        $extensions = collect(preg_split('/[\s,]+/', $accept) ?: [])
            ->map(fn ($value) => ltrim(trim($value), '.'))
            ->map(fn ($value) => preg_replace('#^[a-z]+/#', '', $value) ?: '')
            ->filter(fn ($value) => $value !== '' && ! str_contains($value, '*'))
            ->unique()
            ->values()
            ->all();

        return $extensions === [] ? ['jpg', 'jpeg', 'png', 'pdf'] : $extensions;
    }

    public function isPhotoUpload(): bool
    {
        if ($this->key !== self::PHOTO_KEY) {
            return false;
        }

        return collect($this->acceptedExtensions())
            ->contains(fn ($ext) => in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true));
    }

    public function htmlAccept(): string
    {
        return collect($this->acceptedExtensions())
            ->map(fn ($ext) => $this->isPhotoUpload() ? "image/{$ext}" : ".{$ext}")
            ->implode(',');
    }

    public function acceptLabel(): string
    {
        return collect($this->acceptedExtensions())->map(fn ($ext) => strtoupper($ext))->implode(' / ');
    }

    /**
     * @return array<int, string>
     */
    public function validationRules(): array
    {
        $presence = $this->is_required ? 'required' : 'nullable';

        if ($this->isFile()) {
            return array_filter([
                $presence,
                'file',
                'mimes:'.implode(',', $this->acceptedExtensions()),
                'max:'.$this->maxKb(),
            ]);
        }

        if ($this->hasOptions()) {
            $options = array_keys($this->optionList());

            return $options === [] ? [$presence] : [$presence, Rule::in($options)];
        }

        return match ($this->type) {
            'date' => [$presence, 'date', 'before_or_equal:today'],
            'tel' => [$presence, 'string', 'max:'.$this->maxLength(), 'regex:/^[0-9+\-\s()]+$/'],
            'textarea' => [$presence, 'string', 'max:'.$this->maxLength()],
            default => [$presence, 'string', 'max:'.$this->maxLength()],
        };
    }

    /**
     * Pesan error berbahasa Indonesia yang mengikuti label dinamis.
     *
     * @return array<string, string>
     */
    public function validationMessages(): array
    {
        $label = $this->label;

        $messages = [
            $label.'.required' => $this->isFile() ? "{$label} wajib diunggah." : "{$label} wajib diisi.",
            $label.'.file' => "Berkas {$label} tidak valid.",
            $label.'.image' => "{$label} harus berupa gambar.",
            $label.'.mimes' => "Format {$label} harus berupa: {$this->acceptLabel()}.",
            $label.'.in' => "Pilihan {$label} tidak valid.",
            $label.'.date' => "{$label} tidak valid.",
            $label.'.before_or_equal' => "{$label} tidak valid.",
            $label.'.regex' => "Format {$label} tidak valid.",
        ];

        if ($this->isFile()) {
            $messages[$label.'.max'] = "Ukuran {$label} maksimal ".$this->maxKbLabel().'.';
        } else {
            $messages[$label.'.max'] = "{$label} maksimal {$this->maxLength()} karakter.";
        }

        return $messages;
    }

    public function maxKbLabel(): string
    {
        return $this->maxKb() >= 1024
            ? number_format($this->maxKb() / 1024, 1).' MB'
            : $this->maxKb().' KB';
    }

    /**
     * Konfigurasi awal formulir, mengikuti urutan pertanyaan Google Form.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function defaults(): array
    {
        return [
            [
                'key' => 'full_name',
                'label' => 'Nama Lengkap Siswa',
                'type' => 'text',
                'width' => 'full',
                'placeholder' => 'Nama sesuai akta kelahiran',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'gender',
                'label' => 'Jenis Kelamin',
                'type' => 'select',
                'width' => 'half',
                'options' => ['L' => 'Laki-laki', 'P' => 'Perempuan'],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'birth_place',
                'label' => 'Tempat Lahir',
                'type' => 'text',
                'group_label' => 'Tempat & Tanggal Lahir Siswa',
                'width' => 'half',
                'placeholder' => 'Kota/Kabupaten',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'birth_date',
                'label' => 'Tanggal Lahir',
                'type' => 'date',
                'group_label' => 'Tempat & Tanggal Lahir Siswa',
                'width' => 'half',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'address',
                'label' => 'Alamat Lengkap',
                'type' => 'textarea',
                'width' => 'full',
                'placeholder' => 'Jalan, RT/RW, Kelurahan, Kecamatan, Kota',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'origin_school',
                'label' => 'Sekolah Asal Siswa',
                'type' => 'text',
                'width' => 'full',
                'placeholder' => 'Nama SMP/MTs asal',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'program',
                'label' => 'Program yang Dipilih',
                'type' => 'radio',
                'width' => 'full',
                'options' => [
                    'FULLDAY' => 'Full Day',
                    'BOARDING' => 'Boarding',
                    'TAKHOSUS' => 'Takhousus',
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'info_source',
                'label' => 'Sumber Informasi Mengenai Al-Fatih',
                'type' => 'select',
                'width' => 'full',
                'options' => [
                    'instagram' => 'Instagram',
                    'facebook' => 'Facebook',
                    'whatsapp' => 'WhatsApp',
                    'teman' => 'Teman / Sekolah',
                    'website' => 'Website Sekolah',
                    'lainnya' => 'Lainnya',
                ],
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'phone',
                'label' => 'No. HP/WA Orang Tua atau Wali Siswa',
                'type' => 'tel',
                'width' => 'full',
                'placeholder' => '08xxxxxxxxxx',
                'is_required' => true,
                'is_active' => true,
            ],
            [
                'key' => 'payment_proof',
                'label' => 'Bukti Pembayaran Formulir Pendaftaran',
                'type' => 'file',
                'width' => 'full',
                'accept' => 'jpg,jpeg,png,pdf',
                'max_kb' => 10240,
                'help_text' => 'Upload 1 file yang didukung. Maks 10 MB.',
                'is_required' => true,
                'is_active' => true,
            ],
        ];
    }
}
