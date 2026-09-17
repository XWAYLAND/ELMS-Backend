{{-- resources/views/components/book-grid-card.blade.php --}}
@props(['book'])

<a href="{{ route('books.show', $book->slug ?? $book->isbn) }}" class="book-grid-card">
  <img src="{{ $book->cover_url }}"
       alt="{{ $book->judul }}"
       class="book-grid-card__img"
       loading="lazy">
</a>
