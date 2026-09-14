<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'password'     => ['nullable', 'string', 'min:6'],
            'fcm_token'    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'kelas.required'        => 'Kelas wajib diisi.',
            'password.min'          => 'Password minimal 6 karakter.',
        ];
    }
}
