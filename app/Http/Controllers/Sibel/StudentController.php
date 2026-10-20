<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\StudentRequest;
use App\Models\SibelClass;
use App\Models\SibelStudent;

class StudentController extends Controller
{
    public function index()
    {
        $students = SibelStudent::query()
            ->with('schoolClass')
            ->withCount(['attempts as completed_attempts_count' => fn ($q) => $q->where('status', 'completed')])
            ->when(request('q'), fn ($q, $search) => $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%"))
            ->when(request('class_id'), fn ($q, $classId) => $q->where('class_id', $classId))
            ->when(request('source'), fn ($q, $source) => $q->where('source', $source))
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        $classes = SibelClass::orderBy('name')->get();

        return view('sibel.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        return view('sibel.students.form', [
            'classes' => SibelClass::orderBy('name')->get(),
        ]);
    }

    public function store(StudentRequest $request)
    {
        SibelStudent::create($request->validated());

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan. Siswa kini bisa mengisi kuesioner lewat /sibel tanpa login.');
    }

    public function show(SibelStudent $student)
    {
        $student->load('schoolClass', 'user');

        $attempts = $student->attempts()
            ->with(['questionnaire', 'result'])
            ->latest('id')
            ->paginate(8);

        return view('sibel.students.show', compact('student', 'attempts'));
    }

    public function edit(SibelStudent $student)
    {
        return view('sibel.students.form', [
            'student' => $student,
            'classes' => SibelClass::orderBy('name')->get(),
        ]);
    }

    public function update(StudentRequest $request, SibelStudent $student)
    {
        $student->update($request->validated());

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(SibelStudent $student)
    {
        $user = $student->user;
        $student->delete();

        // Akun login website lama (bila ada) ikut dihapus agar tidak menggantung.
        $user?->delete();

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
