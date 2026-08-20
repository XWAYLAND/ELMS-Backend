<?php

namespace Database\Seeders;

use App\Models\Buku;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'isbn'            => '978-979-22-9224-7',
                'judul'           => 'Laskar Pelangi',
                'edisi'           => '1',
                'deskripsi_fisik' => '529 halaman; 21 cm',
                'bahasa'          => 'Indonesia',
                'cover'           => null,
                'id_jenis'        => 'JNS001',
                'id_penulis'      => 'PNL001',
                'id_penerbit'     => 'PNR004',
            ],
            [
                'isbn'            => '978-979-91-0224-8',
                'judul'           => 'Bumi Manusia',
                'edisi'           => '1',
                'deskripsi_fisik' => '535 halaman; 21 cm',
                'bahasa'          => 'Indonesia',
                'cover'           => null,
                'id_jenis'        => 'JNS001',
                'id_penulis'      => 'PNL002',
                'id_penerbit'     => 'PNR001',
            ],
            [
                'isbn'            => '978-979-433-397-2',
                'judul'           => 'Ayat-Ayat Cinta',
                'edisi'           => '33',
                'deskripsi_fisik' => '419 halaman; 20.5 cm',
                'bahasa'          => 'Indonesia',
                'cover'           => null,
                'id_jenis'        => 'JNS007',
                'id_penulis'      => 'PNL003',
                'id_penerbit'     => 'PNR005',
            ],
            [
                'isbn'            => '978-602-03-1157-8',
                'judul'           => 'Supernova: Ksatria, Puteri, dan Bintang Jatuh',
                'edisi'           => '1',
                'deskripsi_fisik' => '344 halaman; 20 cm',
                'bahasa'          => 'Indonesia',
                'cover'           => null,
                'id_jenis'        => 'JNS001',
                'id_penulis'      => 'PNL004',
                'id_penerbit'     => 'PNR001',
            ],
            [
                'isbn'            => '978-602-8811-55-2',
                'judul'           => 'Bumi',
                'edisi'           => '1',
                'deskripsi_fisik' => '440 halaman; 20 cm',
                'bahasa'          => 'Indonesia',
                'cover'           => null,
                'id_jenis'        => 'JNS001',
                'id_penulis'      => 'PNL005',
                'id_penerbit'     => 'PNR001',
            ],
        ];

        foreach ($data as $item) {
            Buku::firstOrCreate(['isbn' => $item['isbn']], $item);
        }
    }
}
