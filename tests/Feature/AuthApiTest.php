<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Pegawai;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Pegawai::create([
            'id_pegawai' => 'PGW-001',
            'nama'       => 'Admin Perpustakaan',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('admin123'),
        ]);

        Anggota::create([
            'nis'          => '14156',
            'nama_lengkap' => 'Gazhy Arkana',
            'kelas'        => 'XII RPL 1',
            'password'     => Hash::make('password'),
        ]);
    }

    public function test_petugas_can_login_with_id_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'identifier' => 'PGW-001',
            'password'   => 'admin123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login petugas berhasil.',
                'data'    => [
                    'role' => 'petugas',
                    'user' => [
                        'id_pegawai' => 'PGW-001',
                        'nama'       => 'Admin Perpustakaan',
                    ],
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_siswa_can_login_with_nis_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'identifier' => '14156',
            'password'   => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login siswa berhasil.',
                'data'    => [
                    'role' => 'siswa',
                    'user' => [
                        'nis'          => '14156',
                        'nama_lengkap' => 'Gazhy Arkana',
                    ],
                ],
            ]);
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'identifier' => '14156',
            'password'   => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['identifier']);
    }

    public function test_authenticated_siswa_can_get_profile(): void
    {
        $siswa = Anggota::first();
        $token = $siswa->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'role' => 'siswa',
                    'nis'  => '14156',
                ],
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $siswa = Anggota::first();
        $token = $siswa->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Logout berhasil.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
