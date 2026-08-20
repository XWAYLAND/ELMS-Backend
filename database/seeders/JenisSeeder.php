<?php

namespace Database\Seeders;

use App\Models\Jenis;
use Illuminate\Database\Seeder;

class JenisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_jenis' => 'JNS001', 'nama_jenis' => 'Fiksi'],
            ['id_jenis' => 'JNS002', 'nama_jenis' => 'Non-Fiksi'],
            ['id_jenis' => 'JNS003', 'nama_jenis' => 'Sains & Teknologi'],
            ['id_jenis' => 'JNS004', 'nama_jenis' => 'Sejarah'],
            ['id_jenis' => 'JNS005', 'nama_jenis' => 'Biografi'],
            ['id_jenis' => 'JNS006', 'nama_jenis' => 'Pendidikan'],
            ['id_jenis' => 'JNS007', 'nama_jenis' => 'Agama & Spiritualitas'],
            ['id_jenis' => 'JNS008', 'nama_jenis' => 'Komik & Manga'],
        ];

        foreach ($data as $item) {
            Jenis::firstOrCreate(['id_jenis' => $item['id_jenis']], $item);
        }
    }
}
