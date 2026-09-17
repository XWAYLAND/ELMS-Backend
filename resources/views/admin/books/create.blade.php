@extends('layouts.admin')

@section('title', 'Add New Book')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/pages/admin-books.css') }}">
@endpush

@section('content')
<div class="admin-books-page">

  {{-- SVG Heading --}}
  <div class="svg-heading-placeholder svg-heading-placeholder--create">
    <img src="{{ asset('svg/admin_icons/manage-page/Add New Book.svg') }}" alt="Add New Book">
  </div>

  <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="book-form-card" x-data="{
    preview: null,
    dragOver: false,
    showNewGenreInput: false,
    newGenre: '',
    handleDrop(event) {
      const file = event.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        this.preview = URL.createObjectURL(file);
        this.$refs.file.files = event.dataTransfer.files;
      }
    },
    toggleGenre() {
      this.showNewGenreInput = !this.showNewGenreInput;
      if (!this.showNewGenreInput) {
        this.newGenre = '';
      }
    }
  }">

    @csrf

    {{-- Left: Cover Upload --}}
    <div class="cover-upload-area" :class="{ 'has-cover': preview, 'is-dragover': dragOver }"
         @click="$refs.file.click()"
         @drop.prevent="handleDrop($event); dragOver = false"
         @dragover.prevent="dragOver = true"
         @dragleave.prevent="dragOver = false">
      <template x-if="!preview">
        <div class="cover-upload-placeholder">
          <!-- TODO: user will insert image icon SVG manually -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <circle cx="8.5" cy="8.5" r="1.5"></circle>
            <polyline points="21 15 16 10 5 21"></polyline>
          </svg>
          <div>Add Book Cover</div>
          <small>Drop an image or browse it from your computer</small>
        </div>
      </template>
      <img x-show="preview" :src="preview" alt="Cover preview">
      <input type="file" x-ref="file" class="hidden" accept="image/*" name="cover_file" @change="preview = URL.createObjectURL($event.target.files[0]); dragOver = false">
    </div>

    {{-- Right: Form Fields --}}
    <div class="form-fields">

      <h2>Book Detail</h2>

      {{-- Title --}}
      <div class="form-field">
        <label for="judul">Title</label>
        <input class="form-input-pill" type="text" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Insert book title..." required>
      </div>

      {{-- Author + Genre --}}
      <div class="form-row-2">
        <div>
          <label for="penulis">Author</label>
          <input class="form-input-pill" type="text" id="penulis" name="penulis" value="{{ old('penulis') }}" placeholder="Insert author name..." required>
        </div>
        <div>
          <label for="id_jenis">Genre</label>
          <div class="flex items-center gap-2">
            <select required x-show="!showNewGenreInput" class="form-input-pill" id="genre_selector" x-model="newGenre" @change="if (newGenre === '__custom__') { showNewGenreInput = true; newGenre = ''; }" x-bind:style="showNewGenreInput ? 'display:none' : ''" style="flex: 1;">
              <option value="" disabled selected>Choose book genre...</option>
              @foreach($jenisList as $j)
                <option value="{{ $j->nama_jenis }}">{{ $j->nama_jenis }}</option>
              @endforeach
              <option value="__custom__">+ Add new genre</option>
            </select>
            <div x-show="showNewGenreInput || '{{ empty($jenisList) }}'" class="relative flex-1">
              <input x-init="$watch('showNewGenreInput', value => { if(value) $el.focus() })" class="form-input-pill w-full pr-10" type="text" x-model="newGenre" placeholder="Enter new genre..." required>
              <button type="button" @click="showNewGenreInput = false; newGenre = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500" title="Cancel adding new genre" x-show="showNewGenreInput && '{{ !empty($jenisList) }}'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
              </button>
            </div>
            <input type="hidden" name="id_jenis" x-bind:value="newGenre">
          </div>
        </div>
      </div>

      {{-- Edition + Publisher --}}
      <div class="form-row-2">
        <div>
          <label for="edisi">Edition</label>
          <input class="form-input-pill" type="text" id="edisi" name="edisi" value="{{ old('edisi') }}" placeholder="Insert edition...">
        </div>
        <div>
          <label for="penerbit">Publisher</label>
          <input class="form-input-pill" type="text" id="penerbit" name="penerbit" value="{{ old('penerbit') }}" placeholder="Insert publisher name..." required>
        </div>
      </div>

      {{-- Physical Description + ISBN --}}
      <div class="form-row-2">
        <div>
          <label for="deskripsi_fisik">Physical Description</label>
          <input class="form-input-pill" type="text" id="deskripsi_fisik" name="deskripsi_fisik" value="{{ old('deskripsi_fisik') }}" placeholder="Insert physical description...">
        </div>
        <div>
          <label for="isbn">ISBN</label>
          <input class="form-input-pill" type="text" id="isbn" name="isbn" value="{{ old('isbn') }}" placeholder="Insert ISBN..." required>
        </div>
      </div>

      {{-- Language --}}
      <div class="form-field">
        <label for="bahasa">Language</label>
        <input class="form-input-pill" type="text" id="bahasa" name="bahasa" value="{{ old('bahasa') }}" placeholder="Insert language...">
      </div>

      <button type="submit" class="btn-submit-book">Add Book</button>
    </div>
  </form>
</div>
@endsection