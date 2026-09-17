<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Favorit;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Buku::with(['jenis'])
            ->search($request->get('search'))
            ->byKategori($request->get('kategori'))
            ->orderBy('judul')
            ->orderBy('isbn')
            ->paginate(12);

        return view('student.books.index', [
            'books'      => $books,
            'categories' => \App\Models\Jenis::all(),
            'activeKat'  => $request->get('kategori', 'semua'),
            'search'     => $request->get('search', ''),
        ]);
    }

    public function show(string $slug)
    {
        $book = Buku::with(['jenis'])->where('slug', $slug)->firstOrFail();

        return view('student.books.show', compact('book'));
    }

    public function favorites(Request $request)
    {
        $nis   = auth()->guard('anggota')->user()->nis;
        $isbns = Favorit::where('nis', $nis)->pluck('isbn');

        $books = Buku::with(['jenis'])
            ->whereIn('isbn', $isbns)
            ->orderBy('judul')
            ->paginate(12)
            ->withQueryString();

        return view('student.books.favorites', compact('books'));
    }
}
