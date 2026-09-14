<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_pegawai' => 'PGW-001',
                'nama'       => 'Admin Perpustakaan',
                'email'      => 'admin@elibrary.com',
                'password'   => Hash::make('admin123'),
            ],
            [
                'id_pegawai' => 'PGW-002',
                'nama'       => 'Petugas 1',
                'email'      => 'petugas1@elibrary.com',
                'password'   => Hash::make('admin123'),
            ],
        ];

        foreach ($data as $item) {
            Pegawai::firstOrCreate(['id_pegawai' => $item['id_pegawai']], $item);
        }
    }
}
