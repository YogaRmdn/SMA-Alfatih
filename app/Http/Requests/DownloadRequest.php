<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => [
                $this->isMethod('put') || $this->isMethod('patch') ? 'nullable' : 'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
            'category' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul file wajib diisi.',
            'file.required' => 'File wajib diunggah.',
            'file.mimes' => 'File harus berupa PDF.',
        ];
    }
}
