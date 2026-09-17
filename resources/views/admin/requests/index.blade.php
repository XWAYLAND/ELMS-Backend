{{-- resources/views/admin/requests/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Recent Loans')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/admin-requests.css') }}">
@endpush

@section('content')

<div class="admin-requests-page" x-data="verifyKode()">

  <div class="page-header">
    <div class="svg-heading-placeholder">
      <img src="{{ asset('svg/admin_icons/requests-page/Recent Loans.svg') }}" alt="Recent Loans" class="page-title-svg">
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  <div class="table-wrapper">
    <table class="requests-table">
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
        @forelse($requests as $loanReq)
          @php
            $statusMap = [
              'menunggu' => ['label' => 'Pending', 'bg' => '#FFFBEB', 'color' => '#D97706'],
              'aktif' => ['label' => 'Loaning', 'bg' => '#FFF3E0', 'color' => '#FF8400'],
              'terlambat' => ['label' => 'Overdue', 'bg' => '#FFEBEE', 'color' => '#FF0034'],
              'menunggu_kembali' => ['label' => 'Return Requested', 'bg' => '#F3E5F5', 'color' => '#8B5CF6'],
              'dikembalikan' => ['label' => 'Returned', 'bg' => '#F3F4F6', 'color' => '#6B7280'],
              'ditolak' => ['label' => 'Rejected', 'bg' => '#FFEBEE', 'color' => '#B71C1C'],
              'disetujui' => ['label' => 'Approved', 'bg' => '#E8F5E9', 'color' => '#00C853'],
            ];
            $s = $statusMap[$loanReq->status] ?? ['label' => ucfirst($loanReq->status), 'bg' => '#F3F4F6', 'color' => '#6B7280'];
          @endphp
          <tr>
            <td>
              <div class="user-info">
                <div class="user-avatar">{{ $loanReq->anggota?->inisial ?? '?' }}</div>
                <div class="user-details">
                  <strong>{{ $loanReq->anggota?->nama_lengkap ?? 'Unknown' }}</strong>
                  <small>{{ $loanReq->anggota?->nis ?? '-' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div class="book-info">
                <img src="{{ $loanReq->buku?->cover_url ?? asset('images/book-placeholder.png') }}" alt="cover" class="book-cover">
                <div class="book-details">
                  <strong>{{ $loanReq->buku?->judul ?? 'Unknown' }}</strong>
                  <small>{{ $loanReq->buku?->penulis ?? '-' }}</small>
                </div>
              </div>
            </td>
            <td>
              <div class="period-info">
                <span>{{ $loanReq->waktu_pengajuan?->format('d M Y') ?? '-' }}</span>
                <span class="period-sep">-</span>
                <span>{{ $loanReq->batas_waktu ? $loanReq->batas_waktu->format('d M Y') : '-' }}</span>
              </div>
            </td>
            <td><span class="status-badge" style="background: {{ $s['bg'] }}; color: {{ $s['color'] }};">{{ $s['label'] }}</span></td>
            <td>
              <div class="menu-wrapper" x-data="{ open: false }">
                <button class="menu-btn" @click="open = !open" @click.outside="open = false">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                </button>
                <div class="menu-dropdown" x-show="open" x-cloak x-transition>
                  <button class="menu-item" @click="verifyOpen = true; loadLoanByTransaksi('{{ $loanReq->id_transaksi }}'); open = false">View Detail</button>
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr class="empty-row"><td colspan="5" class="text-center empty-state-cell">No loan requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <button class="floating-scan-btn" @click="verifyOpen = true; $nextTick(() => $refs.kodeInput?.focus())">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><line x1="7" y1="12" x2="17" y2="12"/></svg>
    Scan Book
  </button>

  @include('components.admin.loan-detail-modal')

  <div class="pagination-wrapper">
    <nav class="pagination-nav">
      @if($requests->onFirstPage())
        <span class="page-btn page-btn--disabled">&laquo;</span>
      @else
        <a href="{{ $requests->previousPageUrl() }}" class="page-btn">&laquo;</a>
      @endif
      @for($i = 1; $i <= $requests->lastPage(); $i++)
        @if($i == $requests->currentPage())
          <span class="page-btn page-btn--active">{{ $i }}</span>
        @else
          <a href="{{ $requests->url($i) }}" class="page-btn">{{ $i }}</a>
        @endif
      @endfor
      @if($requests->hasMorePages())
        <a href="{{ $requests->nextPageUrl() }}" class="page-btn">&raquo;</a>
      @else
        <span class="page-btn page-btn--disabled">&raquo;</span>
      @endif
    </nav>
  </div>

</div>
@endsection
