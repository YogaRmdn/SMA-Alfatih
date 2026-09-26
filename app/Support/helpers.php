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

if (! function_exists('img_url_absolute')) {
    /**
     * Sama seperti img_url(), tetapi selalu menghasilkan URL absolut.
     * Wajib untuk Open Graph/Twitter Card karena crawler butuh URL lengkap.
     */
    function img_url_absolute(?string $path, string $fallback = ''): string
    {
        $url = img_url($path, $fallback);

        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($url, '/');
    }
}

if (! function_exists('meta_raw')) {
    /**
     * Normalisasi isi section Blade menjadi teks mentah.
     *
     * Illuminate\View\Concerns\ManagesLayouts::startSection() memanggil e()
     * pada nilai yang diisi lewat `@section('nama', $nilai)`, sehingga
     * $__env->yieldContent() mengembalikan teks yang SUDAH ter-escape.
     * Kalau lalu dicetak lagi dengan {{ }}, hasilnya ter-escape dua kali
     * (&amp;amp;). Helper ini decode sekali supaya output {{ }} meng-escape
     * tepat satu kali.
     */
    function meta_raw(?string $content): string
    {
        return trim(strip_tags(html_entity_decode((string) $content, ENT_QUOTES, 'UTF-8')));
    }
}

if (! function_exists('meta_text')) {
    /**
     * Ratakan teks meta (description/keywords) dan batasi panjangnya
     * agar tidak dipotong sendiri oleh Google.
     */
    function meta_text(?string $text, int $limit = 160): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)) ?? '');

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit);
        $lastSpace = mb_strrpos($cut, ' ');

        return rtrim($lastSpace !== false ? mb_substr($cut, 0, $lastSpace) : $cut, ' ,.;:-');
    }
}

if (! function_exists('breadcrumb_schema')) {
    /**
     * Bangun tag <script> JSON-LD BreadcrumbList dari daftar item.
     * Dipasang di halaman tambahan lewat @push('head').
     *
     * @param  array<int, array{name: string, url?: string|null}>  $items
     */
    function breadcrumb_schema(array $items): string
    {
        $elements = [];
        $position = 1;

        foreach ($items as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $element = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name,
            ];

            if (! empty($item['url'])) {
                $element['item'] = $item['url'];
            }

            $elements[] = $element;
        }

        if (count($elements) < 2) {
            return '';
        }

        $json = json_encode(
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $elements,
            ],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        return '<script type="application/ld+json">'.$json.'</script>';
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
