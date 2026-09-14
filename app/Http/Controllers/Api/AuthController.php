<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login untuk Siswa (Anggota) atau Petugas (Pegawai).
     *
     * Body parameter:
     * - identifier : NIS (siswa), ID Pegawai (petugas), atau Email (petugas)
     * - password   : Password pengguna
     * - role       : (opsional) 'siswa' atau 'petugas'
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'identifier' => ['required', 'string'],
            'password'   => ['required', 'string'],
            'role'       => ['nullable', 'in:siswa,petugas'],
        ]);

        $identifier = trim($request->input('identifier'));
        $password   = $request->input('password');
        $role       = $request->input('role');

        // Jika role spesifik siswa atau tidak dispesifikasikan: coba cari di Anggota
        if ($role === 'siswa' || ! $role) {
            $anggota = Anggota::where('nis', $identifier)->first();
            if ($anggota && Hash::check($password, $anggota->password)) {
                $token = $anggota->createToken('flutter-student-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login siswa berhasil.',
                    'data'    => [
                        'role'  => 'siswa',
                        'user'  => [
                            'nis'          => $anggota->nis,
                            'nama_lengkap' => $anggota->nama_lengkap,
                            'kelas'        => $anggota->kelas,
                            'inisial'      => $anggota->inisial,
                        ],
                        'token'      => $token,
                        'token_type' => 'Bearer',
                    ],
                ]);
            }

            if ($role === 'siswa') {
                throw ValidationException::withMessages([
                    'identifier' => ['NIS atau password siswa salah.'],
                ]);
            }
        }

        // Jika role spesifik petugas atau fallback: coba cari di Pegawai
        if ($role === 'petugas' || ! $role) {
            $pegawai = Pegawai::where('id_pegawai', $identifier)
                ->orWhere('email', $identifier)
                ->first();

            if ($pegawai && Hash::check($password, $pegawai->password)) {
                $token = $pegawai->createToken('flutter-staff-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login petugas berhasil.',
                    'data'    => [
                        'role'  => 'petugas',
                        'user'  => [
                            'id_pegawai' => $pegawai->id_pegawai,
                            'nama'       => $pegawai->nama,
                            'email'      => $pegawai->email,
                            'inisial'    => $pegawai->inisial,
                        ],
                        'token'      => $token,
                        'token_type' => 'Bearer',
                    ],
                ]);
            }

            if ($role === 'petugas') {
                throw ValidationException::withMessages([
                    'identifier' => ['ID Pegawai / Email atau password petugas salah.'],
                ]);
            }
        }

        throw ValidationException::withMessages([
            'identifier' => ['Kredensial login tidak valid.'],
        ]);
    }

    /**
     * Logout pengguna yang sedang login.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Mengambil info profil pengguna yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($user instanceof Anggota) {
            return response()->json([
                'success' => true,
                'message' => 'Data profil siswa berhasil diambil.',
                'data'    => [
                    'role'                 => 'siswa',
                    'nis'                  => (string) $user->nis,
                    'nama_lengkap'         => (string) $user->nama_lengkap,
                    'kelas'                => (string) $user->kelas,
                    'inisial'              => (string) $user->inisial,
                    'total_pinjaman_aktif' => (int) $user->peminjamanAktif()->count(),
                ],
            ]);
        }

        if ($user instanceof Pegawai) {
            return response()->json([
                'success' => true,
                'message' => 'Data profil petugas berhasil diambil.',
                'data'    => [
                    'role'       => 'petugas',
                    'id_pegawai' => (string) $user->id_pegawai,
                    'nama'       => (string) $user->nama,
                    'email'      => (string) $user->email,
                    'inisial'    => (string) $user->inisial,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data profil pengguna berhasil diambil.',
            'data'    => [
                'id'   => $user->getAuthIdentifier(),
                'name' => $user->name ?? null,
            ],
        ]);
    }
}
