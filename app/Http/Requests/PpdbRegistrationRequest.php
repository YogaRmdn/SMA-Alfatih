<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PpdbRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['required', 'date', 'before:today'],
            'religion' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'nisn' => ['nullable', 'digits_between:8,10'],
            'origin_school' => ['required', 'string', 'max:150'],
            'father_name' => ['required', 'string', 'max:150'],
            'mother_name' => ['required', 'string', 'max:150'],
            'father_job' => ['nullable', 'string', 'max:150'],
            'mother_job' => ['nullable', 'string', 'max:150'],
            'family_income' => ['nullable', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'kk' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'birth_certificate' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'diploma' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'report_card' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before' => 'Tanggal lahir tidak valid.',
            'address.required' => 'Alamat wajib diisi.',
            'phone.required' => 'Nomor HP/WA wajib diisi.',
            'phone.regex' => 'Format nomor HP/WA tidak valid.',
            'origin_school.required' => 'Asal sekolah wajib diisi.',
            'father_name.required' => 'Nama ayah wajib diisi.',
            'mother_name.required' => 'Nama ibu wajib diisi.',
            'photo.required' => 'Foto siswa wajib diunggah.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'kk.required' => 'Kartu Keluarga (KK) wajib diunggah.',
            'birth_certificate.required' => 'Akta kelahiran wajib diunggah.',
            'diploma.required' => 'Ijazah/SKL wajib diunggah.',
            'report_card.required' => 'Rapor wajib diunggah.',
            '*.max' => 'Ukuran berkas terlalu besar.',
        ];
    }
}