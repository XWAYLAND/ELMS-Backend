<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_jenis'   => ['required', 'string', 'max:50', 'unique:jenis,id_jenis'],
            'nama_jenis' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_jenis.required'   => 'ID Jenis wajib diisi.',
            'id_jenis.unique'     => 'ID Jenis sudah digunakan.',
            'nama_jenis.required' => 'Nama Jenis wajib diisi.',
        ];
    }
}
