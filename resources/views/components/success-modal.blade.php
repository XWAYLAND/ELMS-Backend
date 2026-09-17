@props(['show' => false, 'title' => 'Succeed!', 'message' => '', 'closeUrl' => null])

<div class="success-modal-overlay" x-data="{ show: @js($show) }" x-show="show" x-cloak @keydown.escape.window="show = false">
  <div class="success-modal-card">
    <svg class="checkmark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M20 6L9 17l-5-5"></path>
    </svg>
    <h2>{{ $title }}</h2>
    @if($message)
      <p class="quote">{{ $message }}</p>
    @endif
    @if($closeUrl)
      <a href="{{ $closeUrl }}" class="btn-close">Close</a>
    @else
      <button type="button" class="btn-close" @click="show = false">Close</button>
    @endif
  </div>
</div>