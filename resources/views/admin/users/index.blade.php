@extends('layouts.admin')

@section('title', 'Users')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/admin-books.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages/admin-users.css') }}">
@endpush

@section('content')
<div class="admin-users-page" x-data="{ deleteTarget: null, deleteName: '' }">

  {{-- Header row: heading + search --}}
  <div class="users-page-header">
    <div class="svg-heading-placeholder">
      <span class="users-heading-text">Users</span>
    </div>

</div>
<form class="users-search-form" method="GET" action="{{ route('admin.users.index') }}" id="users-search-form">
  <div class="users-search-wrap">
    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
    </svg>
    <input
      type="text"
      name="q"
      class="users-search-input"
      placeholder="Cari nama, ID, kelas, email..."
      value="{{ $q }}"
      x-data
      x-model.debounce.400ms="$el.value"
      @input.debounce.400ms="$el.closest('form').submit()"
    >
    @if($q)
      <a href="{{ route('admin.users.index') }}" class="search-clear-btn" title="Hapus pencarian">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </a>
    @endif
  </div>
</form>

  {{-- Import errors (from bulk import) --}}
  @if(session('import_errors') && count(session('import_errors')) > 0)
  <div class="import-errors-card">
    <strong>Baris yang gagal diimport:</strong>
    <ul>
      @foreach(session('import_errors') as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  {{-- Users Table --}}
  <div class="recently-added-table recently-added-table--index">
    <div class="recently-added-header users-table-header">
      <div class="col-user">Pengguna</div>
      <div class="col-uid">ID</div>
      <div class="col-sub">Kelas / Email</div>
      <div class="col-role">Role</div>
      <div class="col-menu"></div>
    </div>

    @forelse($users as $user)
      <div class="recently-added-row users-table-row">
        {{-- Avatar + Name --}}
        <div class="col-user">
          <div class="user-avatar">
            {{ mb_strtoupper(mb_substr($user->nama, 0, 2)) }}
          </div>
          <span class="user-name">{{ $user->nama }}</span>
        </div>

        {{-- ID --}}
        <div class="col-uid">{{ $user->id }}</div>

        {{-- Sub (kelas or email) --}}
        <div class="col-sub">{{ $user->sub ?: '-' }}</div>

        {{-- Role badge --}}
        <div class="col-role">
          <span class="role-badge role-badge--{{ $user->role }}">
            {{ $user->role === 'student' ? 'Student' : 'Staff' }}
          </span>
        </div>

        {{-- Hamburger menu --}}
        <div class="col-menu" x-data="{ open: false }" @click.outside="open = false">
          <button type="button" class="menu-btn" @click="open = !open">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
              <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
            </svg>
          </button>
          <div x-show="open" x-transition x-cloak class="dropdown">
            <a href="{{ route('admin.users.edit', $user->id) }}">Edit</a>
            <button
              type="button"
              @click="deleteTarget = '{{ route('admin.users.destroy', $user->id) }}'; deleteName = '{{ addslashes($user->nama) }}'; open = false"
            >Delete</button>
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state">
        @if($q)
          Tidak ada pengguna yang cocok dengan pencarian "<strong>{{ $q }}</strong>".
        @else
          Belum ada pengguna terdaftar.
        @endif
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  @if($users->hasPages())
  <div class="pagination-wrapper">
    <nav class="pagination-nav">
      @if($users->onFirstPage())
        <span class="page-btn page-btn--disabled">&laquo;</span>
      @else
        <a href="{{ $users->previousPageUrl() }}" class="page-btn">&laquo;</a>
      @endif

      @for($i = 1; $i <= $users->lastPage(); $i++)
        @if($i == $users->currentPage())
          <span class="page-btn page-btn--active">{{ $i }}</span>
        @else
          <a href="{{ $users->url($i) }}" class="page-btn">{{ $i }}</a>
        @endif
      @endfor

      @if($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}" class="page-btn">&raquo;</a>
      @else
        <span class="page-btn page-btn--disabled">&raquo;</span>
      @endif
    </nav>
  </div>
  @endif

  {{-- FAB: Add User --}}
  <a href="{{ route('admin.users.create') }}" class="fab-add">+</a>

  {{-- Delete Confirmation Modal --}}
  <div x-show="deleteTarget" x-cloak class="modal-overlay" @keydown.escape.window="deleteTarget = null">
    <div class="modal-card" @click.outside="deleteTarget = null">
      <div class="modal-cancel-icon">
        <svg width="96" height="96" viewBox="0 0 168 168" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M84 21C49.35 21 21 49.35 21 84C21 118.65 49.35 147 84 147C118.65 147 147 118.65 147 84C147 49.35 118.65 21 84 21ZM84 35C94.85 35 105 38.85 113.4 44.8L44.8 113.4C38.85 105 35 94.85 35 84C35 57.05 57.05 35 84 35ZM84 133C73.15 133 63 129.15 54.6 123.2L123.2 54.6C129.15 63 133 73.15 133 84C133 110.95 110.95 133 84 133Z" fill="#FF0004"/>
        </svg>
      </div>
      <h3 class="modal-cancel-title">Hapus Pengguna?</h3>
      <p class="modal-cancel-desc" x-text="'Hapus akun ' + deleteName + '? Tindakan ini tidak bisa dibatalkan.'"></p>
      <form :action="deleteTarget" method="POST" class="modal-cancel-actions">
        @csrf
        @method('DELETE')
        <button type="button" class="btn-cancel-close" @click="deleteTarget = null">Batal</button>
        <button type="submit" class="btn-cancel-confirm">Hapus</button>
      </form>
    </div>
  </div>

</div>
@endsection
