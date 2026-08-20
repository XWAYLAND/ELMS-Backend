<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Penerbit;
use App\Models\Penulis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BukuApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed master data
        Jenis::create(['id_jenis' => 'JNS001', 'nama_jenis' => 'Fiksi']);
        Penulis::create(['id_penulis' => 'PNL001', 'nama_penulis' => 'Andrea Hirata']);
        Penerbit::create(['id_penerbit' => 'PNR001', 'nama_penerbit' => 'Bentang Pustaka']);

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PGW001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('password123'),
        ]);

        $this->token = $pegawai->createToken('test-token')->plainTextToken;
    }

    public function test_can_list_books_publicly(): void
    {
        Buku::create([
            'isbn'            => '978-979-22-9224-7',
            'judul'           => 'Laskar Pelangi',
            'edisi'           => '1',
            'deskripsi_fisik' => '529 halaman',
            'bahasa'          => 'Indonesia',
            'id_jenis'        => 'JNS001',
            'id_penulis'      => 'PNL001',
            'id_penerbit'     => 'PNR001',
        ]);

        $response = $this->getJson('/api/buku');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.0.isbn', '978-979-22-9224-7')
            ->assertJsonPath('data.0.judul', 'Laskar Pelangi');
    }

    public function test_can_search_books_by_title_or_author(): void
    {
        Buku::create([
            'isbn'        => '978-001',
            'judul'       => 'Laskar Pelangi',
            'bahasa'      => 'Indonesia',
            'id_jenis'    => 'JNS001',
            'id_penulis'  => 'PNL001',
            'id_penerbit' => 'PNR001',
        ]);

        // Search by title
        $response = $this->getJson('/api/buku?search=laskar');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        // Search by non-existent term
        $response = $this->getJson('/api/buku?search=nonexistent');
        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    public function test_authenticated_pegawai_can_create_book(): void
    {
        $payload = [
            'isbn'            => '978-602-03-1157-8',
            'judul'           => 'Supernova',
            'edisi'           => '1',
            'deskripsi_fisik' => '344 halaman',
            'bahasa'          => 'Indonesia',
            'cover'           => 'https://example.com/cover.jpg',
            'id_jenis'        => 'JNS001',
            'id_penulis'      => 'PNL001',
            'id_penerbit'     => 'PNR001',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/buku', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Buku berhasil ditambahkan.']);

        $this->assertDatabaseHas('buku', ['isbn' => '978-602-03-1157-8']);
    }

    public function test_unauthenticated_user_cannot_create_book(): void
    {
        $response = $this->postJson('/api/buku', [
            'isbn'  => '978-000',
            'judul' => 'Unauthorized Book',
        ]);

        $response->assertStatus(401);
    }
}
