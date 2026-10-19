<?php

namespace App\Http\Requests\Sibel;

use Illuminate\Foundation\Http\FormRequest;

class ResultUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'teacher_note' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'teacher_note.max' => 'Catatan guru BK maksimal 1000 karakter.',
        ];
    }
}
