<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'          => ['sometimes', 'required', 'in:menunggu,aktif,menunggu_kembali,terlambat,dikembalikan,ditolak'],
            'batas_waktu'     => ['nullable', 'date'],
            'tanggal_kembali' => ['nullable', 'date'],
            'durasi_hari'     => ['nullable', 'integer', 'min:1', 'max:30'],
            'id_pegawai'      => ['nullable', 'string', 'exists:pegawai,id_pegawai'],
            'nis'             => ['sometimes', 'required', 'string', 'exists:anggota,nis'],
            'isbn'            => ['sometimes', 'required', 'string', 'exists:buku,isbn'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'         => 'Status tidak valid. Pilihan: menunggu, aktif, menunggu_kembali, terlambat, dikembalikan, ditolak.',
            'nis.exists'        => 'Anggota tidak ditemukan.',
            'isbn.exists'       => 'Buku tidak ditemukan.',
            'id_pegawai.exists' => 'Pegawai tidak ditemukan.',
        ];
    }
}
