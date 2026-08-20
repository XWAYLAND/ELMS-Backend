<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    /**
     * Daftar peminjaman dengan filter status, NIS, ISBN, dan pencarian.
     *
     * Query params:
     *  - status     : filter berdasarkan status (pending|dipinjam|dikembalikan|terlambat)
     *  - nis        : filter berdasarkan NIS anggota
     *  - isbn       : filter berdasarkan ISBN buku
     *  - per_page   : jumlah item per halaman (default: 15)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Peminjaman::with(['anggota', 'buku.jenis', 'buku.penulis', 'buku.penerbit', 'pegawai'])
            ->status($request->input('status'));

        if ($request->has('nis')) {
            $query->where('nis', $request->input('nis'));
        }

        if ($request->has('isbn')) {
            $query->where('isbn', $request->input('isbn'));
        }

        $peminjaman = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil.',
            'data'    => PeminjamanResource::collection($peminjaman),
            'meta'    => [
                'current_page' => $peminjaman->currentPage(),
                'last_page'    => $peminjaman->lastPage(),
                'per_page'     => $peminjaman->perPage(),
                'total'        => $peminjaman->total(),
            ],
        ]);
    }

    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $peminjaman = Peminjaman::create($request->validated());
        $peminjaman->load(['anggota', 'buku', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibuat.',
            'data'    => new PeminjamanResource($peminjaman),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku.jenis', 'buku.penulis', 'buku.penerbit', 'pegawai'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    public function update(UpdatePeminjamanRequest $request, string $id): JsonResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update($request->validated());
        $peminjaman->load(['anggota', 'buku', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diperbarui.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dihapus.',
        ]);
    }

    /**
     * Mendapatkan daftar peminjaman yang mendekati atau sudah melewati batas kembali.
     * Digunakan oleh Flutter untuk menampilkan notifikasi lokal.
     *
     * Query params:
     *  - hari : jumlah hari sebelum jatuh tempo (default: 3)
     */
    public function mendekatiJatuhTempo(Request $request): JsonResponse
    {
        $hari = (int) $request->input('hari', 3);

        $peminjaman = Peminjaman::with(['anggota', 'buku', 'pegawai'])
            ->mendekatiJatuhTempo($hari)
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Peminjaman yang mendekati jatuh tempo (dalam {$hari} hari).",
            'data'    => PeminjamanResource::collection($peminjaman),
        ]);
    }
}
