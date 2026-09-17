{{-- resources/views/student/books/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Books')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/books.css') }}">
@endpush

@section('content')

<div class="books-page">

  {{-- Page heading --}}
  <div class="svg-heading-placeholder">
    <img src="{{ asset('svg/All Collections.svg') }}" alt="All Collections">
  </div>

  {{-- Search + chips + grid (Livewire) --}}
  <livewire:books-filter />

</div>
@endsection
    