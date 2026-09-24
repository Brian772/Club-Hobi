@extends('layouts.app')

@section('title', 'Orbii | Request Club')

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-6">
    <a href="{{ route('clubs.index') }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-title lg:text-heading-2 text-ink-secondary">Ajukan Klub</h2>
  </header>

  <main class="max-w-4xl" x-data="{ submitForm: false }">
    <form action="{{ route('clubs.request.store') }}" method="POST" enctype="multipart/form-data" x-ref="clubForm"
      @submit.prevent="submitForm = true">
      @csrf
      <div class="flex flex-col lg:flex-row gap-4 w-full" x-data="{
          isDragging: false,
          preview: null,
          error: '',
          handleFile(file) {
              this.error = '';
              if (!file) return;
              if (!['image/jpeg', 'image/png'].includes(file.type)) {
                  this.error = 'Hanya file JPEG, PNG, dan JPG yang diperbolehkan.';
                  return;
              }
              if (file.size > 2 * 2048 * 1024) {
                  this.error = 'Ukuran file tidak boleh lebih dari 2MB.';
                  return;
              }
              const dt = new DataTransfer();
              dt.items.add(file);
              this.$refs.cover.files = dt.files;
      
              const reader = new FileReader();
              reader.onload = (e) => this.preview = e.target.result;
              reader.readAsDataURL(file);
          }
      }">
        <input type="file" x-ref="cover" name="cover" accept="image/jpeg, image/png" class="hidden"
          @change="handleFile($event.target.files[0])">

        <div @click="$refs.cover.click()" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
          @drop.prevent="isDragging = false; handleFile($event.dataTransfer.files[0])"
          :class="isDragging ? 'border-primary bg-primary/10' : 'border-hairline bg-white'"
          class="relative w-full lg:w-lg h-64 border-dashed rounded-lg flex flex-col items-center justify-center cursor-pointer transition-colors duration-300">

          <template x-if="preview">
            <img :src="preview" alt="Preview" draggable="false"
              class="absolute inset-0 w-full h-full object-cover rounded-lg">
          </template>

          <template x-if="!preview">
            <div
              class="flex flex-col items-center w-full h-full justify-center gap-2 text-ink-muted pointer-events-none border-dashed border-2 border-hairline rounded-lg p-4 hover:border-primary hover:bg-primary/10 transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-images preview-icon text-primary">
                <path d="m22 11-1.296-1.296a2.4 2.4 0 0 0-3.408 0L11 16" />
                <path d="M4 8a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2" />
                <circle cx="13" cy="7" r="1" fill="currentColor" />
                <rect x="8" y="2" width="14" height="14" rx="2" />
              </svg>
              <p class="text-body-mid text-center">Klik atau seret file ke sini untuk mengunggah</p>
              <p class="text-caption text-ink-muted">JPEG/PNG, Maks 2MB.</p>
            </div>
          </template>

          <template x-if="preview">
            <button type="button" @click.prevent="preview = null; $refs.cover.value = ''"
              class="absolute cursor-pointer z-10 top-2 right-2 bg-white rounded-full p-1 shadow hover:bg-gray-100 transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18M6 6l12 12" />
              </svg>
            </button>
          </template>
        </div>
        <p class="text-caption text-accent-red" x-show="error" x-text="error"></p>

      </div>
      <div class="flex flex-col gap-2 mb-4">
        <label for="name" class="text-body-mid">Nama Klub <span class="text-accent-red">*</span></label>
        <input type="text" id="name" name="name" placeholder="Klub..."
          class="rounded-lg px-4 py-2 border border-hairline focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          required>
      </div>
      <div class="flex flex-col gap-2 mb-4">
        <label for="description" class="text-body-mid">Deskripsi Klub</label>
        <textarea id="description" name="description" placeholder="Deskripsi klub..."
          class="rounded-lg px-4 py-2 border border-hairline focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
      </div>
      <div class="flex flex-col gap-2 mb-4">
        <label for="hobby" class="text-body-mid">Kategori Klub <span class="text-accent-red">*</span></label>

        <div class="relative">
          <select id="hobby" name="hobby_id" required
            class="w-1/3 appearance-none rounded-lg px-4 py-2 pr-10 border border-hairline bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            <option value="" disabled {{ old('hobby_id') ? '' : 'selected' }}>Pilih Kategori</option>
            @foreach ($hobbies as $hobby)
              <option value="{{ $hobby->id }}" @selected(old('hobby_id') == $hobby->id)>
                {{ $hobby->name }}
              </option>
            @endforeach
          </select>
        </div>

        @error('hobby_id')
          <p class="text-accent-red text-sm">{{ $message }}</p>
        @enderror
      </div>
      <div class="flex flex-col gap-2 mb-4">
        <label for="reason" class="text-body-mid">Alasan Pengajuan <span class="text-accent-red">*</span></label>
        <textarea id="reason" name="reason" placeholder="Deskripsikan alasan pengajuan anda..."
          class="rounded-lg px-4 py-2 border border-hairline focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
          required></textarea>
      </div>

      <button type="submit"
        class="bg-primary/10 text-primary hover:text-white rounded-md px-4 py-2 hover:bg-primary">Ajukan Klub</button>
    </form>

    {{-- Confirm Modal --}}
    <div x-show="submitForm" x-cloak @keydown.escape.window="submitForm = false"
      class="fixed flex items-center justify-center inset-0 z-50">
      <div @click="submitForm = false" class="fixed flex items-center justify-center inset-0 z-40 bg-black/30">
      </div>
      <div class="fixed p-6 h-max rounded-lg z-50 w-max bg-canvas border border-hairline overflow-hidden max-w-md">
        <div class="flex flex-col gap-2 mb-4">
          <h3 class="text-title mb-4 text-ink">
            Konfirmasi Ajuan Klub
          </h3>
          <p class="text-body-mid mb-4 text-ink">Apakah anda yakin ingin mengajukan klub ini? Pastikan data yang anda
            masukkan sudah benar.</p>
        </div>
        <div class="flex flex-row justify-end gap-2">
          <button type="button" @click="submitForm = false; $refs.clubForm.submit()"
            class="flex flex-row gap-2 items-center px-4 py-2 text-caption bg-primary/10 rounded w-max text-primary hover:text-white hover:bg-primary">
            Ya, Ajukan
          </button>
          <button type="button"
            class="bg-gray-200 border border-hairline rounded text-caption px-4 py-2 text-ink hover:bg-gray-300"
            @click="submitForm = false">Cancel</button>
        </div>
      </div>
    </div>
  </main>
@endsection
