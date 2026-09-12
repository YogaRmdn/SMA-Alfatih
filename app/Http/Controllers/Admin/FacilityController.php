<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityRequest;
use App\Models\Facility;

class FacilityController extends Controller
{
    public function index()
    {
        $facilities = Facility::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.facilities.form');
    }

    public function store(FacilityRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'facilities');

        Facility::create($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Facility $facility)
    {
        return view('admin.facilities.form', compact('facility'));
    }

    public function update(FacilityRequest $request, Facility $facility)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($facility->image);
            $data['image'] = upload_file($request->file('image'), 'facilities');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $facility->update($data);

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Facility $facility)
    {
        delete_file($facility->image);
        $facility->delete();

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }
}
