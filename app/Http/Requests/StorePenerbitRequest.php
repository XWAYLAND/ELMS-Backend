<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenerbitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_penerbit'   => ['required', 'string', 'max:50', 'unique:penerbit,id_penerbit'],
            'nama_penerbit' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_penerbit.required'   => 'ID Penerbit wajib diisi.',
            'id_penerbit.unique'     => 'ID Penerbit sudah digunakan.',
            'nama_penerbit.required' => 'Nama Penerbit wajib diisi.',
        ];
    }
}
