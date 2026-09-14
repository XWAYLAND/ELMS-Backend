<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn'            => ['required', 'string', 'max:30', 'unique:buku,isbn'],
            'judul'           => ['required', 'string', 'max:255'],
            'cover'           => ['nullable', 'string', 'max:500'],
            'cover_file'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'edisi'           => ['nullable', 'string', 'max:100'],
            'deskripsi_fisik' => ['nullable', 'string', 'max:255'],
            'bahasa'          => ['nullable', 'string', 'max:50'],
            'id_jenis'        => ['required', 'string', 'exists:jenis,id_jenis'],
            'penulis'         => ['required', 'string', 'max:255'],
            'penerbit'        => ['required', 'string', 'max:255'],
            'tersedia'        => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required'     => 'ISBN wajib diisi.',
            'isbn.unique'       => 'ISBN sudah terdaftar.',
            'judul.required'    => 'Judul buku wajib diisi.',
            'id_jenis.required' => 'Jenis buku wajib dipilih.',
            'id_jenis.exists'   => 'Jenis buku tidak ditemukan.',
            'penulis.required'  => 'Penulis wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
        ];
    }
}
