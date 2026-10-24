<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi peristiwa browser (tab visibility / fullscreen) milik attempt aktif.
 */
class ToeflEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', Rule::in(array_keys(config('toefl.event_types', [])))],
        ];
    }

    public function attributes(): array
    {
        return ['event_type' => 'jenis peristiwa'];
    }
}
