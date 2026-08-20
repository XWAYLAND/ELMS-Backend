<?php

namespace Database\Seeders;

use App\Models\Penerbit;
use Illuminate\Database\Seeder;

class PenerbitSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['id_penerbit' => 'PNR001', 'nama_penerbit' => 'Gramedia Pustaka Utama'],
            ['id_penerbit' => 'PNR002', 'nama_penerbit' => 'Mizan'],
            ['id_penerbit' => 'PNR003', 'nama_penerbit' => 'Erlangga'],
            ['id_penerbit' => 'PNR004', 'nama_penerbit' => 'Bentang Pustaka'],
            ['id_penerbit' => 'PNR005', 'nama_penerbit' => 'Republika Penerbit'],
        ];

        foreach ($data as $item) {
            Penerbit::firstOrCreate(['id_penerbit' => $item['id_penerbit']], $item);
        }
    }
}
