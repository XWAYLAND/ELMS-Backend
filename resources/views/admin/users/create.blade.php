@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/admin-books.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages/admin-users.css') }}">
@endpush

@section('content')
<div class="admin-users-page"
     x-data="{
       tab: 'single',
       role: 'student',
       dragover: false,
       fileName: '',
       handleDrop(e) {
         e.preventDefault();
         this.dragover = false;
         const file = e.dataTransfer.files[0];
         if (file) {
           this.fileName = file.name;
           const dt = new DataTransfer();
           dt.items.add(file);
           document.getElementById('bulk-file-input').files = dt.files;
         }
       }
     }"
>

  {{-- Heading --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--create" style="padding: var(--space-xl) var(--space-lg) 0;">
    <span class="users-heading-text">Add New User</span>
  </div>

  {{-- Tab Toggle --}}
  <div class="user-tabs-wrapper">
    <button type="button" class="user-tab-btn" :class="{ active: tab === 'single' }" @click="tab = 'single'">
      Single Add
    </button>
    <button type="button" class="user-tab-btn" :class="{ active: tab === 'bulk' }" @click="tab = 'bulk'">
      Bulk Add
    </button>
  </div>

  {{-- ── Single Add Form ── --}}
  <div x-show="tab === 'single'" x-cloak>
    <div class="user-form-card">

      @if($errors->any())
      <div class="form-errors-box">
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form action="{{ route('admin.users.storeSingle') }}" method="POST" class="user-form-fields">
        @csrf

        {{-- Name --}}
        <div class="form-field">
          <label for="name">Nama Lengkap</label>
          <input type="text" id="name" name="name" class="form-input-pill"
                 placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
        </div>

        {{-- Role --}}
        <div class="form-field">
          <label for="role">Role</label>
          <select id="role" name="role" class="form-input-pill"
                  x-model="role" @change="role = $event.target.value">
            <option value="student" {{ old('role', 'student') === 'student' ? 'selected' : '' }}>Student</option>
            <option value="staff"   {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
          </select>
        </div>

        {{-- Student fields --}}
        <div x-show="role === 'student'">
          <div class="form-row-2">
            <div>
              <label for="nis">NIS</label>
              <input type="text" id="nis" name="nis" class="form-input-pill"
                     placeholder="Nomor Induk Siswa" value="{{ old('nis') }}"
                     :required="role === 'student'">
            </div>
            <div>
              <label for="kelas">Kelas</label>
              <input type="text" id="kelas" name="kelas" class="form-input-pill"
                     placeholder="Contoh: XII IPA 1" value="{{ old('kelas') }}"
                     :required="role === 'student'">
            </div>
          </div>
        </div>

        {{-- Staff fields --}}
        <div x-show="role === 'staff'">
          <div class="form-row-2">
            <div>
              <label for="id_pegawai">ID Pegawai</label>
              <input type="text" id="id_pegawai" name="id_pegawai" class="form-input-pill"
                     placeholder="Contoh: PGW001" value="{{ old('id_pegawai') }}"
                     :required="role === 'staff'">
            </div>
            <div>
              <label for="email">Email</label>
              <input type="email" id="email" name="email" class="form-input-pill"
                     placeholder="Email (opsional)" value="{{ old('email') }}">
            </div>
          </div>
        </div>

        {{-- Password --}}
        <div class="form-field">
          <label for="password">
            Password
            <span class="field-hint">(kosongkan untuk auto-generate dari NIS / ID Pegawai)</span>
          </label>
          <input type="password" id="password" name="password" class="form-input-pill"
                 placeholder="Biarkan kosong untuk password otomatis" autocomplete="new-password">
        </div>

        <div class="form-actions">
          <a href="{{ route('admin.users.index') }}" class="btn-cancel-user">Batal</a>
          <button type="submit" class="btn-submit-user">Tambah Pengguna</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ── Bulk Add Form ── --}}
  <div x-show="tab === 'bulk'" x-cloak>
    <div class="user-form-card">

      @if($errors->hasBag('default'))
      <div class="form-errors-box">
        <ul>
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      <form action="{{ route('admin.users.storeBulk') }}" method="POST" enctype="multipart/form-data" class="user-form-fields">
        @csrf

        <p class="bulk-desc">
          Unggah file Excel (.xlsx, .xls) atau CSV dengan kolom:
          <code>role, nis, nama_lengkap, kelas, id_pegawai, nama, email</code>.
          Password akan di-generate otomatis dari NIS / ID Pegawai.
        </p>

        {{-- Drag & Drop Zone --}}
        <div
          class="dropzone"
          :class="{ 'dropzone--over': dragover }"
          @dragover.prevent="dragover = true"
          @dragleave.prevent="dragover = false"
          @drop="handleDrop($event)"
          @click="document.getElementById('bulk-file-input').click()"
        >
          <input
            type="file"
            id="bulk-file-input"
            name="file"
            accept=".xlsx,.xls,.csv"
            class="dropzone-input"
            @change="fileName = $event.target.files[0]?.name ?? ''"
          >
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
          <p x-text="fileName ? fileName : 'Drag & drop file di sini, atau klik untuk memilih'"></p>
          <small>Format: .xlsx, .xls, .csv &bull; Maks 10 MB</small>
        </div>

        {{-- Download Template --}}
        <div class="bulk-template-row">
          <a href="{{ route('admin.users.template') }}" class="btn-download-template" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download Template Excel
          </a>
        </div>

        <div class="form-actions">
          <a href="{{ route('admin.users.index') }}" class="btn-cancel-user">Batal</a>
          <button type="submit" class="btn-submit-user" :disabled="!fileName">Import Pengguna</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
