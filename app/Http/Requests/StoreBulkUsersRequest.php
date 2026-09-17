<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 10MB.',
        ];
    }
}
