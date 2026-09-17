{{-- resources/views/livewire/books-filter.blade.php --}}
<div class="books-filter">

  {{-- Search bar --}}
  <div class="search-wrapper">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
        <path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input type="text"
             wire:model.live.debounce.200ms="search"
             placeholder="Search Title/Author/ISBN"
             class="search-input">
    </div>

    {{-- Category chips --}}
    <div class="category-chips">
      <button type="button"
              wire:click="$set('kategori', 'semua')"
              class="category-chip {{ $kategori === 'semua' ? 'active' : '' }}">
        All
      </button>
      @if($categories->count() > 0)
        @foreach($categories as $cat)
          <button type="button"
                  wire:click="$set('kategori', '{{ $cat->id_jenis }}')"
                  class="category-chip {{ $kategori === $cat->id_jenis ? 'active' : '' }}">
            {{ $cat->nama_jenis }}
          </button>
        @endforeach
      @endif
    </div>
  </div>

  {{-- Book grid --}}
  <div class="book-grid" wire:loading.class="is-loading" wire:target="search, kategori">
    @forelse($this->books as $book)
      <x-book-grid-card :book="$book" wire:key="book-{{ $book->isbn }}" />
    @empty
      <p class="empty-state">No books found.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="pagination-wrapper">
    @if($this->books->hasPages())
      {{ $this->books->links('vendor.pagination.custom') }}
    @else
      <nav class="pagination-nav">
        <span class="page-btn page-btn--disabled">&laquo;</span>
        <span class="page-btn page-btn--active">1</span>
        <span class="page-btn page-btn--disabled">&raquo;</span>
      </nav>
    @endif
  </div>

</div>
