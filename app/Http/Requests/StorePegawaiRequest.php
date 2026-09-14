<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_pegawai' => ['required', 'string', 'max:20', 'unique:pegawai,id_pegawai'],
            'nama'       => ['required', 'string', 'max:150'],
            'email'      => ['nullable', 'email', 'max:255', 'unique:pegawai,email'],
            'password'   => ['required', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_pegawai.required' => 'ID Pegawai wajib diisi.',
            'id_pegawai.unique'   => 'ID Pegawai sudah terdaftar.',
            'nama.required'       => 'Nama wajib diisi.',
            'email.unique'        => 'Email sudah terdaftar.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal 6 karakter.',
        ];
    }
}
