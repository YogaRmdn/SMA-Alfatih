<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:262144'],
            'link' => ['nullable', 'url', 'max:500'],
            'position' => ['nullable', Rule::in(['hero', 'top', 'bottom', 'side'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'link.url' => 'Link banner harus berupa URL yang valid.',
            'position.in' => 'Posisi banner tidak valid.',
            'image.max' => 'Ukuran file tidak boleh lebih dari 256 MB.',
        ];
    }
}