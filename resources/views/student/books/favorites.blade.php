{{-- resources/views/student/books/favorites.blade.php --}}
@extends('layouts.app')

@section('title', 'Your Favorites')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/favorites.css') }}">
@endpush

@section('content')
<div class="favorites-page">

  {{-- Page Heading SVG --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--favorites">
    <img src="{{ asset('svg/Your Favorites.svg') }}" alt="Your Favorites">
  </div>

  {{-- Favorites table card --}}
  <div class="favorites-table-card">

    @if($books->count() > 0)
      {{-- Table header --}}
      <div class="favorites-table-header">
        <div class="favorites-col-book">Book</div>
        <div class="favorites-col-author">Penulis</div>
        <div class="favorites-col-subject">Subject</div>
        <div class="favorites-col-action">Detail</div>
      </div>

      {{-- Table rows --}}
      @foreach($books as $book)
        <div class="favorites-table-row">
          {{-- Book column --}}
          <div class="favorites-col-book favorites-book-cell">
            <img src="{{ $book->cover_url }}"
                 alt="{{ $book->judul }}"
                 class="favorites-book-thumb">
            <div>
              <div class="favorites-book-info-title">{{ $book->judul }}</div>
              <div class="favorites-book-info-author">{{ $book->penulis ?? '-' }}</div>
            </div>
          </div>

          {{-- Author column --}}
          <div class="favorites-col-author favorites-author-cell">{{ $book->penulis ?? '-' }}</div>

          {{-- Subject column --}}
          <div class="favorites-col-subject favorites-subject-cell">{{ $book->jenis->nama_jenis ?? '-' }}</div>

          {{-- Action column --}}
          <div class="favorites-col-action favorites-action-cell">
            <a href="{{ route('books.show', $book->slug ?? $book->isbn) }}" class="btn-view-book">
              View Book
            </a>
          </div>
        </div>
      @endforeach
    @else
      {{-- Empty State --}}
      <div class="favorites-empty">
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
    @endif

  </div>

  {{-- Pagination --}}
  @if($books->hasPages())
    <div class="pagination-wrapper">
      <nav class="pagination-nav">
        @if($books->onFirstPage())
          <button class="page-btn page-btn--disabled" disabled>&laquo;</button>
        @else
          <a class="page-btn" href="{{ $books->previousPageUrl() }}">&laquo;</a>
        @endif

        @foreach($books->getUrlRange(1, $books->lastPage()) as $page => $url)
          <a class="page-btn {{ $page == $books->currentPage() ? 'page-btn--active' : '' }}"
             href="{{ $url }}">{{ $page }}</a>
        @endforeach

        @if($books->hasMorePages())
          <a class="page-btn" href="{{ $books->nextPageUrl() }}">&raquo;</a>
        @else
          <button class="page-btn page-btn--disabled" disabled>&raquo;</button>
        @endif
      </nav>
    </div>
  @endif

</div>
@endsection
