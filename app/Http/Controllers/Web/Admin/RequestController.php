<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\LoanVerificationService;

class RequestController extends Controller
{
    public function __construct(private LoanVerificationService $service) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Peminjaman::with(['anggota', 'buku']);

        // Filter by status if provided and not 'all'
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by period (sorting order by waktu_pengajuan)
        $period = $request->get('period', 'newest');
        if ($period === 'oldest') {
            $query->orderBy('waktu_pengajuan', 'asc');
        } else {
            $query->orderBy('waktu_pengajuan', 'desc');
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('admin.requests.index', [
            'requests'      => $requests,
            'currentPeriod' => $period,
            'currentStatus' => $request->get('status', 'all'),
        ]);
    }

    public function approve(string $id)
    {
        try {
            $this->service->approve($id);
            return redirect()->route('admin.requests.index')
                ->with('success', 'Pengajuan disetujui.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(string $id)
    {
        try {
            $this->service->reject($id);
            return redirect()->route('admin.requests.index')
                ->with('success', 'Pengajuan ditolak.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function return(string $id)
    {
        try {
            $this->service->confirmReturn($id);
            return redirect()->route('admin.requests.index')
                ->with('success', 'Buku berhasil dikembalikan.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
