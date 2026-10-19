<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelAttempt;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssessmentSubmitRequest extends FormRequest
{
    /**
     * Aturan dibangun dari daftar pertanyaan kuesioner pada sesi ini:
     * setiap pertanyaan wajib dijawab tepat satu opsi yang benar-benar
     * milik pertanyaan tersebut.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $attempt = $this->route('attempt');

        if (! $attempt instanceof SibelAttempt) {
            return [];
        }

        $rules = [];

        foreach ($attempt->questionnaire->questions as $question) {
            $presence = $question->is_required ? 'required' : 'nullable';

            $rules["answers.{$question->id}"] = [
                $presence,
                'integer',
                Rule::exists('options', 'id')->where('question_id', $question->id),
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $attempt = $this->route('attempt');
        $messages = [];

        if (! $attempt instanceof SibelAttempt) {
            return $messages;
        }

        foreach ($attempt->questionnaire->questions as $question) {
            $key = "answers.{$question->id}";

            if ($question->is_required) {
                $messages[$key.'.required'] = 'Masih ada pertanyaan yang belum dijawab. Jawab seluruh pertanyaan sebelum mengirim.';
            }

            $messages[$key.'.exists'] = 'Pilihan jawaban tidak valid untuk pertanyaan ini.';
            $messages[$key.'.integer'] = 'Pilihan jawaban tidak valid.';
        }

        return $messages;
    }
}
