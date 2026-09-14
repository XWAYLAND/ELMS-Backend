<?php

namespace Database\Seeders;

use App\Models\Penerbit;
use Illuminate\Database\Seeder;

class PenerbitSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_penerbit' => 'PNB-001', 'nama_penerbit' => 'Yayasan Pustaka Obor'],
            ['id_penerbit' => 'PNB-002', 'nama_penerbit' => 'Self Published'],
            ['id_penerbit' => 'PNB-003', 'nama_penerbit' => 'Gramedia'],
            ['id_penerbit' => 'PNB-004', 'nama_penerbit' => 'HarperOne'],
            ['id_penerbit' => 'PNB-005', 'nama_penerbit' => 'Faber & Faber'],
        ];

        foreach ($data as $item) {
            Penerbit::firstOrCreate(['id_penerbit' => $item['id_penerbit']], $item);
        }
    }
}
