<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi pemasukan peserta ke ujian TOEFL (TANPA register/login).
 *
 * Input: identitas yang ditulis peserta (nama + kelas) + pilihan ujian
 * + kode akses. Identitas ini yang mengikat attempt ke session browser.
 */
class ToeflAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'class_name' => ['required', 'string', 'max:60'],
            'exam_id' => ['required', 'integer', 'exists:toefl_exams,id'],
            'code' => ['required', 'string', 'max:32'],
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'class_name' => 'kelas',
            'exam_id' => 'ujian',
            'code' => 'kode akses',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'class_name.required' => 'Kelas wajib diisi.',
            'exam_id.required' => 'Pilih ujian terlebih dahulu.',
            'code.required' => 'Kode akses wajib diisi.',
            'code.max' => 'Kode akses terlalu panjang.',
        ];
    }
}
