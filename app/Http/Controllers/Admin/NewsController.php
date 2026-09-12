<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Models\Category;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::query()
            ->with(['category', 'author'])
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when(request('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->when(request('status') === 'draft', fn ($q) => $q->where('is_published', false))
            ->when(request('status') === 'published', fn ($q) => $q->where('is_published', true))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.news.index', compact('news', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.news.form', compact('categories'));
    }

    public function store(NewsRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at')
            ? $request->date('published_at')
            : ($data['is_published'] ? now() : null);
        $data['thumbnail'] = upload_file($request->file('thumbnail'), 'news');

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(News $news)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.news.form', compact('news', 'categories'));
    }

    public function update(NewsRequest $request, News $news)
    {
        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            delete_file($news->thumbnail);
            $data['thumbnail'] = upload_file($request->file('thumbnail'), 'news');
        } else {
            unset($data['thumbnail']);
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at')
            ? $request->date('published_at')
            : ($data['is_published'] ? ($news->published_at ?? now()) : null);

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}
