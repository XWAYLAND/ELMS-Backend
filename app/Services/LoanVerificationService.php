<?php

namespace App\Services;

use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoanVerificationService
{
    /**
     * Verifikasi kode unik / ID transaksi yang dikirim oleh admin.
     *
     * @param  string  $kode  Kode unik atau ID transaksi
     * @return Peminjaman
     *
     * @throws HttpException  404 jika tidak ditemukan
     */
    public function verifyKode(string $kode): Peminjaman
    {
        $kode = trim($kode);

        $peminjaman = Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->where('kode_unik', $kode)
            ->orWhere('id_transaksi', $kode)
            ->first();

        if (! $peminjaman) {
            abort(404, 'Kode unik atau ID transaksi tidak ditemukan.');
        }

        return $peminjaman;
    }

    /**
     * Ambil detail peminjaman berdasarkan ID Transaksi.
     *
     * @param  string  $idTransaksi
     * @return Peminjaman
     *
     * @throws HttpException  404 jika tidak ditemukan
     */
    public function findByTransaksi(string $idTransaksi): Peminjaman
    {
        return Peminjaman::with(['anggota', 'buku.jenis', 'pegawai'])
            ->where('id_transaksi', $idTransaksi)
            ->firstOrFail();
    }

    /**
     * Setujui peminjaman (ubah status: menunggu → aktif).
     *
     * @param  Peminjaman   $peminjaman
     * @param  string|null  $idPegawai  ID pegawai yang menyetujui
     * @return Peminjaman
     *
     * @throws HttpException  422 jika tidak bisa disetujui
     */
    public function approve(Peminjaman $peminjaman, ?string $idPegawai = null): Peminjaman
    {
        if (! $peminjaman->canBeApproved()) {
            $message = $peminjaman->isKodeExpired()
                ? 'Kode unik telah kadaluwarsa (>24 jam).'
                : 'Peminjaman tidak dapat disetujui karena statusnya bukan menunggu.';

            abort(422, $message);
        }

        DB::transaction(function () use ($peminjaman, $idPegawai) {
            $peminjaman->update([
                'status'      => 'aktif',
                'batas_waktu' => now()->addDays($peminjaman->durasi_hari ?? 7),
                'id_pegawai'  => $idPegawai,
            ]);
        });

        return $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);
    }

    /**
     * Tolak peminjaman (ubah status: menunggu → ditolak).
     *
     * @param  Peminjaman  $peminjaman
     * @return Peminjaman
     *
     * @throws HttpException  422 jika status bukan menunggu
     */
    public function reject(Peminjaman $peminjaman): Peminjaman
    {
        if ($peminjaman->status !== 'menunggu') {
            abort(422, 'Hanya permohonan berstatus menunggu yang dapat ditolak.');
        }

        DB::transaction(function () use ($peminjaman) {
            $peminjaman->update(['status' => 'ditolak']);

            if ($peminjaman->buku) {
                $peminjaman->buku->update(['tersedia' => true]);
            }
        });

        return $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);
    }

    /**
     * Konfirmasi pengembalian buku oleh admin.
     *
     * @param  Peminjaman  $peminjaman
     * @return Peminjaman
     *
     * @throws HttpException  422 jika tidak bisa dikembalikan
     */
    public function confirmReturn(Peminjaman $peminjaman): Peminjaman
    {
        if (! $peminjaman->canBeReturned()) {
            abort(422, 'Peminjaman ini tidak berada dalam status yang dapat dikembalikan.');
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

        return $peminjaman->load(['anggota', 'buku.jenis', 'pegawai']);
    }
}
