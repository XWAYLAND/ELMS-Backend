<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nis'          => ['required', 'string', 'max:20', 'unique:anggota,nis'],
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'kelas'        => ['required', 'string', 'max:20'],
            'fcm_token'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'          => 'NIS wajib diisi.',
            'nis.unique'            => 'NIS sudah terdaftar.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'kelas.required'        => 'Kelas wajib diisi.',
        ];
    }
}
