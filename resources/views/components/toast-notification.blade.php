{{-- resources/views/components/toast-notification.blade.php --}}
@props(['id' => 'global-toast'])

<div x-data
     x-cloak
     x-show="$store.toast.visible"
     x-transition:enter="toast-enter"
     x-transition:enter-start="toast-enter-start"
     x-transition:enter-end="toast-enter-end"
     x-transition:leave="toast-leave"
     x-transition:leave-start="toast-leave-start"
     x-transition:leave-end="toast-leave-end"
     class="toast-notification"
     :class="'toast-' + $store.toast.type"
     x-id="['toast']">

  {{-- Success icon --}}
  <div x-show="$store.toast.type === 'success'" class="toast-icon">
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="14" cy="14" r="13.5" stroke="currentColor" fill="white"/>
      <path fill-rule="evenodd" clip-rule="evenodd" d="M20 9.7C20.117 9.57 20.208 9.42 20.265 9.26C20.322 9.1 20.344 8.93 20.33 8.76C20.316 8.59 20.267 8.43 20.186 8.28C20.105 8.13 19.994 8 19.86 7.89C19.726 7.78 19.573 7.70 19.408 7.65C19.243 7.60 19.07 7.58 18.898 7.60C18.726 7.62 18.56 7.67 18.408 7.75C18.256 7.83 18.122 7.94 18.013 8.07L12.263 15.31L9.313 12.36C9.067 12.13 8.738 12.00 8.396 12.00C8.054 12.01 7.728 12.14 7.490 12.38C7.252 12.61 7.118 12.93 7.115 13.26C7.112 13.60 7.241 13.92 7.476 14.16L11.476 18.16C11.606 18.29 11.761 18.39 11.932 18.45C12.103 18.52 12.286 18.55 12.470 18.54C12.654 18.53 12.833 18.49 12.997 18.41C13.161 18.33 13.306 18.22 13.423 18.08L20 9.7Z" fill="currentColor"/>
    </svg>
  </div>

  {{-- Error icon --}}
  <div x-show="$store.toast.type === 'error'" class="toast-icon">
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="14" cy="14" r="13.5" stroke="currentColor" fill="white"/>
      <path d="M8 8L20 20M20 8L8 20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
  </div>

  {{-- Info icon --}}
  <div x-show="$store.toast.type === 'info'" class="toast-icon">
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="14" cy="14" r="13.5" stroke="currentColor" fill="white"/>
      <path d="M14 9V14M14 18H14.01" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
    </svg>
  </div>

  <span class="toast-message" x-text="$store.toast.message"></span>
</div>