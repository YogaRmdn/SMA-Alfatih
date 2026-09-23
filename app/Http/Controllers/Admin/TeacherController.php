<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Models\Staff;
use App\Models\Teacher;
use App\Traits\HasDeleteAll;

class TeacherController extends Controller
{
    use HasDeleteAll;

    protected function deleteAllModel(): string
    {
        return Teacher::class;
    }

    protected function deleteAllFileColumns(): array
    {
        return ['photo'];
    }
    public function index()
    {
        $teachers = Teacher::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.form');
    }

    public function store(TeacherRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['photo'] = upload_file($request->file('photo'), 'teachers');

        Teacher::create($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.form', compact('teacher'));
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            delete_file($teacher->photo);
            $data['photo'] = upload_file($request->file('photo'), 'teachers');
        } else {
            unset($data['photo']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        delete_file($teacher->photo);
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dihapus.');
    }
}
