<?php

namespace App\Http\Requests;

use App\Models\PpdbFormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PpdbFormFieldRequest extends FormRequest
{
    /**
     * Key yang tidak boleh dipakai karena akan bentrok dengan kolom atau
     * parameter internal formulir.
     *
     * @var array<int, string>
     */
    public const RESERVED_KEYS = [
        '_token',
        '_method',
        'id',
        'registration_number',
        'access_code',
        'status',
        'admin_notes',
        'academic_year',
        'answers',
        'photo',
        'created_at',
        'updated_at',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $field = $this->route('ppdbFormField');

        return [
            'key' => [
                $field ? 'sometimes' : 'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                'not_in:'.implode(',', self::RESERVED_KEYS),
                Rule::unique('ppdb_form_fields', 'key')->ignore($field?->getKey()),
            ],
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(PpdbFormField::TYPES)],
            'group_label' => ['nullable', 'string', 'max:255'],
            'width' => ['required', Rule::in(PpdbFormField::WIDTHS)],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'help_text' => ['nullable', 'string', 'max:500'],
            'options_text' => ['nullable', 'string', 'max:5000'],
            'accept' => ['nullable', 'string', 'max:255'],
            'max_kb' => ['nullable', 'integer', 'min:1', 'max:256000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'is_required' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.regex' => 'Key hanya boleh huruf kecil, angka, dan garis bawah, diawali huruf.',
            'key.not_in' => 'Key tersebut tidak boleh dipakai.',
            'key.unique' => 'Key tersebut sudah dipakai field lain.',
            'options_text.required' => 'Opsi wajib diisi untuk field dropdown atau pilihan.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');

            if (in_array($type, ['select', 'radio'], true) && $this->parseOptions() === []) {
                $validator->errors()->add('options_text', 'Opsi wajib diisi untuk field dropdown atau pilihan.');
            }

            if ($type === 'file' && ! $this->filled('accept')) {
                $validator->errors()->add('accept', 'Format berkas wajib diisi untuk field unggah berkas.');
            }
        });
    }

    /**
     * Opsi ditulis admin satu per baris. Baris berformat "nilai = label"
     * disimpan sebagai peta nilai => label, selain itu nilai = label.
     *
     * @return array<string, string>
     */
    public function parseOptions(): array
    {
        $raw = trim((string) $this->input('options_text'));

        if ($raw === '') {
            return [];
        }

        $options = [];

        foreach (preg_split('/\R/', $raw) ?: [] as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (str_contains($line, '=')) {
                [$value, $label] = array_pad(explode('=', $line, 2), 2, '');
                $value = trim($value);
                $label = trim($label) !== '' ? trim($label) : $value;
            } else {
                $value = $line;
                $label = $line;
            }

            if ($value === '') {
                continue;
            }

            $options[$value] = $label;
        }

        return $options;
    }
}
