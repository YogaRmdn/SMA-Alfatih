<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('announcements', 'slug')->ignore($this->announcement)],
            'content' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'content.required' => 'Isi pengumuman wajib diisi.',
            'attachment.mimes' => 'Lampiran harus berupa file PDF, DOC, atau DOCX.',
        ];
    }
}
