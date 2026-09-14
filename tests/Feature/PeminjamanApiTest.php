<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PeminjamanApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $staffToken;
    protected string $studentToken;
    protected Anggota $siswa;
    protected Pegawai $pegawai;
    protected Buku $buku;

    protected function setUp(): void
    {
        parent::setUp();

        Jenis::create(['id_jenis' => 'JNS-005', 'nama_jenis' => 'Fiksi']);

        $this->buku = Buku::create([
            'isbn'        => '978-001',
            'judul'       => 'Laskar Pelangi',
            'bahasa'      => 'Indonesia',
            'tersedia'    => true,
            'id_jenis'    => 'JNS-005',
            'penulis'     => 'Andrea Hirata',
            'penerbit'    => 'Gramedia',
        ]);

        $this->siswa = Anggota::create([
            'nis'          => '14156',
            'nama_lengkap' => 'Gazhy Arkana',
            'kelas'        => 'XII RPL 1',
            'password'     => Hash::make('password'),
        ]);

        $this->pegawai = Pegawai::create([
            'id_pegawai' => 'PGW-001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('admin123'),
        ]);

        $this->studentToken = $this->siswa->createToken('student-token')->plainTextToken;
        $this->staffToken   = $this->pegawai->createToken('staff-token')->plainTextToken;
    }

    public function test_siswa_can_request_borrowing(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->postJson('/api/peminjaman/pinjam', [
                'isbn'        => '978-001',
                'durasi_hari' => 7,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'nis'    => '14156',
                    'isbn'   => '978-001',
                    'status' => 'menunggu',
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'id_transaksi',
                    'kode_unik',
                    'kode_unik_expires_at',
                ],
            ]);

        // Buku harus terkunci (tidak tersedia)
        $this->assertDatabaseHas('buku', [
            'isbn'     => '978-001',
            'tersedia' => false,
        ]);
    }

    public function test_petugas_can_verify_and_approve_loan(): void
    {
        $peminjaman = Peminjaman::create([
            'nis'         => '14156',
            'isbn'        => '978-001',
            'durasi_hari' => 7,
            'status'      => 'menunggu',
        ]);

        // Verifikasi kode unik
        $verifyResponse = $this->withHeader('Authorization', 'Bearer ' . $this->staffToken)
            ->postJson('/api/peminjaman/verifikasi-kode', [
                'kode_unik' => $peminjaman->kode_unik,
            ]);

        $verifyResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'id_transaksi'    => $peminjaman->id_transaksi,
                    'can_be_approved' => true,
                ],
            ]);

        // Approve loan
        $approveResponse = $this->withHeader('Authorization', 'Bearer ' . $this->staffToken)
            ->postJson("/api/peminjaman/{$peminjaman->id_transaksi}/approve");

        $approveResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'status'     => 'aktif',
                    'id_pegawai' => 'PGW-001',
                ],
            ]);
    }

    public function test_siswa_can_return_and_petugas_confirm_return(): void
    {
        $peminjaman = Peminjaman::create([
            'nis'         => '14156',
            'isbn'        => '978-001',
            'durasi_hari' => 7,
            'status'      => 'aktif',
            'batas_waktu' => now()->addDays(7),
            'id_pegawai'  => 'PGW-001',
        ]);

        // Siswa ajukan pengembalian
        $returnReqResponse = $this->withHeader('Authorization', 'Bearer ' . $this->studentToken)
            ->patchJson("/api/peminjaman/{$peminjaman->id_transaksi}/ajukan-kembali");

        $returnReqResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'status' => 'menunggu_kembali',
                ],
            ]);

        // Petugas konfirmasi pengembalian
        $confirmResponse = $this->withHeader('Authorization', 'Bearer ' . $this->staffToken)
            ->postJson("/api/peminjaman/{$peminjaman->id_transaksi}/confirm-return");

        $confirmResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'status' => 'dikembalikan',
                ],
            ]);

        // Buku kembali tersedia
        $this->assertDatabaseHas('buku', [
            'isbn'     => '978-001',
            'tersedia' => true,
        ]);
    }
}
