@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages/admin-requests.css') }}">
@endpush

@section('content')
<div class="admin-dashboard" x-data="verifyKode()">
  {{-- SVG Heading: Dashboard --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--dashboard">
    <img src="{{ asset('svg/admin_icons/Dashboard.svg') }}" alt="Dashboard">
  </div>

  {{-- Stats Row --}}
  <div class="stats-row">
    {{-- Card 1: Total Books --}}
    <div class="stat-card stat-card--primary">
      <div class="stat-card__icon">
        <img src="{{ asset('svg/admin_icons/stats/books.svg') }}" alt="Books">
      </div>
      <div class="stat-card__label">Total Books</div>
      <div class="stat-card__number">{{ $totalBooks ?? 0 }}</div>
      <div class="stat-card__subtext">{{ $newBooksMonth ?? 0 }} new books this month</div>
    </div>

    {{-- Card 2: Active User --}}
    <div class="stat-card stat-card--default">
      <div class="stat-card__icon">
        <img src="{{ asset('svg/admin_icons/stats/user.svg') }}" alt="User">
      </div>
      <div class="stat-card__label">Active User</div>
      <div class="stat-card__number">{{ $activeUsers ?? 0 }}</div>
      <div class="stat-card__subtext">{{ $newUsersMonth ?? 0 }} new user this month</div>
    </div>

    {{-- Card 3: Overdue Books --}}
    <div class="stat-card stat-card--default">
      <div class="stat-card__icon">
        <img src="{{ asset('svg/admin_icons/stats/overdue.svg') }}" alt="Overdue">
      </div>
      <div class="stat-card__label">Overdue Books</div>
      <div class="stat-card__number stat-card__number--highlight">{{ $overdueCount ?? 0 }}</div>
      <div class="stat-card__subtext">{{ $overdueMonth ?? 0 }} overdue books this month</div>
    </div>
  </div>

  {{-- SVG Heading: Recent Loaner --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--recent-loaner">
    <img src="{{ asset('svg/admin_icons/Recent Loaner.svg') }}" alt="Recent Loaner">
  </div>

  {{-- Recent Loaner Table --}}
  <div class="table-wrapper dashboard-loans-table-wrapper">
    <table class="requests-table dashboard-loans-table">
      <thead>
        <tr>
          <th style="width: 25%;">User</th>
          <th style="width: 30%;">Book</th>
          <th style="width: 25%;">
            <div class="th-filter-wrapper" x-data="{ open: false }">
              <button type="button" class="th-filter-btn" @click="open = !open" @click.outside="open = false">
                <span>Period</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
              </button>
              <div class="th-filter-dropdown" x-show="open" x-cloak x-transition>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'newest']) }}" class="th-filter-item {{ ($currentPeriod ?? 'newest') === 'newest' ? 'active' : '' }}">Newest</a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'oldest']) }}" class="th-filter-item {{ ($currentPeriod ?? '') === 'oldest' ? 'active' : '' }}">Oldest</a>
              </div>
            </div>
          </th>
          <th style="width: 15%;">
            <div class="th-filter-wrapper" x-data="{ open: false }">
              <button type="button" class="th-filter-btn" @click="open = !open" @click.outside="open = false">
                <span>Status</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
              </button>
              <div class="th-filter-dropdown" x-show="open" x-cloak x-transition>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'all']) }}" class="th-filter-item {{ ($currentStatus ?? 'all') === 'all' ? 'active' : '' }}">All Status</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'menunggu']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'menunggu' ? 'active' : '' }}">Pending</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'aktif']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'aktif' ? 'active' : '' }}">Loaning</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'terlambat']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'terlambat' ? 'active' : '' }}">Overdue</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'menunggu_kembali']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'menunggu_kembali' ? 'active' : '' }}">Return Requested</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'dikembalikan']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'dikembalikan' ? 'active' : '' }}">Returned</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'ditolak']) }}" class="th-filter-item {{ ($currentStatus ?? '') === 'ditolak' ? 'active' : '' }}">Rejected</a>
              </div>
            </div>
          </th>
          <th style="width: 50px;"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $loan)
      @php
        $statusLabel = match($loan->status) {
          'menunggu' => 'Pending',
          'aktif' => 'Loaning',
          'terlambat' => 'Overdue',
          'menunggu_kembali' => 'Return Requested',
          'dikembalikan' => 'Returned',
          'ditolak' => 'Rejected',
          'disetujui' => 'Approved',
          default => ucfirst($loan->status),
        };
        $statusStyle = match($loan->status) {
          'menunggu' => 'background: #FFFBEB; color: #D97706;',
          'aktif' => 'background: #FFF3E0; color: #FF8400;',
          'terlambat' => 'background: #FFEBEE; color: #FF0034;',
          'menunggu_kembali' => 'background: #F3E5F5; color: #8B5CF6;',
          'dikembalikan' => 'background: #F3F4F6; color: #6B7280;',
          'ditolak' => 'background: #FFEBEE; color: #B71C1C;',
          'disetujui' => 'background: #E8F5E9; color: #00C853;',
          default => 'background: #F3F4F6; color: #6B7280;',
        };
      @endphp
          <tr>
            <td>
              <div class="user-info">
                <div class="user-avatar">{{ $loan->anggota->inisial ?? '?' }}</div>
                <div class="user-details">
                  <strong>{{ $loan->anggota->nama_lengkap ?? '-' }}</strong>
                  <small>{{ $loan->anggota->nis ?? '-' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div class="book-info">
                @if($loan->buku)
                  <img src="{{ $loan->buku->cover_url }}" alt="cover" class="book-cover">
                @else
                  <div class="book-cover-placeholder">-</div>
                @endif
                <div class="book-details">
                  <strong>{{ $loan->buku->judul ?? '-' }}</strong>
                  <small>{{ $loan->buku->penulis ?? '-' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div class="period-info">
                <span>{{ $loan->waktu_pengajuan?->format('d M Y') ?? '-' }}</span>
                <span class="period-sep">-</span>
                <span>{{ $loan->batas_waktu?->format('d M Y') ?? '-' }}</span>
              </div>
            </td>
            <td><span class="status-badge" style="{{ $statusStyle }}">{{ $statusLabel }}</span></td>
            <td>
              <div class="menu-wrapper" x-data="{ open: false }">
                <button type="button" class="menu-btn" @click="open = !open" @click.outside="open = false">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                </button>
                <div class="menu-dropdown" x-show="open" x-cloak x-transition>
                  <button type="button" class="menu-item" @click="verifyOpen = true; loadLoanByTransaksi('{{ $loan->id_transaksi }}'); open = false">View Detail</button>
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5" class="text-center empty-state-cell">No recent loans found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="pagination-wrapper">
    <nav class="pagination-nav">
      @if($loans->onFirstPage())
        <span class="page-btn page-btn--disabled">&laquo;</span>
      @else
        <a href="{{ $loans->previousPageUrl() }}" class="page-btn">&laquo;</a>
      @endif
      @for($i = 1; $i <= $loans->lastPage(); $i++)
        @if($i == $loans->currentPage())
          <span class="page-btn page-btn--active">{{ $i }}</span>
        @else
          <a href="{{ $loans->url($i) }}" class="page-btn">{{ $i }}</a>
        @endif
      @endfor
      @if($loans->hasMorePages())
        <a href="{{ $loans->nextPageUrl() }}" class="page-btn">&raquo;</a>
      @else
        <span class="page-btn page-btn--disabled">&raquo;</span>
      @endif
    </nav>
  </div>

  @include('components.admin.loan-detail-modal')
</div>
@endsection
