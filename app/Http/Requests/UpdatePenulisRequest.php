<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenulisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_penulis' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_penulis.required' => 'Nama Penulis wajib diisi.',
        ];
    }
}
