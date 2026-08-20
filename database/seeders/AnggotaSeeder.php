<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nis' => '2024001', 'nama_lengkap' => 'Ahmad Fauzi',     'kelas' => 'X-A'],
            ['nis' => '2024002', 'nama_lengkap' => 'Siti Nurhaliza',  'kelas' => 'X-A'],
            ['nis' => '2024003', 'nama_lengkap' => 'Budi Prasetyo',   'kelas' => 'XI-B'],
            ['nis' => '2024004', 'nama_lengkap' => 'Dewi Rahayu',     'kelas' => 'XI-B'],
            ['nis' => '2024005', 'nama_lengkap' => 'Eko Wahyudi',     'kelas' => 'XII-C'],
        ];

        foreach ($data as $item) {
            Anggota::firstOrCreate(['nis' => $item['nis']], $item);
        }
    }
}
