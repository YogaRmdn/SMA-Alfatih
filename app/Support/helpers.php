<?php

if (! function_exists('upload_file')) {
    /**
     * Simpan file upload ke storage dan kembalikan path.
     *
     * @param  \Illuminate\Http\UploadedFile|string|null  $file
     */
    function upload_file(mixed $file, string $directory, string $disk = 'public'): ?string
    {
        if (empty($file) || is_string($file)) {
            return $file;
        }

        return $file->store($directory, $disk);
    }
}

if (! function_exists('delete_file')) {
    function delete_file(?string $path, string $disk = 'public'): void
    {
        if ($path && \Illuminate\Support\Facades\Storage::disk($disk)->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk($disk)->delete($path);
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
