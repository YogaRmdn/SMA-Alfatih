<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi tambah/edit pertanyaan — bentuk bergantung metode penilaian
 * kuesioner induknya:
 *
 * - Pilihan Tunggal (A/B/C): setiap pertanyaan punya tepat tiga opsi teks
 *   dengan pemetaan kategori tetap (A=Visual, B=Auditori, C=Kinestetik).
 * - Skala Likert: pernyataan dimiliki SATU kategori indikator
 *   (visual/auditori/kinestetik); empat opsi skala dibuat otomatis dari
 *   konfigurasi `sibel.likert.levels`.
 */
class QuestionRequest extends FormRequest
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
        $rules = [
            'question_text' => ['required', 'string', 'max:500'],
            'order_number' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_required' => ['nullable', 'boolean'],
        ];

        if ($this->questionnaire()?->isLikert()) {
            $rules['category_code'] = ['required', Rule::in(array_keys(config('sibel.categories', [])))];

            return $rules;
        }

        $rules += [
            'options' => ['required', 'array', 'size:3'],
            'options.A' => ['required', 'string', 'max:255'],
            'options.B' => ['required', 'string', 'max:255'],
            'options.C' => ['required', 'string', 'max:255'],
        ];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [
            'question_text.required' => 'Teks pertanyaan wajib diisi.',
        ];

        if ($this->questionnaire()?->isLikert()) {
            $messages['category_code.required'] = 'Kategori indikator pernyataan wajib dipilih (Visual/Auditori/Kinestetik).';
            $messages['category_code.in'] = 'Kategori indikator tidak valid.';

            return $messages;
        }

        $messages += [
            'options.required' => 'Ketiga pilihan jawaban (A, B, C) wajib diisi.',
            'options.size' => 'Setiap pertanyaan harus memiliki tepat 3 pilihan (A, B, C).',
            'options.A.required' => 'Pilihan A wajib diisi.',
            'options.B.required' => 'Pilihan B wajib diisi.',
            'options.C.required' => 'Pilihan C wajib diisi.',
        ];

        return $messages;
    }

    /**
     * Kuesioner induk — dari route `questionnaire` (tambah) atau
     * `question` (edit).
     */
    protected function questionnaire(): ?SibelQuestionnaire
    {
        $questionnaire = $this->route()->parameter('questionnaire');

        if ($questionnaire instanceof SibelQuestionnaire) {
            return $questionnaire;
        }

        $question = $this->route()->parameter('question');

        if ($question instanceof SibelQuestion) {
            return $question->questionnaire;
        }

        return null;
    }
}
