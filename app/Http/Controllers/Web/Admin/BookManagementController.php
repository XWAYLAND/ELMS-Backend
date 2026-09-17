<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Jenis;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BookManagementController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function index()
    {
        return view('admin.books.index', [
            'newBooks'   => Buku::orderByDesc('created_at')->orderBy('isbn')->limit(8)->get(),
            'recentAdds' => Buku::with(['jenis'])->orderByDesc('created_at')->orderBy('isbn')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.books.create', [
            'jenisList' => Jenis::orderBy('nama_jenis')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'             => ['required', 'string', 'max:30', 'unique:buku,isbn'],
            'judul'            => ['required', 'string', 'max:255'],
            'cover_file'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'edisi'            => ['nullable', 'string', 'max:100'],
            'deskripsi_fisik'  => ['nullable', 'string', 'max:255'],
            'bahasa'           => ['nullable', 'string', 'max:50'],
            'id_jenis'         => ['required', 'string', 'max:255'],
            'penulis'          => ['required', 'string', 'max:255'],
            'penerbit'         => ['required', 'string', 'max:255'],
        ]);

        unset($validated['cover_file']);

        $jenisId = null;
        if (!empty($validated['id_jenis'])) {
            $jenis = \App\Models\Jenis::where('nama_jenis', $validated['id_jenis'])->first();
            if (!$jenis) {
                $maxId = \App\Models\Jenis::max('id_jenis');
                $nextNum = $maxId ? intval(substr($maxId, 1)) + 1 : 1;
                $newId = 'J' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                $jenis = \App\Models\Jenis::create([
                    'id_jenis' => $newId,
                    'nama_jenis' => $validated['id_jenis']
                ]);
            }
            $jenisId = $jenis->id_jenis;
        }
        unset($validated['id_jenis']);

        if ($request->hasFile('cover_file')) {
            $validated['cover'] = $this->imageService->storeAsWebp(
                $request->file('cover_file'),
                'covers',
                $validated['isbn']
            );
        }

        \App\Models\Buku::create(array_merge($validated, ['id_jenis' => $jenisId]));

        return redirect()->route('admin.books.edit', $validated['isbn'])->with('book_added', true);
    }

    public function edit(string $isbn)
    {
        $book = Buku::findOrFail($isbn);

        return view('admin.books.edit', [
            'book'      => $book,
            'jenisList' => Jenis::orderBy('nama_jenis')->get(),
        ]);
    }

    public function update(Request $request, string $isbn)
    {
        $book = Buku::findOrFail($isbn);

        $validated = $request->validate([
            'judul'            => ['required', 'string', 'max:255'],
            'cover_file'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'edisi'            => ['nullable', 'string', 'max:100'],
            'deskripsi_fisik'  => ['nullable', 'string', 'max:255'],
            'bahasa'           => ['nullable', 'string', 'max:50'],
            'id_jenis'         => ['nullable', 'string', 'max:255'],
            'penulis'          => ['required', 'string', 'max:255'],
            'penerbit'         => ['required', 'string', 'max:255'],
            'tersedia'         => ['boolean'],
        ]);

        unset($validated['cover_file']);

        $jenisId = $book->id_jenis;
        if (!empty($validated['id_jenis'])) {
            $jenis = \App\Models\Jenis::where('nama_jenis', $validated['id_jenis'])->first();
            if (!$jenis) {
                $maxId = \App\Models\Jenis::max('id_jenis');
                $nextNum = $maxId ? intval(substr($maxId, 1)) + 1 : 1;
                $newId = 'J' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                $jenis = \App\Models\Jenis::create([
                    'id_jenis' => $newId,
                    'nama_jenis' => $validated['id_jenis']
                ]);
            }
            $jenisId = $jenis->id_jenis;
        }
        unset($validated['id_jenis']);

        if ($request->hasFile('cover_file')) {
            $this->imageService->delete($book->cover);
            $validated['cover'] = $this->imageService->storeAsWebp(
                $request->file('cover_file'),
                'covers',
                $isbn
            );
        }

        $book->update(array_merge($validated, ['id_jenis' => $jenisId]));

        return redirect()->route('admin.books.edit', $isbn)->with('book_updated', true);
    }

    public function destroy(string $isbn)
    {
        $book = Buku::findOrFail($isbn);
        
        // Delete associated loan records first to avoid foreign key constraint error
        $book->peminjaman()->delete();

        $this->imageService->delete($book->cover);
        $book->delete();

        return redirect()->route('admin.books.index')->with('book_deleted', true);
    }
}
