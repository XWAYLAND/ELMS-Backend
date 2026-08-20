<?php

namespace Tests\Feature;

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
            'id_pegawai' => 'PGW001',
            'nama'       => 'Admin Perpustakaan',
            'email'      => 'admin@elibrary.com',
            'password'   => Hash::make('password123'),
        ]);
    }

    public function test_pegawai_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@elibrary.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'pegawai' => ['id_pegawai', 'nama', 'email'],
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_pegawai_cannot_login_with_invalid_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email'    => 'admin@elibrary.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_pegawai_can_get_profile(): void
    {
        $pegawai = Pegawai::first();
        $token = $pegawai->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id_pegawai' => 'PGW001',
                    'email'      => 'admin@elibrary.com',
                ],
            ]);
    }

    public function test_authenticated_pegawai_can_logout(): void
    {
        $pegawai = Pegawai::first();
        $token = $pegawai->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Logout berhasil.']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
