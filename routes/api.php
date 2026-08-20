<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnggotaController;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\JenisController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\PenerbitController;
use App\Http\Controllers\Api\PenulisController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — E-Library Management System
|--------------------------------------------------------------------------
|
| Semua endpoint menggunakan prefix /api secara otomatis oleh Laravel.
| Endpoint yang di dalam middleware 'auth:sanctum' memerlukan Bearer token.
|
*/

// ─── AUTH ─────────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
    });
});

// ─── ROUTE PUBLIK (tidak perlu login) ─────────────────────────────────────────
// Buku: Anggota bisa melihat katalog buku tanpa login
Route::get('buku', [BukuController::class, 'index'])->name('buku.index');
Route::get('buku/{isbn}', [BukuController::class, 'show'])->name('buku.show');

// Jenis, Penulis, Penerbit: Referensi untuk filter
Route::get('jenis', [JenisController::class, 'index'])->name('jenis.index');
Route::get('penulis', [PenulisController::class, 'index'])->name('penulis.index');
Route::get('penerbit', [PenerbitController::class, 'index'])->name('penerbit.index');

// Update FCM token anggota (dipanggil saat app dibuka)
Route::put('anggota/{nis}/fcm-token', [AnggotaController::class, 'updateFcmToken'])->name('anggota.fcm-token');

// ─── ROUTE TERPROTEKSI (harus login sebagai pegawai) ──────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // JENIS
    Route::apiResource('jenis', JenisController::class)->except(['index']);

    // PENULIS
    Route::apiResource('penulis', PenulisController::class)->except(['index']);

    // PENERBIT
    Route::apiResource('penerbit', PenerbitController::class)->except(['index']);

    // BUKU
    Route::apiResource('buku', BukuController::class)->except(['index', 'show']);

    // ANGGOTA
    Route::apiResource('anggota', AnggotaController::class);

    // PEGAWAI
    Route::apiResource('pegawai', PegawaiController::class);

    // PEMINJAMAN
    Route::get('peminjaman/jatuh-tempo', [PeminjamanController::class, 'mendekatiJatuhTempo'])->name('peminjaman.jatuh-tempo');
    Route::apiResource('peminjaman', PeminjamanController::class);
});
