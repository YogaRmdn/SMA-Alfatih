<?php

namespace App\Http\Requests;

use App\Services\PpdbFormDefinition;
use Illuminate\Foundation\Http\FormRequest;

class PpdbRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return app(PpdbFormDefinition::class)->rules();
    }

    public function messages(): array
    {
        return app(PpdbFormDefinition::class)->messages();
    }
}
