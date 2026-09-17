{{-- resources/views/student/loans/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Pinjam Buku')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/loan-form.css') }}">
@endpush

@section('content')

<div class="loan-form-page">

  {{-- Page Heading SVG --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--loan-create">
    <img src="{{ asset('svg/Loan Information.svg') }}" alt="Loan Information">
  </div>

  {{-- Main card with Form + Book Preview --}}
  <div class="loan-card">

    {{-- Form side --}}
    <div class="loan-form-side">
      <h2 class="loan-form-title">Borrower Information</h2>

      <form method="POST" action="{{ route('loans.store') }}" id="borrow-form">
        @csrf
        <input type="hidden" name="isbn" value="{{ $book->isbn }}">

        {{-- Name field --}}
        <div class="loan-field">
          <label class="loan-label" for="nama_lengkap">Name</label>
          <input type="text" class="loan-input-readonly" id="nama_lengkap" value="{{ $user->nama_lengkap }}" readonly>
        </div>

        {{-- Class + NIS row --}}
        <div class="loan-row">
          <div class="loan-field">
            <label class="loan-label" for="kelas">Class</label>
            <input type="text" class="loan-input-readonly" id="kelas" value="{{ $user->kelas }}" readonly>
          </div>
          <div class="loan-field">
            <label class="loan-label" for="nis">NIS</label>
            <input type="text" class="loan-input-readonly" id="nis" value="{{ $user->nis }}" readonly>
          </div>
        </div>

        {{-- Loan Duration --}}
        <label class="loan-duration-label" for="durasi">Loan Duration</label>
        <div class="loan-select-wrapper">
          <select class="loan-select" id="durasi" name="durasi_hari">
            <option value="3">3 Days</option>
            <option value="7" selected>7 Days</option>
            <option value="14">14 Days</option>
          </select>
          <span class="loan-select-chevron">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </span>
        </div>
        <p class="loan-date-preview" id="date-preview"></p>

        {{-- Submit button --}}
        <button type="submit" class="btn-borrow">Borrow</button>
      </form>
    </div>

    {{-- Book preview side --}}
    <div class="loan-book-side">
      <img src="{{ $book->cover_url }}" alt="{{ $book->judul }}" class="loan-book-cover">
      <h3 class="loan-book-title">{{ $book->judul }}</h3>
      <p class="loan-book-author">{{ $book->penulis }}</p>
    </div>

  </div>

  {{-- QR Code Modal (State 2) --}}
  @if($showQr ?? false)
  <div x-data="{ open: true }" x-show="open" x-cloak>
    <div class="modal-overlay">
      <div class="modal-card">
        <a href="{{ route('loans.index') }}" class="modal-close-btn" aria-label="Close">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </a>
        <h3 class="modal-heading-qr">Your Code :</h3>
        <div class="modal-qr-box">
          {!! $qr !!}
        </div>
        <p class="modal-kode-unik">{{ $peminjaman->kode_unik }}</p>
        <p class="modal-kode-expire">Berlaku sampai: <strong>{{ $peminjaman->kode_unik_expires_at->format('d M Y H:i') }}</strong> WIB (24 jam)</p>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection

@push('scripts')
<script>
  // Loan duration date preview
  document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('durasi');
    const preview = document.getElementById('date-preview');

    function updatePreview() {
      const days = parseInt(select.value);
      const start = new Date();
      const end = new Date();
      end.setDate(end.getDate() + days);
      const fmt = d => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
      preview.textContent = fmt(start) + ' - ' + fmt(end);
    }

    if (select && preview) {
      updatePreview();
      select.addEventListener('change', updatePreview);
    }
  });
</script>
@endpush
