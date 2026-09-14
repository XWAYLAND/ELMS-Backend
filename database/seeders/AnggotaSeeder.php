<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nis'          => '14146',
                'nama_lengkap' => 'Altaf Azka Aviantara',
                'kelas'        => 'XII RPL 1',
                'password'     => Hash::make('password'),
            ],
            [
                'nis'          => '14156',
                'nama_lengkap' => 'Gazhy Arkana Pramudito',
                'kelas'        => 'XII RPL 1',
                'password'     => Hash::make('password'),
            ],
            [
                'nis'          => '14144',
                'nama_lengkap' => 'Ahmad Aliffansyah',
                'kelas'        => 'XII RPL 1',
                'password'     => Hash::make('password'),
            ],
            [
                'nis'          => '14145',
                'nama_lengkap' => 'Aidhil Fahim Mubarraq',
                'kelas'        => 'XII RPL 1',
                'password'     => Hash::make('password'),
            ],
            [
                'nis'          => '14169',
                'nama_lengkap' => 'Neina Khairani',
                'kelas'        => 'XII RPL 1',
                'password'     => Hash::make('password'),
            ],
        ];

        foreach ($data as $item) {
            Anggota::firstOrCreate(['nis' => $item['nis']], $item);
        }
    }
}
