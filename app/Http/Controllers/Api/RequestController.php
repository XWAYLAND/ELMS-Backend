<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use App\Services\LoanVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct(protected LoanVerificationService $service) {}

    /**
     * Daftar semua pengajuan peminjaman untuk tampilan admin.
     *
     * Query params:
     *  - period : urutan waktu — 'newest' (default) atau 'oldest'
     *  - status : filter berdasarkan status peminjaman
     *  - search : pencarian berdasarkan ID transaksi, nama anggota, atau judul buku
     *  - per_page: jumlah item per halaman (default: 10)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->status($request->input('status'));

        // Filter pencarian
        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(id_transaksi) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(kode_unik) LIKE ?', [$search])
                    ->orWhereHas('anggota', fn ($a) => $a
                        ->whereRaw('LOWER(nama_lengkap) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(nis) LIKE ?', [$search])
                    )
                    ->orWhereHas('buku', fn ($b) => $b
                        ->whereRaw('LOWER(judul) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(isbn) LIKE ?', [$search])
                    );
            });
        }

        // Sorting berdasarkan periode — newest (default) atau oldest
        $period = $request->input('period', 'newest');
        $direction = $period === 'oldest' ? 'asc' : 'desc';
        $query->orderBy('waktu_pengajuan', $direction);

        $perPage = (int) $request->input('per_page', 10);
        $peminjaman = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Data pengajuan peminjaman berhasil diambil.',
            'data'    => PeminjamanResource::collection($peminjaman),
            'meta'    => [
                'current_page' => $peminjaman->currentPage(),
                'last_page'    => $peminjaman->lastPage(),
                'per_page'     => $peminjaman->perPage(),
                'total'        => $peminjaman->total(),
                'links'        => [
                    'first' => $peminjaman->url(1),
                    'last'  => $peminjaman->url($peminjaman->lastPage()),
                    'prev'  => $peminjaman->previousPageUrl(),
                    'next'  => $peminjaman->nextPageUrl(),
                ],
            ],
        ]);
    }
}
