<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenulisRequest;
use App\Http\Requests\UpdatePenulisRequest;
use App\Http\Resources\PenulisResource;
use App\Models\Penulis;
use Illuminate\Http\JsonResponse;

class PenulisController extends Controller
{
    public function index(): JsonResponse
    {
        $penulis = Penulis::all();

        return response()->json([
            'success' => true,
            'message' => 'Data penulis berhasil diambil.',
            'data'    => PenulisResource::collection($penulis),
        ]);
    }

    public function store(StorePenulisRequest $request): JsonResponse
    {
        $penulis = Penulis::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil ditambahkan.',
            'data'    => new PenulisResource($penulis),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $penulis = Penulis::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data penulis berhasil diambil.',
            'data'    => new PenulisResource($penulis),
        ]);
    }

    public function update(UpdatePenulisRequest $request, string $id): JsonResponse
    {
        $penulis = Penulis::findOrFail($id);
        $penulis->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil diperbarui.',
            'data'    => new PenulisResource($penulis),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $penulis = Penulis::findOrFail($id);
        $penulis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penulis berhasil dihapus.',
        ]);
    }
}
