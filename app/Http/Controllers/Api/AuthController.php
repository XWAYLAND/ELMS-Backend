<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login pegawai dan mendapatkan API token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $pegawai = Pegawai::where('email', $request->email)->first();

        if (! $pegawai || ! Hash::check($request->password, $pegawai->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $pegawai->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'pegawai' => [
                    'id_pegawai' => $pegawai->id_pegawai,
                    'nama'       => $pegawai->nama,
                    'email'      => $pegawai->email,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout: hapus semua token aktif pegawai.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Mendapatkan informasi pegawai yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil.',
            'data'    => [
                'id_pegawai' => $request->user()->id_pegawai,
                'nama'       => $request->user()->nama,
                'email'      => $request->user()->email,
            ],
        ]);
    }
}
