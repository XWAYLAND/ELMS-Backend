<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenulisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_penulis'   => ['required', 'string', 'max:50', 'unique:penulis,id_penulis'],
            'nama_penulis' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_penulis.required'   => 'ID Penulis wajib diisi.',
            'id_penulis.unique'     => 'ID Penulis sudah digunakan.',
            'nama_penulis.required' => 'Nama Penulis wajib diisi.',
        ];
    }
}
