<?php

namespace Database\Seeders;

use App\Models\Jenis;
use Illuminate\Database\Seeder;

class JenisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_jenis' => 'JNS-001', 'nama_jenis' => 'Administrasi'],
            ['id_jenis' => 'JNS-002', 'nama_jenis' => 'Agama'],
            ['id_jenis' => 'JNS-003', 'nama_jenis' => 'Ekonomi'],
            ['id_jenis' => 'JNS-004', 'nama_jenis' => 'Ensiklopedia'],
            ['id_jenis' => 'JNS-005', 'nama_jenis' => 'Fiksi'],
            ['id_jenis' => 'JNS-006', 'nama_jenis' => 'Humor'],
            ['id_jenis' => 'JNS-007', 'nama_jenis' => 'Inspirasi'],
            ['id_jenis' => 'JNS-008', 'nama_jenis' => 'Sejarah'],
        ];

        foreach ($data as $item) {
            Jenis::firstOrCreate(['id_jenis' => $item['id_jenis']], $item);
        }
    }
}
