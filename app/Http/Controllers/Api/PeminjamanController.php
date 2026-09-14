<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePeminjamanRequest;
use App\Http\Requests\UpdatePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    /**
     * Daftar semua peminjaman (dengan filter status, NIS, ISBN, pencarian).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->status($request->input('status'));

        if ($request->filled('nis')) {
            $query->where('nis', $request->input('nis'));
        }

        if ($request->filled('isbn')) {
            $query->where('isbn', $request->input('isbn'));
        }

        if ($request->filled('search')) {
            $search = '%' . strtolower($request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(id_transaksi) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(kode_unik) LIKE ?', [$search])
                    ->orWhereHas('anggota', fn ($a) => $a->whereRaw('LOWER(nama_lengkap) LIKE ?', [$search])->orWhereRaw('LOWER(nis) LIKE ?', [$search]))
                    ->orWhereHas('buku', fn ($b) => $b->whereRaw('LOWER(judul) LIKE ?', [$search])->orWhereRaw('LOWER(isbn) LIKE ?', [$search]));
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $peminjaman = $query->latest('waktu_pengajuan')->paginate($perPage);

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

    /**
     * Detail peminjaman berdasarkan ID Transaksi atau Kode Unik.
     */
    public function show(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->where('id_transaksi', $id)
            ->orWhere('kode_unik', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diambil.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Siswa/Petugas membuat permohonan peminjaman buku.
     */
    public function pinjam(StorePeminjamanRequest $request): JsonResponse
    {
        $user = $request->user();
        $nis  = $request->input('nis');

        // Jika user adalah siswa, otomatis gunakan NIS-nya
        if ($user instanceof Anggota) {
            $nis = $user->nis;
        }

        if (! $nis) {
            throw ValidationException::withMessages([
                'nis' => ['NIS anggota wajib dicantumkan.'],
            ]);
        }

        $isbn       = $request->input('isbn');
        $durasiHari = (int) $request->input('durasi_hari', 7);

        $buku = Buku::findOrFail($isbn);

        if (! $buku->tersedia) {
            return response()->json([
                'success' => false,
                'message' => 'Buku sedang tidak tersedia untuk dipinjam.',
            ], 422);
        }

        $peminjaman = DB::transaction(function () use ($nis, $isbn, $durasiHari, $buku) {
            $loan = Peminjaman::create([
                'nis'             => $nis,
                'isbn'            => $isbn,
                'durasi_hari'     => $durasiHari,
                'status'          => 'menunggu',
                'waktu_pengajuan' => now(),
            ]);

            // Kunci ketersediaan buku
            $buku->update(['tersedia' => false]);

            return $loan;
        });

        $peminjaman->load(['anggota', 'buku.jenis']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman berhasil dibuat. Tunjukkan kode unik / QR code ke petugas.',
            'data'    => new PeminjamanResource($peminjaman),
        ], 201);
    }

    /**
     * Mendapatkan riwayat peminjaman siswa yang sedang login.
     */
    public function saya(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof Anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya siswa yang dapat mengakses riwayat peminjaman pribadi.',
            ], 403);
        }

        $query = Peminjaman::with(['buku.jenis'])
            ->where('nis', $user->nis);

        if ($request->input('filter') === 'aktif') {
            $query->whereIn('status', ['menunggu', 'aktif', 'menunggu_kembali', 'terlambat']);
        } elseif ($request->input('filter') === 'riwayat') {
            $query->whereIn('status', ['dikembalikan', 'ditolak']);
        }

        $peminjaman = $query->latest('waktu_pengajuan')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman saya berhasil diambil.',
            'data'    => PeminjamanResource::collection($peminjaman),
        ]);
    }

    /**
     * Siswa membatalkan pengajuan pinjaman yang masih 'menunggu'.
     */
    public function batalkan(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $peminjaman = Peminjaman::with('buku')->findOrFail($id);

        if ($user instanceof Anggota && $peminjaman->nis !== $user->nis) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk membatalkan pengajuan ini.',
            ], 403);
        }

        if ($peminjaman->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengajuan berstatus menunggu yang dapat dibatalkan.',
            ], 422);
        }

        DB::transaction(function () use ($peminjaman) {
            if ($peminjaman->buku) {
                $peminjaman->buku->update(['tersedia' => true]);
            }
            $peminjaman->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman berhasil dibatalkan.',
        ]);
    }

    /**
     * Siswa mengajukan pengembalian buku.
     */
    public function ajukanKembali(Request $request, string $id): JsonResponse
    {
        $user = $request->user();
        $peminjaman = Peminjaman::findOrFail($id);

        if ($user instanceof Anggota && $peminjaman->nis !== $user->nis) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke peminjaman ini.',
            ], 403);
        }

        if (! in_array($peminjaman->status, ['aktif', 'terlambat'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya peminjaman berstatus aktif atau terlambat yang dapat diajukan pengembalian.',
            ], 422);
        }

        $peminjaman->update(['status' => 'menunggu_kembali']);
        $peminjaman->load(['anggota', 'buku', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan pengembalian berhasil dibuat. Serahkan buku ke petugas perpustakaan.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Petugas memverifikasi kode unik (dari input teks atau scan QR).
     */
    public function verifikasiKode(Request $request): JsonResponse
    {
        $request->validate([
            'kode_unik' => ['required', 'string', 'max:20'],
        ]);

        $kode = trim($request->input('kode_unik'));
        $peminjaman = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->where('kode_unik', $kode)
            ->orWhere('id_transaksi', $kode)
            ->first();

        if (! $peminjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Kode unik atau ID transaksi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode berhasil diverifikasi.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Petugas menyetujui peminjaman (Approval).
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with('buku')->findOrFail($id);

        if (! $peminjaman->canBeApproved()) {
            return response()->json([
                'success' => false,
                'message' => $peminjaman->isKodeExpired()
                    ? 'Kode unik telah kadaluwarsa (>24 jam).'
                    : 'Peminjaman tidak dapat disetujui karena statusnya bukan menunggu.',
            ], 422);
        }

        $pegawaiId = $request->user() instanceof Pegawai ? $request->user()->id_pegawai : $request->input('id_pegawai');

        DB::transaction(function () use ($peminjaman, $pegawaiId) {
            $peminjaman->update([
                'status'      => 'aktif',
                'batas_waktu' => now()->addDays($peminjaman->durasi_hari ?? 7),
                'id_pegawai'  => $pegawaiId,
            ]);
        });

        $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman buku berhasil disetujui.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Petugas menolak peminjaman.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with('buku')->findOrFail($id);

        if ($peminjaman->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya permohonan berstatus menunggu yang dapat ditolak.',
            ], 422);
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update(['status' => 'ditolak']);
            if ($peminjaman->buku) {
                $peminjaman->buku->update(['tersedia' => true]);
            }
        });

        $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan peminjaman telah ditolak.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Petugas mengonfirmasi pengembalian buku.
     */
    public function confirmReturn(Request $request, string $id): JsonResponse
    {
        $peminjaman = Peminjaman::with('buku')->findOrFail($id);

        if (! $peminjaman->canBeReturned()) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman ini tidak berada dalam status yang dapat dikembalikan.',
            ], 422);
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update([
                'status'          => 'dikembalikan',
                'tanggal_kembali' => now(),
            ]);

            if ($peminjaman->buku) {
                $peminjaman->buku->update(['tersedia' => true]);
            }
        });

        $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikonfirmasi kembali.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    /**
     * Mendapatkan daftar peminjaman yang mendekati atau melewati batas waktu.
     */
    public function mendekatiJatuhTempo(Request $request): JsonResponse
    {
        $hari = (int) $request->input('hari', 3);

        $peminjaman = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->mendekatiJatuhTempo($hari)
            ->get();

        return response()->json([
            'success' => true,
            'message' => "Daftar peminjaman jatuh tempo dalam {$hari} hari.",
            'data'    => PeminjamanResource::collection($peminjaman),
        ]);
    }

    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        return $this->pinjam($request);
    }

    public function update(UpdatePeminjamanRequest $request, string $id): JsonResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update($request->validated());
        $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diperbarui.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil dihapus.',
        ]);
    }
}
