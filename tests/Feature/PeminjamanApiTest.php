<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeminjamanApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        Jenis::create(['id_jenis' => 'JNS001', 'nama_jenis' => 'Fiksi']);
        Penulis::create(['id_penulis' => 'PNL001', 'nama_penulis' => 'Andrea Hirata']);
        Penerbit::create(['id_penerbit' => 'PNR001', 'nama_penerbit' => 'Bentang Pustaka']);

        Buku::create([
            'isbn'        => '978-001',
            'judul'       => 'Laskar Pelangi',
            'bahasa'      => 'Indonesia',
            'id_jenis'    => 'JNS001',
            'id_penulis'  => 'PNL001',
            'id_penerbit' => 'PNR001',
        ]);

        Anggota::create([
            'nis'          => '2024001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'kelas'        => 'X-A',
        ]);

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PGW001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('password123'),
        ]);

        $this->token = $pegawai->createToken('test-token')->plainTextToken;
    }

    public function test_can_create_borrowing_transaction(): void
    {
        $payload = [
            'id_transaksi'  => 'TRX001',
            'waktu_pinjam'  => now()->toDateString(),
            'batas_kembali' => now()->addDays(7)->toDateString(),
            'status'        => 'dipinjam',
            'nis'           => '2024001',
            'isbn'          => '978-001',
            'id_pegawai'    => 'PGW001',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/peminjaman', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Peminjaman berhasil dibuat.',
            ]);

        $this->assertDatabaseHas('peminjaman', ['id_transaksi' => 'TRX001']);
    }

    public function test_can_filter_loans_due_soon(): void
    {
        // Transaction due tomorrow
        Peminjaman::create([
            'id_transaksi'  => 'TRX-DUE',
            'waktu_pinjam'  => now()->subDays(6)->toDateString(),
            'batas_kembali' => now()->addDay()->toDateString(),
            'status'        => 'dipinjam',
            'nis'           => '2024001',
            'isbn'          => '978-001',
            'id_pegawai'    => 'PGW001',
        ]);

        // Transaction returned (should not show up in notifications)
        Peminjaman::create([
            'id_transaksi'  => 'TRX-RETURNED',
            'waktu_pinjam'  => now()->subDays(10)->toDateString(),
            'batas_kembali' => now()->subDays(3)->toDateString(),
            'status'        => 'dikembalikan',
            'nis'           => '2024001',
            'isbn'          => '978-001',
            'id_pegawai'    => 'PGW001',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/peminjaman/jatuh-tempo?hari=3');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id_transaksi', 'TRX-DUE');
    }
}
