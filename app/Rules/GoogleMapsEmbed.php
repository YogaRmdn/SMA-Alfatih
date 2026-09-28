<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi URL embed peta Google Maps.
 *
 * URL ini dipakai sebagai iframe src di halaman publik, jadi harus dibatasi
 * ketat: hanya HTTPS, hanya host Google Maps, dan hanya path peta/embed. Tanpa
 * ini admin bisa menyimpan URL arbitrer yang dieksekusi browser pengunjung.
 */
class GoogleMapsEmbed implements ValidationRule
{
    protected const ALLOWED_HOSTS = [
        'maps.google.com',
        'maps.google.co.id',
        'www.google.com',
        'www.google.co.id',
        'google.com',
        'google.co.id',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value)) {
            $fail('URL embed peta harus berupa teks.');

            return;
        }

        $parts = parse_url($value);

        if (($parts['scheme'] ?? null) !== 'https' || empty($parts['host'])) {
            $fail('URL embed peta harus menggunakan HTTPS.');

            return;
        }

        if (! in_array(strtolower($parts['host']), self::ALLOWED_HOSTS, true)) {
            $fail('URL embed peta hanya diizinkan dari Google Maps.');

            return;
        }

        $path = $parts['path'] ?? '';
        $query = $parts['query'] ?? '';

        if (! str_contains($path, '/maps/') && ! str_contains($query, 'output=embed')) {
            $fail('URL embed peta tidak valid.');
        }
    }
}
