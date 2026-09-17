{{-- resources/views/student/books/show.blade.php --}}
@extends('layouts.app')

@section('title', $book->judul )

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/book-detail.css') }}">
<link rel="stylesheet" href="{{ asset('css/components/modal-share.css') }}">
@endpush

@section('content')

<div class="book-detail-page">

  {{-- Page heading --}}
  <div class="svg-heading-placeholder">
    <img src="{{ asset('svg/Book Detail.svg') }}" alt="Book Detail">
  </div>

  <div class="book-detail-body">

    {{-- Cover --}}
    <div class="book-detail-cover">
      <img src="{{ $book->cover_url }}" alt="{{ $book->judul }}">
    </div>

    {{-- Info card --}}
    <div class="book-detail-info">
      <h1 class="book-detail-title">{{ $book->judul }}</h1>
      <p class="book-detail-author">{{ $book->penulis }}</p>

      <span class="book-detail-chip">{{ $book->jenis->nama_jenis }}</span>

      <table class="book-meta-table">
        <tr><td>Edition</td><td>{{ $book->edisi }}</td></tr>
        <tr><td>Publisher</td><td>{{ $book->penerbit }}</td></tr>
        <tr><td>Physical Description</td><td>{{ $book->deskripsi_fisik }}</td></tr>
        <tr><td>ISBN</td><td>{{ $book->isbn }}</td></tr>
        <tr><td>Subject</td><td>{{ $book->jenis->nama_jenis }}</td></tr>
        <tr><td>Language</td><td>{{ $book->bahasa }}</td></tr>
      </table>
    </div>

  </div>

  {{-- Quote --}}
  <div class="book-detail-quote">
    <p>"A room without books is like a body without a soul."</p>
    <small>- Marcus Tullius Cicero</small>
  </div>

</div>

{{-- Floating action bar --}}
<div class="book-detail-fab" x-data>
  <button type="button" class="fab-action" @click="$dispatch('open-share-modal')">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="6" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/>
      <circle cx="17.5" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
      <circle cx="17.5" cy="18.5" r="2.5" stroke="currentColor" stroke-width="1.8"/>
      <path d="M8.2 10.8l7-4M8.2 13.2l7 4" stroke="currentColor" stroke-width="1.8"/>
    </svg>
    Share this book
  </button>

  <button type="button"
          class="fab-action"
          :class="{ 'is-favorited': $store.favorites.isFavorited('{{ $book->isbn }}') }"
          @click="$store.favorites.toggle('{{ $book->isbn }}')">
    <svg viewBox="0 0 24 24"
         fill="none"
         xmlns="http://www.w3.org/2000/svg"
         :class="{ 'filled': $store.favorites.isFavorited('{{ $book->isbn }}') }">
      <path d="M6 3.5h12v17l-6-4.5-6 4.5z"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linejoin="round"/>
    </svg>
    <span x-text="$store.favorites.isFavorited('{{ $book->isbn }}') ? 'Remove from Favorite' : 'Add to Favorite'"></span>
  </button>

  @if($book->tersedia)
    <a href="{{ route('loans.create', $book->isbn) }}" class="fab-cta">
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
      </svg>
      Borrow this book
    </a>
  @else
    <span class="fab-cta disabled">Unavailable</span>
  @endif
</div>

<x-modal-share
  :book-url="route('books.show', $book->slug)"
  :book-title="$book->judul" />

@endsection
