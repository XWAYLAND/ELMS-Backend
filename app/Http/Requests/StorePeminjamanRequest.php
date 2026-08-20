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
            'id_transaksi'  => ['required', 'string', 'max:50', 'unique:peminjaman,id_transaksi'],
            'waktu_pinjam'  => ['required', 'date'],
            'batas_kembali' => ['required', 'date', 'after:waktu_pinjam'],
            'status'        => ['required', 'in:pending,dipinjam,dikembalikan,terlambat'],
            'nis'           => ['required', 'string', 'exists:anggota,nis'],
            'isbn'          => ['required', 'string', 'exists:buku,isbn'],
            'id_pegawai'    => ['required', 'string', 'exists:pegawai,id_pegawai'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_transaksi.required'  => 'ID Transaksi wajib diisi.',
            'id_transaksi.unique'    => 'ID Transaksi sudah digunakan.',
            'waktu_pinjam.required'  => 'Waktu pinjam wajib diisi.',
            'waktu_pinjam.date'      => 'Waktu pinjam harus berupa tanggal yang valid.',
            'batas_kembali.required' => 'Batas kembali wajib diisi.',
            'batas_kembali.after'    => 'Batas kembali harus setelah waktu pinjam.',
            'status.in'              => 'Status tidak valid. Pilihan: pending, dipinjam, dikembalikan, terlambat.',
            'nis.required'           => 'NIS anggota wajib diisi.',
            'nis.exists'             => 'Anggota tidak ditemukan.',
            'isbn.required'          => 'ISBN buku wajib diisi.',
            'isbn.exists'            => 'Buku tidak ditemukan.',
            'id_pegawai.required'    => 'ID Pegawai wajib diisi.',
            'id_pegawai.exists'      => 'Pegawai tidak ditemukan.',
        ];
    }
}
