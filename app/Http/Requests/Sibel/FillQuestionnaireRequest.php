<?php

namespace App\Http\Requests\Sibel;

use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi pengisian kuesioner langsung di /sibel (satu halaman):
 * identitas peserta (nama + kelas) wajib diisi dan harus konsisten dengan
 * data siswa yang sudah terdaftar, lalu setiap pertanyaan pada kuesioner
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
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id'),
                // Nama+kelas harus konsisten dengan data siswa agar identitas
                // tidak tertukar antar kelas dan pengisian ulang tidak bisa
                // dilewatkan dengan memilih kelas berbeda.
                function (string $attribute, mixed $value, Closure $fail): void {
                    $name = (string) $this->input('full_name');

                    if ($name === '' || ! SibelClass::query()->whereKey($value)->exists()) {
                        return; // nama divalidasi terpisah; kelas tak dikenal pakai pesan `exists`.
                    }

                    $matches = SibelStudent::query()
                        ->where('status', SibelStudent::STATUS_ACTIVE)
                        ->whereRaw('LOWER(full_name) = ?', [mb_strtolower($name)])
                        ->with('schoolClass')
                        ->get();

                    if ($matches->isEmpty()
                        || $matches->contains(fn (SibelStudent $s) => (int) $s->class_id === (int) $value)
                        || $matches->contains(fn (SibelStudent $s) => $s->class_id === null)) {
                        return; // cocok, atau identitas lama tanpa kelas (diadopsi kelas pilihan).
                    }

                    $registered = $matches->pluck('schoolClass')->filter()->pluck('name')->unique()->implode(', ');

                    $fail("Nama \"{$name}\" tercatat di kelas {$registered}. Pilih kelas yang sesuai, atau hubungi guru BK bila data Anda salah.");
                },
            ],
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
            'class_id.required' => 'Pilih kelas Anda terlebih dahulu.',
            'class_id.integer' => 'Kelas tidak valid — muat ulang halaman lalu pilih dari daftar.',
            'class_id.exists' => 'Kelas tidak dikenali — muat ulang halaman lalu pilih dari daftar.',
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
