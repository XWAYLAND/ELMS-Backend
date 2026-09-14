<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn'        => ['required', 'string', 'exists:buku,isbn'],
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'nis'         => ['nullable', 'string', 'exists:anggota,nis'],
            'id_pegawai'  => ['nullable', 'string', 'exists:pegawai,id_pegawai'],
            'status'      => ['nullable', 'in:menunggu,aktif,menunggu_kembali,terlambat,dikembalikan,ditolak'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required'        => 'ISBN buku wajib diisi.',
            'isbn.exists'          => 'Buku tidak ditemukan.',
            'durasi_hari.required' => 'Durasi peminjaman wajib diisi.',
            'durasi_hari.integer'  => 'Durasi hari harus berupa angka.',
            'durasi_hari.min'      => 'Durasi peminjaman minimal 1 hari.',
            'durasi_hari.max'      => 'Durasi peminjaman maksimal 30 hari.',
            'nis.exists'           => 'Anggota tidak ditemukan.',
        ];
    }
}
