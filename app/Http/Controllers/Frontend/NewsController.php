<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::query()
            ->published()
            ->with(['category', 'author'])
            ->when(request('q'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            }))
            ->when(request('kategori'), fn ($q, $slug) => $q->whereHas('category', fn ($q) => $q->where('slug', $slug)))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->whereHas('news', fn ($q) => $q->published())
            ->withCount(['news as news_count' => fn ($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return view('frontend.news.index', compact('news', 'categories'));
    }

    public function show(News $news): View
    {
        $news->load(['category', 'author']);

        abort_unless($news->is_published && $news->published_at?->isPast(), 404);

        $news->increment('views');

        $comments = $news->comments()
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);

        $relatedNews = News::query()
            ->published()
            ->whereKeyNot($news->getKey())
            ->when($news->category_id, fn ($q) => $q->where('category_id', $news->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        $newsTitle = $news->title;

        return view('frontend.news.show', compact('news', 'comments', 'relatedNews', 'newsTitle'));
    }

    public function comment(News $news): RedirectResponse
    {
        abort_unless($news->is_published && $news->published_at?->isPast(), 404);

        $validated = request()->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'content.required' => 'Komentar wajib diisi.',
            'content.max' => 'Komentar maksimal 2000 karakter.',
        ]);

        $news->comments()->create($validated);

        return back()->with('success', 'Komentar berhasil dikirim dan akan tampil setelah disetujui admin.');
    }
}
