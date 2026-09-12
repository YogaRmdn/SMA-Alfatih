<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryRequest;
use App\Models\Album;
use App\Models\Gallery;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::query()
            ->with('album')
            ->when(request('album_id'), fn ($q, $id) => $q->where('album_id', $id))
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $albums = Album::orderBy('title')->get();

        return view('admin.galleries.index', compact('galleries', 'albums'));
    }

    public function create()
    {
        $albums = Album::orderBy('title')->get();

        return view('admin.galleries.form', compact('albums'));
    }

    public function store(GalleryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($data['type'] === 'photo' && $request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'), 'galleries');
        }

        Gallery::create($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil ditambahkan.');
    }

    public function edit(Gallery $gallery)
    {
        $albums = Album::orderBy('title')->get();

        return view('admin.galleries.form', compact('gallery', 'albums'));
    }

    public function update(GalleryRequest $request, Gallery $gallery)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($data['type'] === 'photo' && $request->hasFile('image')) {
            delete_file($gallery->image);
            $data['image'] = upload_file($request->file('image'), 'galleries');
        }

        if ($data['type'] !== 'photo') {
            $data['image'] = null;
        }

        $gallery->update($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery)
    {
        delete_file($gallery->image);
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('success', 'Item galeri berhasil dihapus.');
    }
}
