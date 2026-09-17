<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->input('role');
        $id   = $this->route('id');

        $rules = [
            'role'     => ['required', 'in:student,staff'],
            'name'     => ['required', 'string', 'max:150'],
            'password' => ['nullable', 'string', 'min:6'],
        ];

        if ($role === 'student') {
            $rules['kelas'] = ['required', 'string', 'max:20'];
        } else {
            $rules['email'] = ['nullable', 'email', 'max:255', "unique:pegawai,email,{$id},id_pegawai"];
        }

        return $rules;
    }
}
