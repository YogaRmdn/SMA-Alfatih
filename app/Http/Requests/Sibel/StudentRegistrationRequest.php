<?php

namespace App\Http\Requests\Sibel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pendaftaran mandiri terbuka untuk tamu; dibatasi throttle:10,1 di routes/sibel.php.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'class_id' => ['nullable', 'integer', Rule::exists('classes', 'id')],
            'student_number' => ['nullable', 'string', 'max:30', Rule::unique('students', 'student_number')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal 120 karakter.',
            'class_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'student_number.max' => 'Nomor induk maksimal 30 karakter.',
            'student_number.unique' => 'Nomor induk tersebut sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah dipakai akun lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}
