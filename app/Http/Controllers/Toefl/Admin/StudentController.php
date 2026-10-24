<?php

namespace App\Http\Controllers\Toefl\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflStudent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Kelola data peserta TOEFL (identitas yang muncul otomatis saat peserta
 * masuk portal, atau didaftarkan lebih dulu oleh pengawas).
 * Kode akses dikelola di halaman ujian, bukan di sini.
 */
class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $students = ToeflStudent::withCount('attempts')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('full_name', 'like', "%{$q}%")
                        ->orWhere('student_number', 'like', "%{$q}%")
                        ->orWhere('class_name', 'like', "%{$q}%");
                });
            })
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('toefl.admin.students.index', compact('students', 'q'));
    }

    public function create(): View
    {
        return view('toefl.admin.students.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'student_number' => ['nullable', 'string', 'max:30', 'unique:toefl_students,student_number'],
            'email' => ['nullable', 'email', 'max:190', 'unique:toefl_students,email'],
            'class_name' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'in:active,inactive'],
        ], [], [
            'full_name' => 'nama lengkap',
            'student_number' => 'nomor induk',
            'class_name' => 'kelas',
        ]);

        $validated['status'] = $validated['status'] ?? ToeflStudent::STATUS_ACTIVE;

        ToeflStudent::create($validated);

        return redirect()->route('toefl.admin.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(ToeflStudent $student): View
    {
        return view('toefl.admin.students.edit', compact('student'));
    }

    public function update(Request $request, ToeflStudent $student): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'student_number' => ['nullable', 'string', 'max:30', 'unique:toefl_students,student_number,'.$student->id],
            'email' => ['nullable', 'email', 'max:190', 'unique:toefl_students,email,'.$student->id],
            'class_name' => ['nullable', 'string', 'max:60'],
            'status' => ['required', 'in:active,inactive'],
        ], [], [
            'full_name' => 'nama lengkap',
            'student_number' => 'nomor induk',
            'class_name' => 'kelas',
        ]);

        $student->update($validated);

        return redirect()->route('toefl.admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(ToeflStudent $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('toefl.admin.students.index')
            ->with('success', 'Data peserta beserta kode akses & attemptnya dihapus.');
    }
}
