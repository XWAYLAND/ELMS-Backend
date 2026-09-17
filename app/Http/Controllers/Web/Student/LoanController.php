<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function create(string $isbn)
    {
        $book = Buku::findOrFail($isbn);

        if (!$book->tersedia) {
            return back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }

        return view('student.loans.create', [
            'book' => $book,
            'user' => auth()->guard('anggota')->user(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'isbn' => ['required', 'exists:buku,isbn'],
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $book = Buku::findOrFail($request->isbn);
        $user = auth()->guard('anggota')->user();

        // Check if book is still available
        if (!$book->tersedia) {
            return back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }

        $peminjaman = Peminjaman::create([
            'nis'           => $user->nis,
            'isbn'          => $book->isbn,
            'id_pegawai'    => null,
            'durasi_hari'   => $request->durasi_hari,
            'status'        => 'menunggu',
        ]);

        $book->update(['tersedia' => false]);

        $qr = QrCode::size(200)->generate($peminjaman->kode_unik);

        return view('student.loans.create', compact('book', 'user', 'peminjaman', 'qr'))
            ->with('showQr', true);
    }

    public function index()
    {
        $user = auth()->guard('anggota')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $loans = Peminjaman::with(['buku'])
            ->where('nis', $user->nis)
            ->orderByDesc('waktu_pengajuan')
            ->paginate(10);

        return view('student.loans.index', compact('loans'));
    }

    public function cancel(string $id)
    {
        $user = auth()->guard('anggota')->user();

        $loan = Peminjaman::with('buku')
            ->where('id_transaksi', $id)
            ->where('nis', $user->nis)
            ->firstOrFail();

        if ($loan->status !== 'menunggu') {
            return redirect()->route('loans.index')
                ->with('error', 'Hanya peminjaman berstatus menunggu yang dapat dibatalkan.');
        }

        if ($loan->buku) {
            $loan->buku->update(['tersedia' => true]);
        }

        $loan->delete();

        return redirect()->route('loans.index')
            ->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    public function requestReturn(string $id)
    {
        $user = auth()->guard('anggota')->user();

        $loan = Peminjaman::where('id_transaksi', $id)
            ->where('nis', $user->nis)
            ->firstOrFail();

        if (!in_array($loan->status, ['aktif', 'terlambat'])) {
            return redirect()->route('loans.index')
                ->with('error', 'Hanya buku yang sedang dipinjam yang dapat diajukan pengembalian.');
        }

        $loan->update(['status' => 'menunggu_kembali']);

        return redirect()->route('loans.index')
            ->with('success', 'Pengajuan pengembalian buku berhasil dikirim.');
    }
}
