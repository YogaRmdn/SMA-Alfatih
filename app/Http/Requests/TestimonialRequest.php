<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'alumni_year' => ['nullable', 'integer', 'min:1980', 'max:' . now()->year],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:262144'],
            'content' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'content.required' => 'Isi testimoni wajib diisi.',
            'photo.max' => 'Ukuran file tidak boleh lebih dari 256 MB.',
        ];
    }
}
