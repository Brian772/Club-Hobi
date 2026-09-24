@extends('layouts.app')

@section('title', 'Orbii | Create Post')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/post.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
  @if ($user->status === 'suspended')
    <div
      class="w-full flex flex-col justify-start items-center border border-accent-yellow rounded-lg bg-accent-yellow/10 p-6 lg:p-8">
      <h1 class="text-title text-yellow-900">Account Restricted</h1>
      <p class="text-body-mid text-yellow-700 text-center mt-1">You cannot create posts while your account is suspended.
      </p>
    </div>
  @endif
  <div class="post-page">
    <div class="post-page-header">
      <h1 class="header-title">
        <a href="{{ route('posts.index') }}">
          <svg class="back-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
        </a>
        Buat Postingan
      </h1>
      <p>Bagikan aktivitas atau konten ke club yang kamu ikuti.</p>
    </div>

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
      @csrf

      <div class="form-group">
        <label for="club_id">Pilih Club</label>
        <select name="club_id" id="club_id" required>
          <option value="">-- Pilih Club --</option>
          @foreach ($clubs as $club)
            <option value="{{ $club->id }}" @selected(old('club_id') == $club->id)>
              {{ $club->name }}
            </option>
          @endforeach
        </select>
        @error('club_id')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="title">Judul Postingan</label>
        <input type="text" name="title" id="title" value="{{ old('title') }}"
          placeholder="Masukkan judul postingan" required>
        @error('title')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label for="content">Isi Postingan</label>
        <textarea name="content" id="content" placeholder="Apa yang ingin kamu bagikan?" required>{{ old('content') }}</textarea>
        @error('content')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-group">
        <label>Lampiran Media</label>

        {{-- Input file tersembunyi untuk FORM SUBMISSION (dikirim ke server) --}}
        <input type="file" name="media[]" id="mediaInput" multiple class="hidden">

        {{-- Input file tersembunyi untuk DIALOG PICKER (tidak punya name, tidak dikirim) --}}
        <input type="file" id="mediaPicker" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" multiple class="hidden"
          onchange="addFiles(this.files)">

        <div class="flex items-center gap-3 overflow-x-auto pb-2" id="mediaContainer">

          <div id="mediaAddButton" onclick="document.getElementById('mediaPicker').click()"
            class="w-24 h-24 shrink-0 rounded-xl border-2 border-dashed border-neutral-300 hover:border-blue-500 bg-neutral-50 hover:bg-blue-50/50 flex flex-col items-center justify-center cursor-pointer transition-colors group">
            <i class="fa-solid fa-plus text-xl text-neutral-400 group-hover:text-blue-600 transition-colors"></i>
            <span class="text-[10px] font-medium text-neutral-500 group-hover:text-blue-600 mt-1">Tambah
              File</span>
          </div>
        </div>

        @error('media')
          <span class="form-error">{{ $message }}</span>
        @enderror
      </div>

      <div class="form-actions">
        <a href="{{ route('posts.index') }}" class="cancel-button">Batal</a>
        <button type="submit" class="publish-button">
          <i class="fa-solid fa-paper-plane"></i> Publikasikan Postingan
        </button>
      </div>
    </form>
  </div>

  <script>
    var selectedFiles = [];

    // Bersihkan data saat halaman dimuat (termasuk dari cache browser / tombol back)
    document.addEventListener("DOMContentLoaded", function() {
      resetFormMedia();
    });

    window.addEventListener('pageshow', function() {
      resetFormMedia();
    });

    function resetFormMedia() {
      selectedFiles = [];
      const picker = document.getElementById('mediaPicker');
      if (picker) picker.value = '';
      const input = document.getElementById('mediaInput');
      if (input) input.value = '';

      renderPreviews();
    }

    // Pastikan file tersinkron ke form input SEBELUM dikirim ke server
    document.getElementById('postForm').addEventListener('submit', function() {
      updateFileInput();
    });

    function addFiles(files) {
      if (!files || files.length === 0) return;

      Array.from(files).forEach(file => {
        selectedFiles.push(file);
      });

      renderPreviews();
      updateFileInput();

      // Reset PICKER (bukan mediaInput) agar onchange terpanggil jika memilih file yang sama
      document.getElementById('mediaPicker').value = '';
    }

    function removeFile(index) {
      selectedFiles.splice(index, 1);
      renderPreviews();
      updateFileInput();
    }

    function renderPreviews() {
      const container = document.getElementById('mediaContainer');
      if (!container) return;

      const addButton = document.getElementById('mediaAddButton');

      // Bersihkan kontainer
      container.innerHTML = '';

      selectedFiles.forEach((file, index) => {
        const wrapper = document.createElement('div');
        wrapper.className =
          'relative w-24 h-24 shrink-0 rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100 group';

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className =
          'absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer hover:bg-red-600';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.onclick = (e) => {
          e.stopPropagation();
          removeFile(index);
        };

        const content = document.createElement('div');
        content.className = 'w-full h-full flex flex-col items-center justify-center p-1 text-center';

        if (file.type.startsWith('image/')) {
          const img = document.createElement('img');
          img.src = URL.createObjectURL(file);
          img.className = 'w-full h-full object-cover';
          wrapper.appendChild(img);
        } else if (file.type.startsWith('video/')) {
          content.innerHTML = `
                          <i class="fa-solid fa-file-video text-2xl text-blue-500 mb-1"></i>
                          <span class="text-[9px] text-neutral-600 truncate w-full px-1">${file.name}</span>
                      `;
          wrapper.appendChild(content);
        } else if (file.type.startsWith('audio/')) {
          content.innerHTML = `
                          <i class="fa-solid fa-file-audio text-2xl text-purple-500 mb-1"></i>
                          <span class="text-[9px] text-neutral-600 truncate w-full px-1">${file.name}</span>
                      `;
          wrapper.appendChild(content);
        } else {
          content.innerHTML = `
                          <i class="fa-solid fa-file-lines text-2xl text-amber-500 mb-1"></i>
                          <span class="text-[9px] text-neutral-600 truncate w-full px-1">${file.name}</span>
                      `;
          wrapper.appendChild(content);
        }

        wrapper.appendChild(deleteBtn);
        container.appendChild(wrapper);
      });

      if (addButton) {
        container.appendChild(addButton);
      }
    }

    function updateFileInput() {
      const input = document.getElementById('mediaInput');
      if (!input) return;
      const dataTransfer = new DataTransfer();

      selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
      });

      input.files = dataTransfer.files;
    }

    resetFormMedia();
  </script>
@endsection
