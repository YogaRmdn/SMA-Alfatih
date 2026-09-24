<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        $add = function (string $path, ?string $changeFreq = null, ?string $priority = null, ?\DateTimeInterface $lastmod = null) use (&$urls) {
            $urls[] = [
                'loc' => url($path),
                'lastmod' => $lastmod?->toAtomString(),
                'changefreq' => $changeFreq,
                'priority' => $priority,
            ];
        };

        $add('/', 'daily', '1.0');
        $add('berita', 'daily', '0.9');
        $add('unduhan', 'weekly', '0.6');
        $add('tentang-sejarah', 'monthly', '0.8');
        $add('visi-misi', 'monthly', '0.8');
        $add('struktur-organisasi', 'monthly', '0.7');
        $add('ppdb', 'monthly', '0.9');
        $add('ppdb/status', 'monthly', '0.5');

        News::query()
            ->published()
            ->latest('published_at')
            ->select(['slug', 'published_at'])
            ->take(500)
            ->get()
            ->each(fn ($news) => $add('berita/'.$news->slug, 'weekly', '0.8', $news->published_at));

        return response()
            ->view('frontend.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }
}