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
            'id_pegawai' => 'PGW-001',
            'nama'       => 'Admin',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('admin123'),
        ]);

        $this->token = $pegawai->createToken('test-token')->plainTextToken;
    }

    public function test_can_create_and_fetch_anggota(): void
    {
        $payload = [
            'nis'          => '14156',
            'nama_lengkap' => 'Gazhy Arkana',
            'kelas'        => 'XII RPL 1',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/anggota', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nis'          => '14156',
                    'nama_lengkap' => 'Gazhy Arkana',
                    'kelas'        => 'XII RPL 1',
                    'inisial'      => 'GA',
                ],
            ]);

        $this->assertDatabaseHas('anggota', [
            'nis' => '14156',
        ]);
    }

    public function test_can_update_fcm_token(): void
    {
        Anggota::create([
            'nis'          => '14156',
            'nama_lengkap' => 'Gazhy Arkana',
            'kelas'        => 'XII RPL 1',
            'password'     => Hash::make('password'),
        ]);

        $response = $this->putJson('/api/anggota/14156/fcm-token', [
            'fcm_token' => 'sample-fcm-token-12345',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'FCM token berhasil diperbarui.']);

        $this->assertDatabaseHas('anggota', [
            'nis'       => '14156',
            'fcm_token' => 'sample-fcm-token-12345',
        ]);
    }
}
