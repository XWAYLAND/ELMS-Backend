<?php

namespace App\Services;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanVerificationService
{
    public function findByKode(string $kode): ?Peminjaman
    {
        $loan = Peminjaman::with(['buku', 'anggota'])
            ->findByValidKode($kode)
            ->first();

        return $loan;
    }

    public function approve(string $id): Peminjaman
    {
        return DB::transaction(function () use ($id) {
            $loan = Peminjaman::with('buku')->findOrFail($id);

            if (!$loan->canBeApproved()) {
                throw new \RuntimeException('Peminjaman tidak dapat disetujui (status: ' . $loan->status . ').');
            }

            $loan->update([
                'status'     => 'aktif',
                'id_pegawai' => Auth::guard('pegawai')->id() ?? $this->resolvePegawaiId(),
                'batas_waktu'=> $loan->batas_waktu ?? now()->addDays($loan->durasi_hari),
            ]);

            return $loan;
        });
    }

    public function reject(string $id): Peminjaman
    {
        return DB::transaction(function () use ($id) {
            $loan = Peminjaman::with('buku')->findOrFail($id);

            if ($loan->status !== 'menunggu') {
                throw new \RuntimeException('Hanya peminjaman berstatus menunggu yang dapat ditolak.');
            }

            if ($loan->buku) {
                $loan->buku->update(['tersedia' => true]);
            }

            $loan->update([
                'status'     => 'ditolak',
                'id_pegawai' => Auth::guard('pegawai')->id() ?? $this->resolvePegawaiId(),
            ]);

            return $loan;
        });
    }

    public function confirmReturn(string $id): Peminjaman
    {
        return DB::transaction(function () use ($id) {
            $loan = Peminjaman::with('buku')->findOrFail($id);

            if (!$loan->canBeReturned()) {
                throw new \RuntimeException('Buku ini tidak dapat dikonfirmasi pengembaliannya.');
            }

            if ($loan->buku) {
                $loan->buku->update(['tersedia' => true]);
            }

            $loan->update([
                'status'         => 'dikembalikan',
                'tanggal_kembali'=> now(),
                'id_pegawai'     => Auth::guard('pegawai')->id() ?? $this->resolvePegawaiId(),
            ]);

            return $loan;
        });
    }

    private function resolvePegawaiId(): ?string
    {
        $user = Auth::user();
        if ($user && isset($user->id_pegawai)) {
            return $user->id_pegawai;
        }
        if ($user) {
            $pegawai = Pegawai::where('email', $user->email)->first();
            return $pegawai?->id_pegawai;
        }
        return null;
    }
}