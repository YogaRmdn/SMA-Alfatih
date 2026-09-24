<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

const UPLOAD_ALLOWED_EXTENSIONS = [
    'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif',
    'ico', 'mp4', 'webm',
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip',
];

const UPLOAD_MAX_BYTES = 256 * 1024 * 1024;

if (! function_exists('upload_file')) {
    /**
     * Simpan file upload ke storage dengan ekstensi aman (dari whitelist server-side)
     * dan kembalikan path.
     *
     * Ekstensi file selalu ditentukan ulang di sisi server (guessExtension),
     * bukan dari nama file yang dikirim klien, untuk mencegah eksekusi
     * script berbahaya (mis. polyglot yang dikirim sebagai .php).
     *
     * @param  UploadedFile|string|null  $file
     */
    function upload_file(mixed $file, string $directory, string $disk = 'public'): ?string
    {
        if (empty($file) || is_string($file)) {
            return $file;
        }

        $extension = strtolower((string) ($file->guessExtension() ?? $file->getClientOriginalExtension()));

        if (! in_array($extension, UPLOAD_ALLOWED_EXTENSIONS, true)) {
            throw new InvalidArgumentException('Jenis file tidak diizinkan untuk diunggah.');
        }

        if ($file->getSize() > UPLOAD_MAX_BYTES) {
            throw new InvalidArgumentException('Ukuran file melebihi batas maksimal 256 MB.');
        }

        $filename = Str::random(40).'.'.$extension;

        return $file->storeAs($directory, $filename, $disk);
    }
}

if (! function_exists('delete_file')) {
    function delete_file(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}

if (! function_exists('img_url')) {
    /**
     * Resolusi path gambar agar bisa dari storage maupun public.
     */
    function img_url(?string $path, string $fallback = ''): string
    {
        if (empty($path)) {
            return $fallback ? asset($fallback) : '';
        }

        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return asset('storage/'.$path);
    }
}

if (! function_exists('format_bytes')) {
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return round($bytes / (1024 ** $pow), $precision).' '.$units[$pow];
    }
}

if (! function_exists('sanitize_html')) {
    /**
     * Bersihkan HTML dari elemen/atribut berbahaya (XSS) menggunakan HTMLPurifier.
     */
    function sanitize_html(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        return \Stevebauman\Purify\Facades\Purify::clean($html);
    }
}
