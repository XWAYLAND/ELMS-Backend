<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        Pegawai::firstOrCreate(
            ['id_pegawai' => 'PGW001'],
            [
                'id_pegawai' => 'PGW001',
                'nama'       => 'Admin Perpustakaan',
                'email'      => 'admin@elibrary.com',
                'password'   => Hash::make('password123'),
            ]
        );

        Pegawai::firstOrCreate(
            ['id_pegawai' => 'PGW002'],
            [
                'id_pegawai' => 'PGW002',
                'nama'       => 'Budi Santoso',
                'email'      => 'budi@elibrary.com',
                'password'   => Hash::make('password123'),
            ]
        );
    }
}
