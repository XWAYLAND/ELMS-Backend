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
            'judul'           => ['sometimes', 'required', 'string', 'max:255'],
            'cover'           => ['nullable', 'string', 'max:500'],
            'cover_file'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'edisi'           => ['nullable', 'string', 'max:100'],
            'deskripsi_fisik' => ['nullable', 'string', 'max:255'],
            'bahasa'          => ['nullable', 'string', 'max:50'],
            'id_jenis'        => ['sometimes', 'required', 'string', 'exists:jenis,id_jenis'],
            'penulis'         => ['sometimes', 'required', 'string', 'max:255'],
            'penerbit'        => ['sometimes', 'required', 'string', 'max:255'],
            'tersedia'        => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'    => 'Judul buku wajib diisi.',
            'id_jenis.exists'   => 'Jenis buku tidak ditemukan.',
            'penulis.required'  => 'Penulis wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
        ];
    }
}
