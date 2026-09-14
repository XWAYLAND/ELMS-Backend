<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('pegawai');

        return [
            'nama'     => ['sometimes', 'required', 'string', 'max:150'],
            'email'    => ['nullable', 'email', 'max:255', Rule::unique('pegawai', 'email')->ignore($id, 'id_pegawai')],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'email.unique'  => 'Email sudah digunakan pegawai lain.',
            'password.min'  => 'Password minimal 6 karakter.',
        ];
    }
}
