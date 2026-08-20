<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jenis' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_jenis.required' => 'Nama Jenis wajib diisi.',
        ];
    }
}
