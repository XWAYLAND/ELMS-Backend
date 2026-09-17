{{-- resources/views/student/loans/qr.blade.php --}}
@extends('layouts.app')

@section('title', 'QR Code Peminjaman')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/qr.css') }}">
@endpush

@section('content')
<div class="qr-page">
  <h1 class="qr-page-title">Pengajuan Berhasil</h1>
  <p class="qr-page-description">Kode peminjaman Anda telah dibuat</p>

  <div class="qr-code">
    {!! $qr !!}
  </div>

  <p class="qr-kode">{{ $peminjaman->kode_unik }}</p>

  <div class="qr-info">
    <p><strong>Judul Buku:</strong> {{ $peminjaman->buku->judul }}</p>
    <p><strong>Durasi Pinjam:</strong> {{ $peminjaman->durasi_hari }} hari</p>
    <p><strong>Status:</strong> {{ ucfirst($peminjaman->status) }}</p>
    <p><strong>Waktu Pengajuan:</strong> {{ $peminjaman->waktu_pengajuan->format('d/m/Y H:i') }}</p>
  </div>

  <p class="qr-instruction">Tunjukkan kode ini ke petugas perpustakaan saat mengambil buku.</p>

  <a href="{{ route('loans.index') }}" class="btn-back">Lihat Status Peminjaman</a>
</div>
@endsection
