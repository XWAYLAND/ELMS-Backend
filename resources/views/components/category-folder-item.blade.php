{{-- resources/views/components/category-folder-item.blade.php --}}
@props(['category'])

<a href="{{ route('books.index', ['kategori' => $category['id']]) }}"
  class="category-folder"
  aria-label="{{ $category['nama'] }}">

  <div class="category-folder__visual">
        <img src="{{ asset('svg/category_folder/folder-back.svg') }}"
         alt=""
          class="category-folder__back">
    <div class="category-folder__covers">
      @foreach(array_slice($category['covers'] ?? [], 0, 3) as $i => $cover)
        <img src="{{ $cover ?: asset('images/book-placeholder.png') }}"
             alt="{{ $category['nama'] }} book cover {{ $i + 1 }}"
             class="category-folder__cover category-folder__cover--{{ $i }}">
      @endforeach
    </div>
        <img src="{{ asset('svg/category_folder/folder-front.svg') }}"
          alt=""
          class="category-folder__front">
  </div>

  <span class="category-folder__name">{{ $category['nama'] }}</span>
</a>
