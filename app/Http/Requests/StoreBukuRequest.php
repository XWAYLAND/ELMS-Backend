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
            'isbn'           => ['required', 'string', 'max:20', 'unique:buku,isbn'],
            'judul'          => ['required', 'string', 'max:255'],
            'edisi'          => ['nullable', 'string', 'max:50'],
            'deskripsi_fisik'=> ['nullable', 'string'],
            'bahasa'         => ['required', 'string', 'max:50'],
            'cover'          => ['nullable', 'url', 'max:500'],
            'id_jenis'       => ['required', 'string', 'exists:jenis,id_jenis'],
            'id_penulis'     => ['required', 'string', 'exists:penulis,id_penulis'],
            'id_penerbit'    => ['required', 'string', 'exists:penerbit,id_penerbit'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required'        => 'ISBN wajib diisi.',
            'isbn.unique'          => 'ISBN sudah terdaftar.',
            'judul.required'       => 'Judul buku wajib diisi.',
            'bahasa.required'      => 'Bahasa wajib diisi.',
            'cover.url'            => 'Cover harus berupa URL yang valid.',
            'id_jenis.required'    => 'Jenis buku wajib dipilih.',
            'id_jenis.exists'      => 'Jenis buku tidak ditemukan.',
            'id_penulis.required'  => 'Penulis wajib dipilih.',
            'id_penulis.exists'    => 'Penulis tidak ditemukan.',
            'id_penerbit.required' => 'Penerbit wajib dipilih.',
            'id_penerbit.exists'   => 'Penerbit tidak ditemukan.',
        ];
    }
}
