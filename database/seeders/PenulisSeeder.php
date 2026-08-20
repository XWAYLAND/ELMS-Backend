<?php

namespace Database\Seeders;

use App\Models\Penulis;
use Illuminate\Database\Seeder;

class PenulisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_penulis' => 'PNL001', 'nama_penulis' => 'Andrea Hirata'],
            ['id_penulis' => 'PNL002', 'nama_penulis' => 'Pramoedya Ananta Toer'],
            ['id_penulis' => 'PNL003', 'nama_penulis' => 'Habiburrahman El Shirazy'],
            ['id_penulis' => 'PNL004', 'nama_penulis' => 'Dewi Lestari'],
            ['id_penulis' => 'PNL005', 'nama_penulis' => 'Tere Liye'],
        ];

        foreach ($data as $item) {
            Penulis::firstOrCreate(['id_penulis' => $item['id_penulis']], $item);
        }
    }
}
