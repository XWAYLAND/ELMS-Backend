<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnggotaRequest;
use App\Http\Requests\UpdateAnggotaRequest;
use App\Http\Resources\AnggotaResource;
use App\Models\Anggota;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Anggota::query();

        // Filter berdasarkan kelas
        if ($request->has('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        if ($request->filled('search')) {
            $term = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(nama_lengkap) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(nis) LIKE ?', [$term]);
            });
        }

        $anggota = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diambil.',
            'data'    => AnggotaResource::collection($anggota),
            'meta'    => [
                'current_page' => $anggota->currentPage(),
                'last_page'    => $anggota->lastPage(),
                'per_page'     => $anggota->perPage(),
                'total'        => $anggota->total(),
            ],
        ]);
    }

    public function store(StoreAnggotaRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            $data['password'] = 'password';
        }

        $anggota = Anggota::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil ditambahkan.',
            'data'    => new AnggotaResource($anggota),
        ], 201);
    }

    public function show(string $nis): JsonResponse
    {
        $anggota = Anggota::findOrFail($nis);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diambil.',
            'data'    => new AnggotaResource($anggota),
        ]);
    }

    public function update(UpdateAnggotaRequest $request, string $nis): JsonResponse
    {
        $anggota = Anggota::findOrFail($nis);
        $anggota->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil diperbarui.',
            'data'    => new AnggotaResource($anggota),
        ]);
    }

    public function destroy(string $nis): JsonResponse
    {
        $anggota = Anggota::findOrFail($nis);
        $anggota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil dihapus.',
        ]);
    }

    /**
     * Update FCM token anggota untuk notifikasi push Flutter.
     */
    public function updateFcmToken(Request $request, string $nis): JsonResponse
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        $anggota = Anggota::findOrFail($nis);
        $anggota->update(['fcm_token' => $request->fcm_token]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token berhasil diperbarui.',
        ]);
    }
}
