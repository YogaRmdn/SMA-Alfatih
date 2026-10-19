<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelStudent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses sudah dibatasi middleware role pada routes/sibel.php.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'full_name' => ['required', 'string', 'max:120'],
            'class_id' => ['required', 'integer', Rule::exists('classes', 'id')],
            'student_number' => ['nullable', 'string', 'max:30', Rule::unique('students', 'student_number')
                ->ignore($student instanceof SibelStudent ? $student->id : null)],
            'status' => ['required', Rule::in(array_keys(SibelStudent::STATUS_LABELS))],

            // Pembuatan akun login siswa (opsional, hanya saat belum punya akun).
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['nullable', 'string', 'min:8', 'required_with:email'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap siswa wajib diisi.',
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'student_number.unique' => 'Nomor induk tersebut sudah terdaftar.',
            'email.unique' => 'Email tersebut sudah dipakai akun lain.',
            'email.email' => 'Format email tidak valid.',
            'password.required_with' => 'Password wajib diisi bila email login dibuat.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }
}
