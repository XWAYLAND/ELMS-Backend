{{-- resources/views/student/loans/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Status Peminjaman')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/loan-status.css') }}">
@endpush

@section('content')

<div class="loan-status-page" x-data="{
  activeQr: '',
  activeKode: '',
  cancelTargetId: '',
  returnTargetId: '',
  showQrModal: false,
  showCancelModal: false,
  showReturnModal: false,

  openQr(qrSvg, kode) {
    this.activeQr = qrSvg;
    this.activeKode = kode;
    this.showQrModal = true;
  },

  openCancel(id) {
    this.cancelTargetId = id;
    this.showCancelModal = true;
  },

  openReturn(id) {
    this.returnTargetId = id;
    this.showReturnModal = true;
  }
}">

  {{-- Page Heading SVG --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--loan-status">
    <img src="{{ asset('svg/Your Loan.svg') }}" alt="Your Loan">
  </div>

  {{-- Loan table card --}}
  <div class="loan-table-card">
    {{-- Table header --}}
    <div class="loan-table-header">
      <div class="loan-col-book">Book</div>
      <div class="loan-col-period">Period</div>
      <div class="loan-col-status">Status</div>
    </div>

    {{-- Table rows --}}
    @forelse($loans as $loan)
      <div class="loan-table-row">
        {{-- Book column --}}
        <div class="loan-col-book loan-book-cell">
          <img src="{{ $loan->buku->cover_url }}" alt="{{ $loan->buku->judul }}" class="loan-book-thumb">
          <div>
            <div class="loan-book-info-title">{{ $loan->buku->judul }}</div>
            <div class="loan-book-info-author">{{ $loan->buku->penulis }}</div>
          </div>
        </div>

        {{-- Period column --}}
        <div class="loan-col-period loan-period-cell">
          @if($loan->status === 'menunggu')
            <span class="loan-status-waiting">Menunggu persetujuan</span>
          @elseif($loan->batas_waktu)
            {{ \Carbon\Carbon::parse($loan->waktu_pengajuan)->format('d M Y') }} - {{ \Carbon\Carbon::parse($loan->batas_waktu)->format('d M Y') }}
          @else
            -
          @endif
        </div>

        {{-- Status column --}}
        <div class="loan-col-status loan-status-cell">
          @php
            $statusClass = match($loan->status) {
              'menunggu' => 'loan-badge--pending',
              'aktif' => 'loan-badge--loaning',
              'menunggu_kembali' => 'loan-badge--pending_return',
              'terlambat' => 'loan-badge--late',
              'dikembalikan' => 'loan-badge--returned',
              'ditolak' => 'loan-badge--rejected',
              default => 'loan-badge--pending'
            };
            $statusLabel = match($loan->status) {
              'menunggu' => 'Pending',
              'aktif' => 'Loaning',
              'menunggu_kembali' => 'Pending Return',
              'terlambat' => 'Late',
              'dikembalikan' => 'Returned',
              'ditolak' => 'Rejected',
              default => ucfirst($loan->status)
            };
          @endphp
          <span class="loan-badge {{ $statusClass }}">{{ $statusLabel }}</span>

          @if($loan->status === 'menunggu')
            <div x-data="{ openMenu: false }" @click.outside="openMenu = false" style="position: relative;">
              <template x-ref="qrTemplate_{{ $loop->index }}">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($loan->kode_unik) !!}
              </template>

              <button type="button" class="loan-menu-btn" @click="openMenu = !openMenu" aria-label="Menu">
                ≡
              </button>

              <div x-show="openMenu" x-cloak class="loan-dropdown" x-transition>
                <button type="button"
                        class="loan-dropdown-item"
                        @click="openMenu = false; openQr($refs.qrTemplate_{{ $loop->index }}.innerHTML, '{{ $loan->kode_unik }}')">
                  Show QR Code
                </button>
                <button type="button"
                        class="loan-dropdown-item loan-dropdown-item--danger"
                        @click="openMenu = false; openCancel('{{ $loan->id_transaksi }}')">
                  Cancel Loan
                </button>
              </div>
            </div>
          @elseif(in_array($loan->status, ['aktif', 'terlambat']))
            <div x-data="{ openMenu: false }" @click.outside="openMenu = false" style="position: relative;">
              <button type="button" class="loan-menu-btn" @click="openMenu = !openMenu" aria-label="Menu">
                ≡
              </button>

              <div x-show="openMenu" x-cloak class="loan-dropdown" x-transition>
                <button type="button"
                        class="loan-dropdown-item"
                        @click="openMenu = false; openReturn('{{ $loan->id_transaksi }}')">
                  Return Book
                </button>
              </div>
            </div>
          @endif
        </div>
      </div>
    @empty
      <p class="loan-empty">Belum ada peminjaman</p>
    @endforelse
  </div>

  {{-- Pagination --}}
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

  {{-- Modal 1: QR Code Modal --}}
  <div x-show="showQrModal" x-cloak class="modal-overlay" @keydown.escape.window="showQrModal = false">
    <div class="modal-card" @click.outside="showQrModal = false">
      <button type="button" class="modal-close-btn" @click="showQrModal = false" aria-label="Close">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <h3 class="modal-heading-qr">Your Code :</h3>

      <div class="modal-qr-box" x-html="activeQr"></div>

      <p class="modal-kode-unik" x-text="activeKode"></p>
    </div>
  </div>

  {{-- Modal 2: Cancel Confirmation Modal --}}
  <div x-show="showCancelModal" x-cloak class="modal-overlay" @keydown.escape.window="showCancelModal = false">
    <div class="modal-card" @click.outside="showCancelModal = false">
      <div class="modal-cancel-icon">
        <svg width="120" height="120" viewBox="0 0 168 168" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M84 21C49.35 21 21 49.35 21 84C21 118.65 49.35 147 84 147C118.65 147 147 118.65 147 84C147 49.35 118.65 21 84 21ZM84 35C94.85 35 105 38.85 113.4 44.8L44.8 113.4C38.85 105 35 94.85 35 84C35 57.05 57.05 35 84 35ZM84 133C73.15 133 63 129.15 54.6 123.2L123.2 54.6C129.15 63 133 73.15 133 84C133 110.95 110.95 133 84 133Z" fill="#FF0004"/>
        </svg>
      </div>

      <h3 class="modal-cancel-title">Cancel?</h3>
      <p class="modal-cancel-desc">Are you sure to cancel<br>your loan request?</p>

      <form :action="`/loans/${cancelTargetId}/cancel`" method="POST" class="modal-cancel-actions">
        @csrf
        @method('DELETE')
        <button type="button" class="btn-cancel-close" @click="showCancelModal = false">
          Close
        </button>
        <button type="submit" class="btn-cancel-confirm">
          Cancel
        </button>
      </form>
    </div>
  </div>

  {{-- Modal 3: Return Confirmation Modal --}}
  <div x-show="showReturnModal" x-cloak class="modal-overlay" @keydown.escape.window="showReturnModal = false">
    <div class="modal-card" @click.outside="showReturnModal = false">
      <div class="modal-cancel-icon">
        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="#141DFE" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/>
          <polyline points="16 6 12 2 8 6"/>
          <line x1="12" y1="2" x2="12" y2="15"/>
        </svg>
      </div>

      <h3 class="modal-cancel-title" style="color: var(--color-primary, #141DFE);">Return Book?</h3>
      <p class="modal-cancel-desc">Are you sure you want to return this book?</p>

      <form :action="`/loans/${returnTargetId}/return`" method="POST" class="modal-cancel-actions">
        @csrf
        @method('PATCH')
        <button type="button" class="btn-cancel-close" @click="showReturnModal = false">
          Close
        </button>
        <button type="submit" class="btn-return-confirm">
          Return
        </button>
      </form>
    </div>
  </div>

</div>
@endsection
