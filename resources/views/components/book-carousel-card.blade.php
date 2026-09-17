{{-- resources/views/components/book-carousel-card.blade.php --}}
@props(['book'])

@php
    $slug = is_array($book) ? ($book['slug'] ?? $book['isbn']) : ($book->slug ?? $book->isbn);
    $cover = is_array($book) ? ($book['cover_url'] ?? asset('images/book-placeholder.png')) : $book->cover_url;
    $title = is_array($book) ? $book['judul'] : $book->judul;
@endphp

<a href="{{ route('books.show', $slug) }}" class="carousel-card">
  <img src="{{ $cover }}"
       alt="{{ $title }}"
       class="carousel-card__img"
       loading="lazy">
</a>