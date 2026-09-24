<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlbumRequest;
use App\Models\Album;
use App\Models\Gallery;
use App\Traits\HasDeleteAll;

class AlbumController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Album::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['cover'];
    }

    public function deleteAll()
    {
        if (Gallery::exists()) {
            return back()->with('error', 'Tidak dapat menghapus semua album karena masih ada foto/video di dalam galeri. Hapus galeri terlebih dahulu.');
        }

        Album::query()->chunkById(200, function ($albums) {
            foreach ($albums as $album) {
                delete_file($album->cover);
                $album->delete();
            }
        });

        return back()->with('success', 'Semua album berhasil dihapus.');
    }
    public function index()
    {
        $albums = Album::query()
            ->withCount('galleries')
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.albums.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.albums.form');
    }

    public function store(AlbumRequest $request)
    {
        $data = $request->validated();
        $data['cover'] = upload_file($request->file('cover'), 'albums');

        Album::create($data);

        return redirect()->route('admin.albums.index')->with('success', 'Album berhasil ditambahkan.');
    }

    public function edit(Album $album)
    {
        return view('admin.albums.form', compact('album'));
    }

    public function update(AlbumRequest $request, Album $album)
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            delete_file($album->cover);
            $data['cover'] = upload_file($request->file('cover'), 'albums');
        } else {
            unset($data['cover']);
        }

        $album->update($data);

        return redirect()->route('admin.albums.index')->with('success', 'Album berhasil diperbarui.');
    }

    public function destroy(Album $album)
    {
        if ($album->galleries()->exists()) {
            return back()->with('error', 'Album tidak dapat dihapus karena masih memiliki foto/video.');
        }

        delete_file($album->cover);
        $album->delete();

        return redirect()->route('admin.albums.index')->with('success', 'Album berhasil dihapus.');
    }
}
