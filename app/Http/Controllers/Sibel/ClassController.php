<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\ClassRequest;
use App\Models\SibelAttempt;
use App\Models\SibelClass;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SibelClass::withCount('students')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Siswa yang sudah menyelesaikan kuesioner (untuk hitung per kelas).
        $completedStudentIds = SibelAttempt::where('status', SibelAttempt::STATUS_COMPLETED)
            ->whereHas('student')
            ->pluck('student_id')
            ->unique();

        $completedCounts = [];
        foreach ($classes as $class) {
            $completedCounts[$class->id] = $class->students()->pluck('id')
                ->intersect($completedStudentIds)
                ->count();
        }

        return view('sibel.classes.index', compact('classes', 'completedCounts'));
    }

    public function create()
    {
        return view('sibel.classes.form');
    }

    public function store(ClassRequest $request)
    {
        SibelClass::create($request->validated());

        return redirect()->route('sibel.classes.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SibelClass $class)
    {
        return view('sibel.classes.form', ['class' => $class]);
    }

    public function update(ClassRequest $request, SibelClass $class)
    {
        $class->update($request->validated());

        return redirect()->route('sibel.classes.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SibelClass $class)
    {
        $class->delete();

        return redirect()->route('sibel.classes.index')
            ->with('success', 'Kelas beserta data siswa di dalamnya berhasil dihapus.');
    }
}
