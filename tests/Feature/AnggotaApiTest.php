<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Pegawai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnggotaApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PGW001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('password123'),
        ]);

        $this->token = $pegawai->createToken('test-token')->plainTextToken;
    }

    public function test_can_create_and_fetch_anggota(): void
    {
        $payload = [
            'nis'          => '2024001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'kelas'        => 'X-A',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/anggota', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nis'          => '2024001',
                    'nama_lengkap' => 'Ahmad Fauzi',
                    'kelas'        => 'X-A',
                ],
            ]);
    }

    public function test_can_update_fcm_token(): void
    {
        Anggota::create([
            'nis'          => '2024001',
            'nama_lengkap' => 'Ahmad Fauzi',
            'kelas'        => 'X-A',
        ]);

        $response = $this->putJson('/api/anggota/2024001/fcm-token', [
            'fcm_token' => 'sample-fcm-token-12345',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'FCM token berhasil diperbarui.']);

        $this->assertDatabaseHas('anggota', [
            'nis'       => '2024001',
            'fcm_token' => 'sample-fcm-token-12345',
        ]);
    }
}
