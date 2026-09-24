<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('albums', 'slug')->ignore($this->album)],
            'description' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:262144'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul album wajib diisi.',
            'cover.max' => 'Ukuran file tidak boleh lebih dari 256 MB.',
        ];
    }
}
