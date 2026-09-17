{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script>window.__isLoggedIn = {{ auth()->guard('anggota')->check() ? 'true' : 'false' }};</script>
  <title>@yield('title', 'Home')</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">

  {{-- Google Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  {{-- Global CSS --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  {{-- Component CSS --}}
  <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/loader.css') }}">

  {{-- Livewire --}}
  @livewireStyles

  {{-- Page-specific CSS --}}
  @stack('styles')

  {{-- Alpine.js stores --}}
  <script src="{{ asset('js/toast-store.js') }}"></script>
  <script src="{{ asset('js/favorite-store.js') }}"></script>

  {{-- Alpine.js --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
  <img src="{{ asset('svg/Background Layer.svg') }}" class="global-page-bg" alt="" aria-hidden="true">

  {{-- Page Loader --}}
  <x-loader />

  {{-- Reusable Header --}}
  @include('components.header')

  {{-- Page Content --}}
  <main>
    @yield('content')
  </main>

  @stack('scripts')

  @livewireScripts
  <script>
    window.addEventListener('load', function() {
      var loader = document.getElementById('pageLoader');
      if (loader) {
        loader.classList.add('is-hidden');
        setTimeout(function() { loader.style.display = 'none'; }, 300);
      }
    });
  </script>
</body>
</html>
