<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi penyimpanan jawaban (auto-save) — satu pertanyaan per request.
 *
 * answer_key TIDAK pernah diterima/dikirim di sini; validasi kecocokan soal
 * dengan attempt dilakukan di controller terhadap data server.
 */
class ToeflAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $options = config('toefl.answer_options', ['A', 'B', 'C', 'D']);

        return [
            'question_id' => ['required', 'integer', 'exists:toefl_questions,id'],
            // null = mengosongkan jawaban; selain itu harus salah satu opsi sah.
            'selected_answer' => ['nullable', Rule::in($options)],
        ];
    }

    public function attributes(): array
    {
        return [
            'question_id' => 'pertanyaan',
            'selected_answer' => 'jawaban',
        ];
    }

    public function messages(): array
    {
        return [
            'selected_answer.in' => 'Pilihan jawaban tidak valid.',
        ];
    }
}
