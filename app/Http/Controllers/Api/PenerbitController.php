<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenerbitRequest;
use App\Http\Requests\UpdatePenerbitRequest;
use App\Http\Resources\PenerbitResource;
use App\Models\Penerbit;
use Illuminate\Http\JsonResponse;

class PenerbitController extends Controller
{
    public function index(): JsonResponse
    {
        $penerbit = Penerbit::all();

        return response()->json([
            'success' => true,
            'message' => 'Data penerbit berhasil diambil.',
            'data'    => PenerbitResource::collection($penerbit),
        ]);
    }

    public function store(StorePenerbitRequest $request): JsonResponse
    {
        $penerbit = Penerbit::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil ditambahkan.',
            'data'    => new PenerbitResource($penerbit),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $penerbit = Penerbit::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data penerbit berhasil diambil.',
            'data'    => new PenerbitResource($penerbit),
        ]);
    }

    public function update(UpdatePenerbitRequest $request, string $id): JsonResponse
    {
        $penerbit = Penerbit::findOrFail($id);
        $penerbit->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil diperbarui.',
            'data'    => new PenerbitResource($penerbit),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $penerbit = Penerbit::findOrFail($id);
        $penerbit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penerbit berhasil dihapus.',
        ]);
    }
}
