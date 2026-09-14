<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Pegawai;
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
        Jenis::create(['id_jenis' => 'JNS-005', 'nama_jenis' => 'Fiksi']);

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PGW-001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('admin123'),
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
            'tersedia'        => true,
            'id_jenis'        => 'JNS-005',
            'penulis'         => 'Andrea Hirata',
            'penerbit'        => 'Gramedia',
        ]);

        $response = $this->getJson('/api/buku');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.0.isbn', '978-979-22-9224-7')
            ->assertJsonPath('data.0.judul', 'Laskar Pelangi')
            ->assertJsonPath('data.0.penulis', 'Andrea Hirata');
    }

    public function test_can_search_books_by_title_or_author(): void
    {
        Buku::create([
            'isbn'        => '978-001',
            'judul'       => 'Laskar Pelangi',
            'bahasa'      => 'Indonesia',
            'id_jenis'    => 'JNS-005',
            'penulis'     => 'Andrea Hirata',
            'penerbit'    => 'Gramedia',
        ]);

        // Search by title
        $response = $this->getJson('/api/buku?search=laskar');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        // Search by author
        $response = $this->getJson('/api/buku?search=hirata');
        $response->assertStatus(200)->assertJsonCount(1, 'data');

        // Search by non-existent term
        $response = $this->getJson('/api/buku?search=nonexistent');
        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    public function test_can_find_book_by_slug(): void
    {
        $buku = Buku::create([
            'isbn'        => '978-602-03-2478-3',
            'judul'       => 'The Alchemist',
            'bahasa'      => 'Indonesia',
            'id_jenis'    => 'JNS-005',
            'penulis'     => 'Paulo Coelho',
            'penerbit'    => 'Gramedia',
        ]);

        $response = $this->getJson('/api/buku/' . $buku->slug);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'isbn'  => '978-602-03-2478-3',
                    'judul' => 'The Alchemist',
                    'slug'  => $buku->slug,
                ],
            ]);
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
            'id_jenis'        => 'JNS-005',
            'penulis'         => 'Dee Lestari',
            'penerbit'        => 'Gramedia',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/buku', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Buku berhasil ditambahkan.']);

        $this->assertDatabaseHas('buku', [
            'isbn'    => '978-602-03-1157-8',
            'penulis' => 'Dee Lestari',
            'penerbit'=> 'Gramedia',
        ]);
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
