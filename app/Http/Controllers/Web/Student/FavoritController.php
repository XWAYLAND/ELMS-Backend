<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Favorit;
use Illuminate\Http\Request;

class FavoritController extends Controller
{
    private function user()
    {
        return auth()->guard('anggota')->user();
    }

    /** GET /favorites/api → JSON list of ISBNs */
    public function index()
    {
        $isbns = Favorit::where('nis', $this->user()->nis)->pluck('isbn');
        return response()->json($isbns);
    }

    /** POST /favorites/api { isbn } → toggle */
    public function toggle(Request $request)
    {
        $request->validate(['isbn' => 'required|string|exists:buku,isbn']);

        $nis  = $this->user()->nis;
        $isbn = $request->isbn;

        $existing = Favorit::where('nis', $nis)->where('isbn', $isbn)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['action' => 'removed', 'isbn' => $isbn]);
        }

        Favorit::create(['nis' => $nis, 'isbn' => $isbn]);
        return response()->json(['action' => 'added', 'isbn' => $isbn]);
    }
}
