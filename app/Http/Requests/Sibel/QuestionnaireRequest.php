<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelQuestionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionnaireRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:150'],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'version' => ['required', 'string', 'max:20'],
            'scoring_method' => ['nullable', Rule::in(array_keys(config('sibel.scoring_methods', [])))],
            'status' => ['required', Rule::in(array_keys(SibelQuestionnaire::STATUS_LABELS))],
            'allow_retake' => ['nullable', 'boolean'],
            'needs_verification' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul kuesioner wajib diisi.',
            'version.required' => 'Versi instrumen wajib diisi.',
            'scoring_method.in' => 'Metode penilaian tidak valid.',
            'status.in' => 'Status kuesioner tidak valid.',
        ];
    }
}
