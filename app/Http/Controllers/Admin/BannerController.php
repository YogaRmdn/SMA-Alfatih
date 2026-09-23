<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Traits\HasDeleteAll;

class BannerController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Banner::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['image'];
    }
    public function index()
    {
        $banners = Banner::query()
            ->orderBy('position')
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form');
    }

    public function store(BannerRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'banners');

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($banner->image);
            $data['image'] = upload_file($request->file('image'), 'banners');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner)
    {
        delete_file($banner->image);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner berhasil dihapus.');
    }
}
