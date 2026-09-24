<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::query()
            ->orderBy('category')
            ->orderBy('title')
            ->paginate(12);

        return view('frontend.downloads.index', compact('downloads'));
    }

    public function download(Download $download)
    {
        $disk = Storage::disk('public');

        $path = $download->file;
        abort_if(! is_string($path), 404, 'File tidak ditemukan.');

        abort_if(
            $path === '' || $path !== str_replace('\\', '/', trim($path)) || str_starts_with($path, '/') || str_contains($path, '..'),
            404,
            'File tidak ditemukan.'
        );

        abort_if(! $disk->exists($path), 404, 'File tidak ditemukan.');

        $download->increment('downloads');

        $filename = Str::slug($download->title).'.pdf';

        return $disk->download($path, $filename);
    }
}