{{-- resources/views/components/header.blade.php --}}
<header class="app-header">
  <div class="header-inner">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="header-logo">
      <img src="{{ asset('assets/logo.svg') }}" alt="e-LiBraRy" width="120" height="36">
    </a>

    {{-- Nav links --}}
    <nav class="header-nav">
      @php
        $activeSection = match(true) {
            request()->routeIs('home')                      => 'home',
            request()->routeIs('books.favorites')           => 'favorites',
            request()->routeIs('books.index', 'books.show') => 'books',
            request()->routeIs('loans.index', 'loans.create') => 'loans',
            default                                         => null,
        };
        $isSiswa = Auth::guard('anggota')->check();
        $isPetugas = Auth::guard('pegawai')->check();
      @endphp

      <a href="{{ route('home') }}"
         class="nav-link {{ $activeSection === 'home' ? 'active' : '' }}">
        Home
      </a>
      <a href="{{ route('books.index') }}"
         class="nav-link {{ $activeSection === 'books' ? 'active' : '' }}">
        Books
      </a>
      @if(!$isPetugas)
        <a href="{{ route('books.favorites') }}"
           class="nav-link {{ $activeSection === 'favorites' ? 'active' : '' }}">
          Favorites
        </a>
      @endif
      <a href="{{ route('loans.index') }}"
         class="nav-link {{ $activeSection === 'loans' ? 'active' : '' }}">
        Loan
      </a>
    </nav>

    {{-- Right: Login CTA or user greeting/logout --}}
    <div class="header-right">
      @if($isSiswa || $isPetugas)
          @if($isSiswa)
          <div x-data="{ open: false }" @click.outside="open = false" style="position: relative;">
            <button type="button" class="profile-trigger" @click="open = !open">
              <div class="avatar-circle" x-text="'{{ Auth::guard('anggota')->user()->inisial }}'"></div>
              <div class="profile-trigger-info">
                <div class="profile-trigger-name">{{ Auth::guard('anggota')->user()->nama_lengkap }}</div>
                <div class="profile-trigger-nis">{{ Auth::guard('anggota')->user()->nis }}</div>
              </div>
              <svg class="profile-trigger-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{ 'rotate-180': open }">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>

            <div class="profile-dropdown-card" x-show="open" x-transition x-cloak>
              <div class="profile-dropdown-header">
                <div class="profile-dropdown-avatar" x-text="'{{ Auth::guard('anggota')->user()->inisial }}'"></div>
                <div>
                  <div class="profile-dropdown-name">{{ Auth::guard('anggota')->user()->nama_lengkap }}</div>
                  <div class="profile-dropdown-nis">{{ Auth::guard('anggota')->user()->nis }}</div>
                </div>
              </div>

              <hr class="profile-dropdown-divider">

              <div class="profile-dropdown-logout" @click="document.getElementById('logout-form').submit()">
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                  <polyline points="16 17 21 12 16 7"/>
                  <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Log Out
              </div>
            </div>
          </div>
        @else
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
          </form>
        @endif
      @else
        <a href="{{ route('login') }}" class="btn-login-cta">Login</a>
      @endif
    </div>

  </div>
</header>