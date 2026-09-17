{{-- resources/views/student/books/favorites.blade.php --}}
@extends('layouts.app')

@section('title', 'Your Favorites')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/favorites.css') }}">
@endpush

@section('content')
<div class="favorites-page" x-data="{
  currentPage: 1,
  perPage: 12,
  allBooks: {{ Js::from($books) }},
  get favoriteBooks() {
    if (!this.$store.favorites) return [];
    const isbns = this.$store.favorites.items;
    return this.allBooks.filter(book => isbns.includes(book.isbn));
  },
  get favoriteCount() {
    return this.favoriteBooks.length;
  },
  get totalPages() {
    return Math.ceil(this.favoriteCount / this.perPage) || 1;
  },
  get paginatedBooks() {
    const start = (this.currentPage - 1) * this.perPage;
    return this.favoriteBooks.slice(start, start + this.perPage);
  },
  goToPage(page) {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }
}">

  {{-- Page Heading SVG --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--favorites">
    <img src="{{ asset('svg/Your Favorites.svg') }}" alt="Your Favorites">
  </div>

  {{-- Favorites table card --}}
  <div class="favorites-table-card">

    {{-- Table header --}}
    <div class="favorites-table-header" x-show="favoriteCount > 0">
      <div class="favorites-col-book">Book</div>
      <div class="favorites-col-author">Penulis</div>
      <div class="favorites-col-subject">Subject</div>
      <div class="favorites-col-action">Detail</div>
    </div>

    {{-- Table rows --}}
    <template x-for="book in paginatedBooks" :key="book.isbn">
      <div class="favorites-table-row">
        {{-- Book column --}}
        <div class="favorites-col-book favorites-book-cell">
          <img :src="book.cover_url"
               :alt="book.judul"
               class="favorites-book-thumb">
          <div>
            <div class="favorites-book-info-title" x-text="book.judul"></div>
            <div class="favorites-book-info-author" x-text="book.penulis || '-'"></div>
          </div>
        </div>

        {{-- Author column --}}
        <div class="favorites-col-author favorites-author-cell" x-text="book.penulis || '-'"></div>

        {{-- Subject column --}}
        <div class="favorites-col-subject favorites-subject-cell" x-text="book.jenis ? book.jenis.nama_jenis : '-'"></div>

        {{-- Action column --}}
        <div class="favorites-col-action favorites-action-cell">
          <a :href="'/books/' + (book.slug || book.isbn)" class="btn-view-book">
            View Book
          </a>
        </div>
      </div>
    </template>

    {{-- Empty State --}}
    <div class="favorites-empty" x-show="favoriteCount === 0" x-cloak>
      <div class="favorites-empty-icon">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
      </div>
      <div class="favorites-empty-title">Belum ada buku favorit</div>
      <p class="favorites-empty-desc">Simpan buku favorit Anda agar mudah ditemukan kembali nanti.</p>
      <a href="{{ route('books.index') }}" class="btn-browse-books">
        Jelajahi Buku
      </a>
    </div>

  </div>

  {{-- Pagination --}}
  <div class="pagination-wrapper" x-show="favoriteCount > 0" x-cloak>
    <nav class="pagination-nav">
      <button type="button"
              class="page-btn"
              :class="{ 'page-btn--disabled': currentPage === 1 }"
              :disabled="currentPage === 1"
              @click="goToPage(currentPage - 1)">
        &laquo;
      </button>

      <template x-for="p in totalPages" :key="p">
        <button type="button"
                class="page-btn"
                :class="{ 'page-btn--active': currentPage === p }"
                @click="goToPage(p)"
                x-text="p">
        </button>
      </template>

      <button type="button"
              class="page-btn"
              :class="{ 'page-btn--disabled': currentPage === totalPages }"
              :disabled="currentPage === totalPages"
              @click="goToPage(currentPage + 1)">
        &raquo;
      </button>
    </nav>
  </div>

</div>
@endsection
