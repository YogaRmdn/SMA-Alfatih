<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelQuestionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi pengisian kuesioner langsung di /sibel (satu halaman):
 * nama peserta wajib diisi, lalu setiap pertanyaan pada kuesioner
 * aktif wajib dijawab tepat satu opsi yang benar-benar miliknya.
 */
class FillQuestionnaireRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        if (is_string($this->input('full_name'))) {
            $this->merge([
                // Rapikan spasi ganda sebelum divalidasi & disimpan.
                'full_name' => preg_replace('/\s+/', ' ', trim($this->input('full_name'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'full_name' => ['required', 'string', 'min:3', 'max:120'],
        ];

        $questionnaire = SibelQuestionnaire::activeForFilling();

        if (! $questionnaire) {
            return $rules;
        }

        foreach ($questionnaire->questions as $question) {
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
        $messages = [
            'full_name.required' => 'Tulis nama Anda terlebih dahulu.',
            'full_name.min' => 'Nama minimal 3 karakter.',
            'full_name.max' => 'Nama maksimal 120 karakter.',
        ];

        $questionnaire = SibelQuestionnaire::activeForFilling();

        if (! $questionnaire) {
            return $messages;
        }

        foreach ($questionnaire->questions as $question) {
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
