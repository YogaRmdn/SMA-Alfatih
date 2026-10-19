<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\StudentRequest;
use App\Models\Role;
use App\Models\SibelClass;
use App\Models\SibelStudent;
use App\Models\User;

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
        $data = $request->validated();
        $data['user_id'] = $this->createLoginAccount($request);

        unset($data['email'], $data['password']);

        SibelStudent::create($data);

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
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
        $data = $request->validated();

        // Email/hanya dipakai saat membuat akun login baru.
        if ($student->user_id || ! ($data['email'] ?? null)) {
            unset($data['email'], $data['password']);
        } else {
            $data['user_id'] = $this->createLoginAccount($request);
            unset($data['email'], $data['password']);
        }

        $student->update($data);

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(SibelStudent $student)
    {
        $user = $student->user;
        $student->delete();

        // Akun login yang dibuat khusus untuk siswa ini ikut dihapus.
        $user?->delete();

        return redirect()->route('sibel.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Buat akun login ber-role siswa bila form diisi email + password.
     */
    protected function createLoginAccount(StudentRequest $request): ?int
    {
        $email = $request->validated('email');

        if (! $email) {
            return null;
        }

        $role = Role::firstOrCreate(
            ['slug' => 'siswa'],
            ['name' => 'Siswa', 'description' => 'Mengisi kuesioner gaya belajar dan melihat hasilnya sendiri.']
        );

        $user = new User;
        $user->name = $request->validated('full_name');
        $user->email = $email;
        $user->password = (string) $request->validated('password');
        $user->email_verified_at = now();
        $user->role_id = $role->id;
        $user->save();

        return $user->id;
    }
}
