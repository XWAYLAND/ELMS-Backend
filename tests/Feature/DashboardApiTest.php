<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_dashboard_stats(): void
    {
        $jenis = Jenis::create(['id_jenis' => 'JNS-005', 'nama_jenis' => 'Fiksi']);

        Buku::create([
            'isbn'        => '978-001',
            'judul'       => 'Laskar Pelangi',
            'bahasa'      => 'Indonesia',
            'tersedia'    => true,
            'id_jenis'    => 'JNS-005',
            'penulis'     => 'Andrea Hirata',
            'penerbit'    => 'Gramedia',
        ]);

        Anggota::create([
            'nis'          => '14156',
            'nama_lengkap' => 'Gazhy Arkana',
            'kelas'        => 'XII RPL 1',
            'password'     => Hash::make('password'),
        ]);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'summary' => [
                        'total_buku'          => 1,
                        'total_buku_tersedia' => 1,
                        'total_anggota'       => 1,
                    ],
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'summary' => [
                        'total_buku',
                        'total_buku_tersedia',
                        'total_buku_dipinjam',
                        'total_anggota',
                        'total_peminjaman_aktif',
                        'total_peminjaman_menunggu',
                        'total_peminjaman_terlambat',
                    ],
                    'kategori',
                    'buku_terbaru',
                ],
            ]);
    }
}
