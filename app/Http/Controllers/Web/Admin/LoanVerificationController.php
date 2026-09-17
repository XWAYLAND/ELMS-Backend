<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\LoanVerificationService;
use Illuminate\Http\Request;

class LoanVerificationController extends Controller
{
    public function __construct(private LoanVerificationService $service) {}

    public function verify(Request $request)
    {
        $request->validate([
            'kode_unik' => ['required', 'string', 'max:20'],
        ]);

        $loan = $this->service->findByKode($request->kode_unik);

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Kode tidak ditemukan.',
            ], 404);
        }

        if ($loan->isKodeExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode sudah kedaluwarsa (lebih dari 24 jam).',
            ], 410);
        }

        if (in_array($loan->status, ['dikembalikan', 'ditolak'])) {
            return response()->json([
                'success' => false,
                'message' => 'Kode sudah tidak berlaku (status: ' . $loan->status . ').',
            ], 410);
        }

        return response()->json([
            'success' => true,
            'loan'    => [
                'id_transaksi'   => $loan->id_transaksi,
                'kode_unik'      => $loan->kode_unik,
                'status'         => $loan->status,
                'status_label'   => $this->statusLabel($loan->status),
                'anggota_nama'   => $loan->anggota->nama_lengkap ?? '-',
                'anggota_nis'    => $loan->anggota->nis ?? '-',
                'buku_judul'     => $loan->buku->judul ?? '-',
                'buku_isbn'      => $loan->buku->isbn ?? '-',
                'durasi_hari'    => $loan->durasi_hari,
                'batas_waktu'    => $loan->batas_waktu?->format('d M Y'),
                'waktu_pengajuan'=> $loan->waktu_pengajuan->format('d M Y H:i'),
                'can_approve'    => $loan->canBeApproved(),
                'can_return'     => $loan->canBeReturned(),
            ],
        ]);
    }

    public function approve(string $id)
    {
        try {
            $loan = $this->service->approve($id);
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman disetujui.',
                'status'  => $loan->status,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function reject(string $id)
    {
        try {
            $loan = $this->service->reject($id);
            return response()->json([
                'success' => true,
                'message' => 'Peminjaman ditolak.',
                'status'  => $loan->status,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function confirmReturn(string $id)
    {
        try {
            $loan = $this->service->confirmReturn($id);
            return response()->json([
                'success' => true,
                'message' => 'Pengembalian dikonfirmasi.',
                'status'  => $loan->status,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function byTransaksi(string $id)
    {
        $loan = Peminjaman::with(['anggota', 'buku'])->where('id_transaksi', $id)->first();

        if (!$loan) {
            return response()->json([
                'success' => false,
                'message' => 'Loan not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'loan'    => [
                'id_transaksi'   => $loan->id_transaksi,
                'kode_unik'      => $loan->kode_unik,
                'status'         => $loan->status,
                'status_label'   => $this->statusLabel($loan->status),
                'anggota_nama'   => $loan->anggota->nama_lengkap ?? '-',
                'anggota_nis'    => $loan->anggota->nis ?? '-',
                'buku_judul'     => $loan->buku->judul ?? '-',
                'buku_isbn'      => $loan->buku->isbn ?? '-',
                'durasi_hari'    => $loan->durasi_hari,
                'batas_waktu'    => $loan->batas_waktu?->format('d M Y'),
                'waktu_pengajuan'=> $loan->waktu_pengajuan->format('d M Y H:i'),
                'can_approve'    => $loan->canBeApproved(),
                'can_return'     => $loan->canBeReturned(),
            ],
        ]);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'menunggu'         => 'Pending',
            'aktif'            => 'Loaning',
            'terlambat'        => 'Overdue',
            'menunggu_kembali' => 'Return Requested',
            'dikembalikan'     => 'Returned',
            'ditolak'          => 'Rejected',
            default            => ucfirst($status),
        };
    }
}
