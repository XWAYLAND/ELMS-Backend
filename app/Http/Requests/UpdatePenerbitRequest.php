<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePenerbitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_penerbit' => ['required', 'string', 'max:150'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_penerbit.required' => 'Nama Penerbit wajib diisi.',
        ];
    }
}
