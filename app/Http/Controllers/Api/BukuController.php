<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Daftar buku dengan fitur pencarian & filter.
     *
     * Query params:
     *  - search   : cari berdasarkan judul, penulis, penerbit, jenis
     *  - id_jenis    : filter berdasarkan jenis
     *  - id_penulis  : filter berdasarkan penulis
     *  - id_penerbit : filter berdasarkan penerbit
     *  - bahasa      : filter berdasarkan bahasa
     *  - per_page    : jumlah item per halaman (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Buku::with(['jenis', 'penulis', 'penerbit'])
            ->search($request->input('search'))
            ->filterJenis($request->input('id_jenis'))
            ->filterPenulis($request->input('id_penulis'))
            ->filterPenerbit($request->input('id_penerbit'))
            ->filterBahasa($request->input('bahasa'));

        $perPage = $request->input('per_page', 15);
        $buku    = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diambil.',
            'data'    => BukuResource::collection($buku),
            'meta'    => [
                'current_page' => $buku->currentPage(),
                'last_page'    => $buku->lastPage(),
                'per_page'     => $buku->perPage(),
                'total'        => $buku->total(),
            ],
        ]);
    }

    public function store(StoreBukuRequest $request): JsonResponse
    {
        $buku = Buku::create($request->validated());
        $buku->load(['jenis', 'penulis', 'penerbit']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan.',
            'data'    => new BukuResource($buku),
        ], 201);
    }

    public function show(string $isbn): JsonResponse
    {
        $buku = Buku::with(['jenis', 'penulis', 'penerbit'])->findOrFail($isbn);

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diambil.',
            'data'    => new BukuResource($buku),
        ]);
    }

    public function update(UpdateBukuRequest $request, string $isbn): JsonResponse
    {
        $buku = Buku::findOrFail($isbn);
        $buku->update($request->validated());
        $buku->load(['jenis', 'penulis', 'penerbit']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui.',
            'data'    => new BukuResource($buku),
        ]);
    }

    public function destroy(string $isbn): JsonResponse
    {
        $buku = Buku::findOrFail($isbn);
        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus.',
        ]);
    }
}
