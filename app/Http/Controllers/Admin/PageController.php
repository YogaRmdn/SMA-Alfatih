<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::query()
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when(request('section'), fn ($q, $section) => $q->where('section', $section))
            ->orderBy('section')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form');
    }

    public function store(PageRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'pages');

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(PageRequest $request, Page $page)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($page->image);
            $data['image'] = upload_file($request->file('image'), 'pages');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page)
    {
        delete_file($page->image);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Halaman berhasil dihapus.');
    }
}
