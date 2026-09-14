<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuController extends Controller
{
    /**
     * Daftar buku dengan fitur pencarian & filter.
     *
     * Query params:
     *  - search   : cari berdasarkan judul, ISBN, penulis, penerbit, atau jenis
     *  - id_jenis : filter berdasarkan jenis / kategori (contoh: JNS-005)
     *  - tersedia : filter ketersediaan (true/false atau 1/0)
     *  - bahasa   : filter berdasarkan bahasa
     *  - per_page : jumlah item per halaman (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Buku::with(['jenis'])
            ->search($request->input('search'))
            ->byKategori($request->input('id_jenis'))
            ->filterBahasa($request->input('bahasa'));

        if ($request->has('tersedia')) {
            $tersedia = filter_var($request->input('tersedia'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($tersedia !== null) {
                $query->where('tersedia', $tersedia);
            }
        }

        if ($request->boolean('all')) {
            $buku = $query->latest()->get();
            return response()->json([
                'success' => true,
                'message' => 'Seluruh data buku berhasil diambil.',
                'data'    => BukuResource::collection($buku),
            ]);
        }

        $perPage = (int) $request->input('per_page', 15);
        $buku    = $query->latest()->paginate($perPage);

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

    /**
     * Tambah data buku baru.
     */
    public function store(StoreBukuRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_file')) {
            $path = $request->file('cover_file')->store('covers', 'public');
            $data['cover'] = $path;
        }

        $buku = Buku::create($data);
        $buku->load(['jenis']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan.',
            'data'    => new BukuResource($buku),
        ], 201);
    }

    /**
     * Detail buku berdasarkan ISBN atau Slug.
     */
    public function show(string $identifier): JsonResponse
    {
        $buku = Buku::with(['jenis'])
            ->where('isbn', $identifier)
            ->orWhere('slug', $identifier)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diambil.',
            'data'    => new BukuResource($buku),
        ]);
    }

    /**
     * Update data buku.
     */
    public function update(UpdateBukuRequest $request, string $isbn): JsonResponse
    {
        $buku = Buku::findOrFail($isbn);
        $data = $request->validated();

        if ($request->hasFile('cover_file')) {
            if ($buku->cover && ! Str::startsWith($buku->cover, ['http://', 'https://'])) {
                Storage::disk('public')->delete($buku->cover);
            }
            $path = $request->file('cover_file')->store('covers', 'public');
            $data['cover'] = $path;
        }

        $buku->update($data);
        $buku->load(['jenis']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui.',
            'data'    => new BukuResource($buku),
        ]);
    }

    /**
     * Hapus data buku.
     */
    public function destroy(string $isbn): JsonResponse
    {
        $buku = Buku::findOrFail($isbn);

        if ($buku->cover && ! Str::startsWith($buku->cover, ['http://', 'https://'])) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus.',
        ]);
    }
}
