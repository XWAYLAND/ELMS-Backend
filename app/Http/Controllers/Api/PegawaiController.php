<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;

class PegawaiController extends Controller
{
    public function index(): JsonResponse
    {
        $pegawai = Pegawai::all();

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil.',
            'data'    => PegawaiResource::collection($pegawai),
        ]);
    }

    public function store(StorePegawaiRequest $request): JsonResponse
    {
        $pegawai = Pegawai::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil ditambahkan.',
            'data'    => new PegawaiResource($pegawai),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $pegawai = Pegawai::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil.',
            'data'    => new PegawaiResource($pegawai),
        ]);
    }

    public function update(UpdatePegawaiRequest $request, string $id): JsonResponse
    {
        $pegawai = Pegawai::findOrFail($id);
        $data    = $request->validated();

        // Hanya update password jika diisi
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $pegawai->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil diperbarui.',
            'data'    => new PegawaiResource($pegawai),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil dihapus.',
        ]);
    }
}
