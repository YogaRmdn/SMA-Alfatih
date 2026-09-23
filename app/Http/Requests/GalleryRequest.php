<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'album_id' => ['nullable', 'exists:albums,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:photo,video'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'video_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe galeri wajib dipilih.',
            'video_url.url' => 'URL video tidak valid.',
        ];
    }
}
