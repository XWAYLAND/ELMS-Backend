@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/admin-books.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages/admin-users.css') }}">
@endpush

@section('content')
<div class="admin-users-page">

  {{-- Heading --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--create" style="padding: var(--space-xl) var(--space-lg) 0;">
    <span class="users-heading-text">Edit User</span>
  </div>

  <div class="user-form-card"
       x-data="{ role: '{{ $role }}' }"
  >

    @if($errors->any())
    <div class="form-errors-box">
      <ul>
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('admin.users.update', $role === 'student' ? $user->nis : $user->id_pegawai) }}"
          method="POST" class="user-form-fields">
      @csrf
      @method('PUT')
      <input type="hidden" name="role" value="{{ $role }}">

      {{-- Name --}}
      <div class="form-field">
        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" name="name" class="form-input-pill"
               placeholder="Masukkan nama lengkap"
               value="{{ old('name', $role === 'student' ? $user->nama_lengkap : $user->nama) }}"
               required>
      </div>

      {{-- Role (read-only display) --}}
      <div class="form-field">
        <label>Role</label>
        <input type="text" class="form-input-pill"
               value="{{ $role === 'student' ? 'Student' : 'Staff' }}"
               readonly style="background: #F9FAFB; color: var(--color-muted); cursor: not-allowed;">
      </div>

      {{-- Student fields --}}
      @if($role === 'student')
        <div class="form-row-2">
          <div>
            <label>NIS</label>
            <input type="text" class="form-input-pill"
                   value="{{ $user->nis }}"
                   readonly style="background: #F9FAFB; color: var(--color-muted); cursor: not-allowed;">
          </div>
          <div>
            <label for="kelas">Kelas</label>
            <input type="text" id="kelas" name="kelas" class="form-input-pill"
                   placeholder="Contoh: XII IPA 1"
                   value="{{ old('kelas', $user->kelas) }}" required>
          </div>
        </div>
      @endif

      {{-- Staff fields --}}
      @if($role === 'staff')
        <div class="form-row-2">
          <div>
            <label>ID Pegawai</label>
            <input type="text" class="form-input-pill"
                   value="{{ $user->id_pegawai }}"
                   readonly style="background: #F9FAFB; color: var(--color-muted); cursor: not-allowed;">
          </div>
          <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-input-pill"
                   placeholder="Email (opsional)"
                   value="{{ old('email', $user->email) }}">
          </div>
        </div>
      @endif

      {{-- Password --}}
      <div class="form-field">
        <label for="password">
          Password
          <span class="field-hint">(kosongkan untuk tidak mengubah password)</span>
        </label>
        <input type="password" id="password" name="password" class="form-input-pill"
               placeholder="Biarkan kosong jika tidak ingin mengubah" autocomplete="new-password">
      </div>

      <div class="form-actions">
        <a href="{{ route('admin.users.index') }}" class="btn-cancel-user">Batal</a>
        <button type="submit" class="btn-submit-user">Simpan Perubahan</button>
      </div>
    </form>
  </div>

</div>
@endsection
