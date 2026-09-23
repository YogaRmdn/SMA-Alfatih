<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Traits\HasDeleteAll;

class AnnouncementController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Announcement::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['attachment'];
    }
    public function index()
    {
        $announcements = Announcement::query()
            ->with('author')
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when(request('status') === 'draft', fn ($q) => $q->where('is_published', false))
            ->when(request('status') === 'published', fn ($q) => $q->where('is_published', true))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.form');
    }

    public function store(AnnouncementRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at')
            ? $request->date('published_at')
            : ($data['is_published'] ? now() : null);
        $data['attachment'] = upload_file($request->file('attachment'), 'announcements');

        Announcement::create($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.form', compact('announcement'));
    }

    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            delete_file($announcement->attachment);
            $data['attachment'] = upload_file($request->file('attachment'), 'announcements');
        } else {
            unset($data['attachment']);
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $request->filled('published_at')
            ? $request->date('published_at')
            : ($data['is_published'] ? ($announcement->published_at ?? now()) : null);

        $announcement->update($data);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        delete_file($announcement->attachment);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
