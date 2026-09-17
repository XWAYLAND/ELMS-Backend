<nav class="pagination-nav">
  @if ($paginator->onFirstPage())
    <span class="page-btn page-btn--disabled">&laquo;</span>
  @else
    <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="page-btn">&laquo;</button>
  @endif

  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="page-btn page-btn--disabled">{{ $element }}</span>
    @endif

    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="page-btn page-btn--active">{{ $page }}</span>
        @else
          <button type="button" wire:click="gotoPage({{ $page }})" class="page-btn">{{ $page }}</button>
        @endif
      @endforeach
    @endif
  @endforeach

  @if ($paginator->hasMorePages())
    <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="page-btn">&raquo;</button>
  @else
    <span class="page-btn page-btn--disabled">&raquo;</span>
  @endif
</nav>
