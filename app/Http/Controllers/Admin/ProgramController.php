<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramRequest;
use App\Models\Program;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.programs.index', compact('programs'));
    }

    public function create()
    {
        return view('admin.programs.form');
    }

    public function store(ProgramRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['image'] = upload_file($request->file('image'), 'programs');

        Program::create($data);

        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil ditambahkan.');
    }

    public function edit(Program $program)
    {
        return view('admin.programs.form', compact('program'));
    }

    public function update(ProgramRequest $request, Program $program)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            delete_file($program->image);
            $data['image'] = upload_file($request->file('image'), 'programs');
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $program->update($data);

        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        delete_file($program->image);
        $program->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Program berhasil dihapus.');
    }
}
