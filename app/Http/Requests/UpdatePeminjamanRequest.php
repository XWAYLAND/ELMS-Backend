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
            'waktu_pinjam'  => ['sometimes', 'required', 'date'],
            'batas_kembali' => ['sometimes', 'required', 'date'],
            'status'        => ['sometimes', 'required', 'in:pending,dipinjam,dikembalikan,terlambat'],
            'nis'           => ['sometimes', 'required', 'string', 'exists:anggota,nis'],
            'isbn'          => ['sometimes', 'required', 'string', 'exists:buku,isbn'],
            'id_pegawai'    => ['sometimes', 'required', 'string', 'exists:pegawai,id_pegawai'],
        ];
    }

    public function messages(): array
    {
        return [
            'batas_kembali.date' => 'Batas kembali harus berupa tanggal yang valid.',
            'status.in'          => 'Status tidak valid. Pilihan: pending, dipinjam, dikembalikan, terlambat.',
            'nis.exists'         => 'Anggota tidak ditemukan.',
            'isbn.exists'        => 'Buku tidak ditemukan.',
            'id_pegawai.exists'  => 'Pegawai tidak ditemukan.',
        ];
    }
}
