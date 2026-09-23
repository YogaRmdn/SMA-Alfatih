<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExtracurricularRequest;
use App\Models\Extracurricular;
use App\Traits\HasDeleteAll;

class ExtracurricularController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Extracurricular::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['image'];
    }
    public function index()
    {
        $extracurriculars = Extracurricular::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.extracurriculars.index', compact('extracurriculars'));
    }

    public function create()
    {
        return view('admin.extracurriculars.form');
    }

    public function store(ExtracurricularRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'extracurriculars');

        Extracurricular::create($data);

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function edit(Extracurricular $extracurricular)
    {
        return view('admin.extracurriculars.form', compact('extracurricular'));
    }

    public function update(ExtracurricularRequest $request, Extracurricular $extracurricular)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($extracurricular->image);
            $data['image'] = upload_file($request->file('image'), 'extracurriculars');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $extracurricular->update($data);

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Extracurricular $extracurricular)
    {
        delete_file($extracurricular->image);
        $extracurricular->delete();

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}
