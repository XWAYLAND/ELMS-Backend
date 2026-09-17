<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Anggota;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $loanQuery = Peminjaman::with(['anggota', 'buku']);

        if ($request->filled('status') && $request->status !== 'all') {
            $loanQuery->where('status', $request->status);
        }

        $period = $request->get('period', 'newest');
        $loanQuery->orderBy('waktu_pengajuan', $period === 'oldest' ? 'asc' : 'desc');

        return view('admin.dashboard', [
            'totalBooks'    => Buku::count(),
            'activeUsers'   => Anggota::count(), // assuming all members active
            'newUsersMonth' => Anggota::whereMonth('created_at', now()->month)->count(),
            'overdueCount'  => Peminjaman::where('status', 'terlambat')->count(),
            'newBooksMonth' => Buku::whereMonth('created_at', now()->month)->count(),
            'overdueMonth'  => Peminjaman::where('status', 'terlambat')->whereMonth('updated_at', now()->month)->count(),
            'loans'         => $loanQuery->paginate(10)->withQueryString(),
            'currentPeriod' => $period,
            'currentStatus' => $request->get('status', 'all'),
        ]);
    }
}
