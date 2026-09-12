<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadRequest;
use App\Models\Download;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::query()
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when(request('category'), fn ($q, $category) => $q->where('category', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.downloads.index', compact('downloads'));
    }

    public function create()
    {
        return view('admin.downloads.form');
    }

    public function store(DownloadRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file'] = upload_file($file, 'downloads');
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        }

        Download::create($data);

        return redirect()->route('admin.downloads.index')->with('success', 'File berhasil ditambahkan.');
    }

    public function edit(Download $download)
    {
        return view('admin.downloads.form', compact('download'));
    }

    public function update(DownloadRequest $request, Download $download)
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            delete_file($download->file);
            $file = $request->file('file');
            $data['file'] = upload_file($file, 'downloads');
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        } else {
            unset($data['file']);
        }

        $download->update($data);

        return redirect()->route('admin.downloads.index')->with('success', 'File berhasil diperbarui.');
    }

    public function destroy(Download $download)
    {
        delete_file($download->file);
        $download->delete();

        return redirect()->route('admin.downloads.index')->with('success', 'File berhasil dihapus.');
    }
}
