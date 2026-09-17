<?php

// routes/web.php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Student\HomeController;
use App\Http\Controllers\Web\Student\BookController;
use App\Http\Controllers\Web\Student\LoanController;
use App\Http\Controllers\Web\Student\FavoritController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\BookManagementController;
use App\Http\Controllers\Web\Admin\RequestController;
use App\Http\Controllers\Web\Admin\LoanVerificationController;
use App\Http\Controllers\Web\Admin\UserManagementController;

// ── Auth ──────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ── Public routes ─────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books',        [BookController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');

// ── Student routes ────────────────────────────────────
Route::middleware(['auth.role:siswa'])->prefix('')->group(function () {
    Route::get('/favorites',         [BookController::class, 'favorites'])->name('books.favorites');
    Route::get('/favorites/api',     [FavoritController::class, 'index'])->name('favorites.api');
    Route::post('/favorites/toggle', [FavoritController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/borrow/{isbn}', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/borrow',   [LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans',     [LoanController::class, 'index'])->name('loans.index');
    Route::patch('/loans/{id}/return', [LoanController::class, 'requestReturn'])->name('loans.return');
    Route::delete('/loans/{id}/cancel', [LoanController::class, 'cancel'])->name('loans.cancel');
});

// ── Admin routes ──────────────────────────────────────
Route::middleware(['auth.role:petugas'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');

    Route::get('/books',           [BookManagementController::class, 'index'])->name('books.index');
    Route::get('/books/create',    [BookManagementController::class, 'create'])->name('books.create');
    Route::post('/books',          [BookManagementController::class, 'store'])->name('books.store');
    Route::get('/books/{isbn}/edit',[BookManagementController::class, 'edit'])->name('books.edit');
    Route::put('/books/{isbn}',    [BookManagementController::class, 'update'])->name('books.update');
    Route::delete('/books/{isbn}', [BookManagementController::class, 'destroy'])->name('books.destroy');

    Route::get('/requests',               [RequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{id}/approve',[RequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/requests/{id}/reject', [RequestController::class, 'reject'])->name('requests.reject');
    Route::patch('/requests/{id}/return', [RequestController::class, 'return'])->name('requests.return');

    Route::post('/loans/verify-kode',          [LoanVerificationController::class, 'verify'])->name('loans.verify');
    Route::get('/loans/by-transaksi/{id}',     [LoanVerificationController::class, 'byTransaksi'])->name('loans.byTransaksi');
    Route::post('/loans/{id}/approve-via-kode',[LoanVerificationController::class, 'approve'])->name('loans.approveKode');
    Route::post('/loans/{id}/reject-via-kode', [LoanVerificationController::class, 'reject'])->name('loans.rejectKode');
    Route::post('/loans/{id}/return-via-kode', [LoanVerificationController::class, 'confirmReturn'])->name('loans.returnKode');

    // Users management
    Route::get('/users',                  [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create',           [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users/single',          [UserManagementController::class, 'storeSingle'])->name('users.storeSingle');
    Route::post('/users/bulk',            [UserManagementController::class, 'storeBulk'])->name('users.storeBulk');
    Route::get('/users/template',         [UserManagementController::class, 'downloadTemplate'])->name('users.template');
    Route::get('/users/{id}/edit',        [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}',             [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}',          [UserManagementController::class, 'destroy'])->name('users.destroy');
});
