<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'role' => ['required', 'in:student,staff'],
            'name' => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:6'],
        ];

        if ($this->input('role') === 'student') {
            $rules['nis']   = ['required', 'string', 'max:20', 'unique:anggota,nis'];
            $rules['kelas'] = ['required', 'string', 'max:20'];
        } else {
            $rules['id_pegawai'] = ['required', 'string', 'max:20', 'unique:pegawai,id_pegawai'];
            $rules['email']      = ['nullable', 'email', 'max:255', 'unique:pegawai,email'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nis.unique'          => 'NIS sudah digunakan.',
            'id_pegawai.unique'   => 'ID Pegawai sudah digunakan.',
            'email.unique'        => 'Email sudah digunakan.',
        ];
    }
}
