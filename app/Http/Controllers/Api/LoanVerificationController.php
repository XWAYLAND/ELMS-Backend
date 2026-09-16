<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Pegawai;
use App\Services\LoanVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanVerificationController extends Controller
{
    public function __construct(protected LoanVerificationService $service) {}

    /**
     * POST /admin/loans/verify-kode
     * Validasi kode unik/QR yang di-scan atau diketik oleh admin.
     */
    public function verifyKode(Request $request): JsonResponse
    {
        $request->validate([
            'kode_unik' => ['required', 'string', 'max:20'],
        ]);

        $peminjaman = $this->service->verifyKode($request->input('kode_unik'));

        return response()->json([
            'success' => true,
            'message' => 'Kode berhasil diverifikasi.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * GET /admin/loans/by-transaksi/{id}
     * Fetch detail peminjaman berdasarkan ID transaksi.
     */
    public function byTransaksi(string $id): JsonResponse
    {
        $peminjaman = $this->service->findByTransaksi($id);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * POST /admin/loans/{id}/approve-via-kode
     * Persetujuan peminjaman oleh admin setelah verifikasi kode.
     */
    public function approveViaKode(Request $request, string $id): JsonResponse
    {
        $peminjaman = $this->service->findByTransaksi($id);

        $idPegawai = $request->user() instanceof Pegawai
            ? $request->user()->id_pegawai
            : $request->input('id_pegawai');

        $peminjaman = $this->service->approve($peminjaman, $idPegawai);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman buku berhasil disetujui.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * POST /admin/loans/{id}/reject-via-kode
     * Penolakan peminjaman oleh admin.
     */
    public function rejectViaKode(Request $request, string $id): JsonResponse
    {
        $peminjaman = $this->service->findByTransaksi($id);
        $peminjaman = $this->service->reject($peminjaman);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman telah ditolak.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * POST /admin/loans/{id}/return-via-kode
     * Konfirmasi pengembalian buku oleh admin.
     */
    public function returnViaKode(Request $request, string $id): JsonResponse
    {
        $peminjaman = $this->service->findByTransaksi($id);
        $peminjaman = $this->service->confirmReturn($peminjaman);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikonfirmasi kembali.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }
}
