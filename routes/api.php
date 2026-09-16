<?php

use App\Http\Controllers\Api\AnggotaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JenisController;
use App\Http\Controllers\Api\LoanVerificationController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PenerbitController;
use App\Http\Controllers\Api\PenulisController;
use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — E-Library LMS for Flutter & Web Backend
|--------------------------------------------------------------------------
|
| Semua endpoint ini diawali dengan prefix '/api'.
| Autentikasi menggunakan Laravel Sanctum Bearer token.
|
*/

// ─── 1. AUTENTIKASI ─────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
    });
});

// ─── 2. ROUTE PUBLIK (Katalog Buku, Referensi & Dashboard Stats) ────────────
Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

// Katalog & Detail Buku (Mendukung pencarian, filter kategori, dan slug/ISBN)
Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
Route::get('buku/{identifier}', [BukuController::class, 'show'])->name('buku.show');

// Master Referensi
Route::get('jenis', [JenisController::class, 'index'])->name('jenis.index');
Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
Route::get('penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');

// Update FCM Token Anggota
Route::put('anggota/{nis}/fcm-token', [AnggotaController::class, 'updateFcmToken'])->name('anggota.fcm-token');

// ─── 3. ROUTE TERPROTEKSI (Sanctum Token: Siswa & Petugas) ───────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ── FITUR SISWA (Anggota) ──
    // Pengajuan peminjaman baru (menghasilkan kode unik / QR code)
    Route::post('peminjaman/pinjam', [PeminjamanController::class, 'pinjam'])->name('peminjaman.pinjam');
    Route::post('borrow', [PeminjamanController::class, 'pinjam'])->name('peminjaman.borrow.alias');

    // Riwayat peminjaman siswa yang sedang login
    Route::get('peminjaman/saya', [PeminjamanController::class, 'saya'])->name('peminjaman.saya');

    // Siswa membatalkan permohonan peminjaman status menunggu
    Route::delete('peminjaman/{id}/batalkan', [PeminjamanController::class, 'batalkan'])->name('peminjaman.batalkan');
    Route::delete('loans/{id}/cancel', [PeminjamanController::class, 'batalkan'])->name('peminjaman.cancel.alias');

    // Siswa mengajukan pengembalian buku
    Route::patch('peminjaman/{id}/ajukan-kembali', [PeminjamanController::class, 'ajukanKembali'])->name('peminjaman.ajukan-kembali');
    Route::patch('loans/{id}/return', [PeminjamanController::class, 'ajukanKembali'])->name('peminjaman.return.alias');

    // ── FITUR PETUGAS (Pegawai & Admin) ──
    // Verifikasi kode unik / QR code peminjaman
    Route::post('peminjaman/verifikasi-kode', [PeminjamanController::class, 'verifikasiKode'])->name('peminjaman.verifikasi-kode');
    Route::post('loans/verify-kode', [PeminjamanController::class, 'verifikasiKode'])->name('peminjaman.verify.alias');

    // Approval, Reject, dan Konfirmasi Pengembalian oleh Petugas
    Route::post('peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::post('peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    Route::post('peminjaman/{id}/confirm-return', [PeminjamanController::class, 'confirmReturn'])->name('peminjaman.confirm-return');

    // Notifikasi / Daftar Jatuh Tempo
    Route::get('peminjaman/jatuh-tempo', [PeminjamanController::class, 'mendekatiJatuhTempo'])->name('peminjaman.jatuh-tempo');

    // CRUD Peminjaman
    Route::apiResource('peminjaman', PeminjamanController::class);

    // CRUD Master Data (Manajemen Buku, Anggota, Petugas, Kategori)
    Route::apiResource('buku', BukuController::class)->except(['index', 'show']);
    Route::apiResource('anggota', AnggotaController::class);
    Route::apiResource('pegawai', PegawaiController::class);
    Route::apiResource('jenis', JenisController::class)->except(['index']);
    Route::apiResource('penulis', PenulisController::class)->except(['index']);
    Route::apiResource('penerbit', PenerbitController::class)->except(['index']);

    // ── ADMIN — Verifikasi QR / Kode Unik ──
    Route::prefix('admin/loans')->name('admin.loans.')->group(function () {
        Route::post('verify-kode', [LoanVerificationController::class, 'verifyKode'])
            ->name('verify-kode');
        Route::get('by-transaksi/{id}', [LoanVerificationController::class, 'byTransaksi'])
            ->name('by-transaksi');
        Route::post('{id}/approve-via-kode', [LoanVerificationController::class, 'approveViaKode'])
            ->name('approve-via-kode');
        Route::post('{id}/reject-via-kode', [LoanVerificationController::class, 'rejectViaKode'])
            ->name('reject-via-kode');
        Route::post('{id}/return-via-kode', [LoanVerificationController::class, 'returnViaKode'])
            ->name('return-via-kode');
    });

    // ── ADMIN — Daftar Pengajuan Peminjaman (dengan filter period & status) ──
    Route::get('admin/requests', [RequestController::class, 'index'])->name('admin.requests.index');
});
