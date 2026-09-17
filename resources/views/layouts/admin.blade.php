{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Dashboard')</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">

  {{-- Google Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  {{-- Global CSS --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/loader.css') }}">

  {{-- Page-specific CSS --}}
  @stack('styles')

  {{-- Alpine.js stores --}}
  <script src="{{ asset('js/toast-store.js') }}"></script>

  {{-- Alpine.js --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Handle session-based toast alerts (e.g. after redirect) --}}
  <script>
    document.addEventListener('alpine:initialized', () => {
      // Check Laravel Session Flash
      @if(session('success') || session('book_deleted'))
        setTimeout(() => {
          Alpine.store('toast').show("{{ session('success') ?? 'Data berhasil dihapus.' }}", 'success');
        }, 100);
      @endif

      @if(session('error'))
        setTimeout(() => {
          Alpine.store('toast').show("{{ session('error') }}", 'error');
        }, 100);
      @endif

      // Check SessionStorage from JS fetch reloads
      const jsToast = sessionStorage.getItem('toast_message');
      const jsToastType = sessionStorage.getItem('toast_type') || 'success';
      if (jsToast) {
        setTimeout(() => {
          Alpine.store('toast').show(jsToast, jsToastType);
        }, 100);
        sessionStorage.removeItem('toast_message');
        sessionStorage.removeItem('toast_type');
      }
    });
  </script>
</head>
<body>
  <img src="{{ asset('svg/Background Layer.svg') }}" class="global-page-bg" alt="" aria-hidden="true">

  {{-- Global Toast Notification --}}
  <x-toast-notification />

  {{-- Page Loader --}}
  <x-loader />

  {{-- Admin Top Header --}}
  <header class="app-header">
    <div class="header-inner">
      {{-- Logo --}}
      <a href="{{ route('admin.dashboard') }}" class="header-logo">
        <img src="{{ asset('assets/logo.svg') }}" alt="e-LiBraRy" width="120" height="36">
      </a>

      {{-- Nav links --}}
      <nav class="header-nav">
        @php
          $activeSection = match(true) {
              request()->routeIs('admin.dashboard') => 'dashboard',
              request()->routeIs('admin.books.*')   => 'books',
              request()->routeIs('admin.requests.*')=> 'requests',
              request()->routeIs('admin.users.*')   => 'users',
              default                               => null,
          };
        @endphp

        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ $activeSection === 'dashboard' ? 'active' : '' }}">
          Dashboard
        </a>
        <a href="{{ route('admin.books.index') }}"
           class="nav-link {{ $activeSection === 'books' ? 'active' : '' }}">
          Books
        </a>
        <a href="{{ route('admin.requests.index') }}"
           class="nav-link {{ $activeSection === 'requests' ? 'active' : '' }}">
          Requests
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="nav-link {{ $activeSection === 'users' ? 'active' : '' }}">
          Users
        </a>
      </nav>

      {{-- Right: Logout --}}
      <div class="header-right">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn-logout">Logout</button>
        </form>
      </div>
    </div>
  </header>

  {{-- Page Content --}}
  <main>
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
