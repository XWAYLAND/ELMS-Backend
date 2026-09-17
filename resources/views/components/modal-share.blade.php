{{-- resources/views/components/modal-share.blade.php --}}
@props(['bookUrl', 'bookTitle'])

@php
  $pageUrl = urlencode($bookUrl);
  $shareText = urlencode('Lihat buku ini: ' . $bookTitle . ' ' . $bookUrl);
@endphp

<div x-data="{ open: false, copied: false, url: {{ Js::from($bookUrl) }} }"
     @open-share-modal.window="open = true"
     @keydown.escape.window="open = false"
     x-cloak>
  <div class="share-modal-overlay"
       x-show="open"
       x-transition:enter="share-modal-fade-enter"
       x-transition:enter-start="share-modal-fade-start"
       x-transition:enter-end="share-modal-fade-end"
       x-transition:leave="share-modal-fade-leave"
       x-transition:leave-start="share-modal-fade-end"
       x-transition:leave-end="share-modal-fade-start"
       @click.self="open = false">
    <section class="share-modal-card"
             role="dialog"
             aria-modal="true"
             aria-labelledby="share-modal-title">
      <h2 id="share-modal-title">Share this book!</h2>

      <div class="share-platforms">
        <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="share-platform share-platform--whatsapp">
          <img src="{{ asset('svg/social_media/Whatsapp_logo.svg') }}" alt="WhatsApp" class="share-platform__icon">
          <span>WhatsApp</span>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $pageUrl }}" target="_blank" rel="noopener noreferrer" class="share-platform share-platform--facebook">
          <img src="{{ asset('svg/social_media/Facebook_logo.svg') }}" alt="Facebook" class="share-platform__icon">
          <span>Facebook</span>
        </a>
        <a href="https://twitter.com/intent/tweet?url={{ $pageUrl }}" target="_blank" rel="noopener noreferrer" class="share-platform share-platform--x">
          <img src="{{ asset('svg/social_media/X_logo.svg') }}" alt="X" class="share-platform__icon">
          <span>X</span>
        </a>
        <a href="https://t.me/share/url?url={{ $pageUrl }}" target="_blank" rel="noopener noreferrer" class="share-platform share-platform--telegram">
          <img src="{{ asset('svg/social_media/Telegram_logo.svg') }}" alt="Telegram" class="share-platform__icon">
          <span>Telegram</span>
        </a>
      </div>

      <label for="share-url" class="share-modal-label">Copy Link:</label>
      <div class="share-copy-row">
        <input id="share-url" type="text" :value="url" readonly>
        <button type="button" class="share-copy-button" @click="navigator.clipboard.writeText(url).then(() => { copied = true; setTimeout(() => copied = false, 2000) })" :aria-label="copied ? 'Tersalin' : 'Salin tautan'">
          <span x-show="!copied" aria-hidden="true">⧉</span>
          <span x-show="copied">Copied!</span>
        </button>
      </div>

      <button type="button" class="share-modal-back" @click="open = false">Back</button>
    </section>
  </div>
</div>
