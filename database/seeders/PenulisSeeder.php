<?php

namespace Database\Seeders;

use App\Models\Penulis;
use Illuminate\Database\Seeder;

class PenulisSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_penulis' => 'PNS-001', 'nama_penulis' => 'Nawal el-Saadawi'],
            ['id_penulis' => 'PNS-002', 'nama_penulis' => 'Alexacakes'],
            ['id_penulis' => 'PNS-003', 'nama_penulis' => 'Tere Liye'],
            ['id_penulis' => 'PNS-004', 'nama_penulis' => 'Ireen Chau'],
            ['id_penulis' => 'PNS-005', 'nama_penulis' => 'Paulo Coelho'],
            ['id_penulis' => 'PNS-006', 'nama_penulis' => 'Sally Rooney'],
            ['id_penulis' => 'PNS-007', 'nama_penulis' => 'Anne Frank'],
            ['id_penulis' => 'PNS-008', 'nama_penulis' => 'Mitch Albom'],
        ];

        foreach ($data as $item) {
            Penulis::firstOrCreate(['id_penulis' => $item['id_penulis']], $item);
        }
    }
}
