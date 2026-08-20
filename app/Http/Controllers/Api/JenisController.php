<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJenisRequest;
use App\Http\Requests\UpdateJenisRequest;
use App\Http\Resources\JenisResource;
use App\Models\Jenis;
use Illuminate\Http\JsonResponse;

class JenisController extends Controller
{
    public function index(): JsonResponse
    {
        $jenis = Jenis::all();

        return response()->json([
            'success' => true,
            'message' => 'Data jenis berhasil diambil.',
            'data'    => JenisResource::collection($jenis),
        ]);
    }

    public function store(StoreJenisRequest $request): JsonResponse
    {
        $jenis = Jenis::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil ditambahkan.',
            'data'    => new JenisResource($jenis),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $jenis = Jenis::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data jenis berhasil diambil.',
            'data'    => new JenisResource($jenis),
        ]);
    }

    public function update(UpdateJenisRequest $request, string $id): JsonResponse
    {
        $jenis = Jenis::findOrFail($id);
        $jenis->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil diperbarui.',
            'data'    => new JenisResource($jenis),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $jenis = Jenis::findOrFail($id);
        $jenis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis berhasil dihapus.',
        ]);
    }
}
