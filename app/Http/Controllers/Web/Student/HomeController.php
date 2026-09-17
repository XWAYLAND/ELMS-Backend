<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Jenis;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Jenis::query()->get();
        $booksByCategory = Buku::query()
            ->orderByDesc('tersedia')
            ->orderBy('judul')
            ->get()
            ->groupBy('id_jenis');

        $homeCategories = $categories
            ->map(function ($category) use ($booksByCategory) {
                $books = $booksByCategory->get($category->id_jenis, collect());

                if ($books->isEmpty()) {
                    return null;
                }

                return [
                    'id' => $category->id_jenis,
                    'nama' => $category->nama_jenis,
                    'covers' => $books->take(3)
                        ->map(fn (Buku $book) => $book->cover_url)
                        ->values()
                        ->all(),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return view('student.home', [
            'recommendedBooks' => Buku::tersedia()->get()->toArray(),
            'homeCategories'   => $homeCategories,
        ]);
    }
}
