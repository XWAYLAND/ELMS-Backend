<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['sometimes', 'required', 'string', 'max:150'],
            'kelas'        => ['sometimes', 'required', 'string', 'max:20'],
            'fcm_token'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'kelas.required'        => 'Kelas wajib diisi.',
        ];
    }
}
