<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Jenis;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Statistik ringkasan untuk Dashboard Flutter (Petugas & Siswa).
     */
    public function stats(Request $request): JsonResponse
    {
        $totalBuku          = Buku::count();
        $bukuTersedia       = Buku::where('tersedia', true)->count();
        $totalAnggota       = Anggota::count();
        $pinjamanAktif      = Peminjaman::whereIn('status', ['aktif', 'terlambat', 'menunggu_kembali'])->count();
        $pinjamanMenunggu   = Peminjaman::where('status', 'menunggu')->count();
        $pinjamanTerlambat  = Peminjaman::where('status', 'terlambat')
            ->orWhere(function ($q) {
                $q->whereIn('status', ['aktif', 'menunggu_kembali'])
                  ->whereNotNull('batas_waktu')
                  ->where('batas_waktu', '<', now());
            })->count();

        $bukuTerbaru = Buku::with('jenis')->latest()->take(6)->get();
        $kategori    = Jenis::withCount('buku')->get();

        return response()->json([
            'success' => true,
            'message' => 'Statistik dashboard berhasil diambil.',
            'data'    => [
                'summary' => [
                    'total_buku'                 => $totalBuku,
                    'total_buku_tersedia'        => $bukuTersedia,
                    'total_buku_dipinjam'        => $totalBuku - $bukuTersedia,
                    'total_anggota'              => $totalAnggota,
                    'total_peminjaman_aktif'     => $pinjamanAktif,
                    'total_peminjaman_menunggu'  => $pinjamanMenunggu,
                    'total_peminjaman_terlambat' => $pinjamanTerlambat,
                ],
                'kategori'      => $kategori,
                'buku_terbaru'  => BukuResource::collection($bukuTerbaru),
            ],
        ]);
    }
}
