<?php

namespace App\Http\Requests\Sibel;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Setiap pertanyaan selalu punya tepat tiga opsi A/B/C dengan pemetaan
     * kategori tetap (A=Visual, B=Auditori, C=Kinestetik) sesuai konfigurasi.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string', 'max:500'],
            'order_number' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['required', 'array', 'size:3'],
            'options.A' => ['required', 'string', 'max:255'],
            'options.B' => ['required', 'string', 'max:255'],
            'options.C' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question_text.required' => 'Teks pertanyaan wajib diisi.',
            'options.required' => 'Ketiga pilihan jawaban (A, B, C) wajib diisi.',
            'options.size' => 'Setiap pertanyaan harus memiliki tepat 3 pilihan (A, B, C).',
            'options.A.required' => 'Pilihan A wajib diisi.',
            'options.B.required' => 'Pilihan B wajib diisi.',
            'options.C.required' => 'Pilihan C wajib diisi.',
        ];
    }
}
