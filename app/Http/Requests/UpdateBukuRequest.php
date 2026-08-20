<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'          => ['sometimes', 'required', 'string', 'max:255'],
            'edisi'          => ['nullable', 'string', 'max:50'],
            'deskripsi_fisik'=> ['nullable', 'string'],
            'bahasa'         => ['sometimes', 'required', 'string', 'max:50'],
            'cover'          => ['nullable', 'url', 'max:500'],
            'id_jenis'       => ['sometimes', 'required', 'string', 'exists:jenis,id_jenis'],
            'id_penulis'     => ['sometimes', 'required', 'string', 'exists:penulis,id_penulis'],
            'id_penerbit'    => ['sometimes', 'required', 'string', 'exists:penerbit,id_penerbit'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'       => 'Judul buku wajib diisi.',
            'bahasa.required'      => 'Bahasa wajib diisi.',
            'cover.url'            => 'Cover harus berupa URL yang valid.',
            'id_jenis.exists'      => 'Jenis buku tidak ditemukan.',
            'id_penulis.exists'    => 'Penulis tidak ditemukan.',
            'id_penerbit.exists'   => 'Penerbit tidak ditemukan.',
        ];
    }
}
