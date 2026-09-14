<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id_transaksi'         => 'TRX-SAMPLE01',
                'nis'                  => '14156',
                'isbn'                 => '978-979-461-487-1',
                'id_pegawai'           => null,
                'kode_unik'            => 'KODE0001',
                'kode_unik_expires_at' => now()->addHours(20),
                'waktu_pengajuan'      => now()->subHours(4),
                'batas_waktu'          => null,
                'durasi_hari'          => 7,
                'tanggal_kembali'      => null,
                'status'               => 'menunggu',
            ],
            [
                'id_transaksi'         => 'TRX-SAMPLE02',
                'nis'                  => '14146',
                'isbn'                 => '978-602-03-2478-3',
                'id_pegawai'           => 'PGW-001',
                'kode_unik'            => 'KODE0002',
                'kode_unik_expires_at' => now()->addHours(12),
                'waktu_pengajuan'      => now()->subDays(2),
                'batas_waktu'          => now()->addDays(5),
                'durasi_hari'          => 7,
                'tanggal_kembali'      => null,
                'status'               => 'aktif',
            ],
            [
                'id_transaksi'         => 'TRX-SAMPLE03',
                'nis'                  => '14144',
                'isbn'                 => '978-602-06-3317-6',
                'id_pegawai'           => 'PGW-001',
                'kode_unik'            => 'KODE0003',
                'kode_unik_expires_at' => now()->subDays(10),
                'waktu_pengajuan'      => now()->subDays(12),
                'batas_waktu'          => now()->subDays(3),
                'durasi_hari'          => 7,
                'tanggal_kembali'      => null,
                'status'               => 'terlambat',
            ],
        ];

        foreach ($data as $item) {
            Peminjaman::firstOrCreate(['id_transaksi' => $item['id_transaksi']], $item);
        }
    }
}
