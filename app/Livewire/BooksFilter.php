<?php

namespace App\Livewire;

use App\Models\Buku;
use App\Models\Jenis;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BooksFilter extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'semua')]
    public string $kategori = 'semua';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKategori(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function books()
    {
        return Buku::with('jenis')
            ->search($this->search)
            ->byKategori($this->kategori === 'semua' ? null : $this->kategori)
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.books-filter', [
            'categories' => Jenis::all(),
        ]);
    }
}
