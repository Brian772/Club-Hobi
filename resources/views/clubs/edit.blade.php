@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <header class="flex flex-row gap-2 lg:gap-4 items-center justify-start mb-3">
    <a href="{{ route('clubs.show', $club->id) }}" class="text-ink-muted">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="18" viewBox="0 0 16 9">
        <path d="M0 0h16v9H0z" fill="none" />
        <path fill="currentColor" d="M12.5 5h-9c-.28 0-.5-.22-.5-.5s.22-.5.5-.5h9c.28 0 .5.22.5.5s-.22.5-.5.5" />
        <path fill="currentColor"
          d="M6 8.5a.47.47 0 0 1-.35-.15l-3.5-3.5c-.2-.2-.2-.51 0-.71L5.65.65c.2-.2.51-.2.71 0s.2.51 0 .71L3.21 4.51l3.15 3.15c.2.2.2.51 0 .71c-.1.1-.23.15-.35.15Z" />
      </svg>
    </a>
    <h2 class="text-heading-2 flex flex-row items-center gap-2 justify-center text-ink">Edit {{ $club->name }}</h2>
  </header>
  <div class="max-w-4xl">
    <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="flex flex-col lg:flex-row gap-4 w-full">
        <div class="w-full lg:w-1/2 flex flex-col gap-2">
          <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="Club Cover" id="coverPreview"
            class="w-full h-64 rounded-lg object-cover border border-hairline">
        </div>
        <div class="w-full lg:w-1/2 flex flex-col gap-2">
          <input type="file" id="cover" name="cover" accept="image/jpeg, image/png" class="hidden">
          <button type="button" id="editCover" onclick="document.getElementById('cover').click()"
            class="rounded-lg bg-canvas-soft border border-hairline text-body-mid mt-4 lg:mt-0 text-ink w-max py-2 px-4 flex flex-row gap-2 items-center justify-center cursor-pointer hover:bg-primary hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-pencil-icon lucide-pencil">
              <path
                d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
              <path d="m15 5 4 4" />
            </svg>
            Edit Cover
          </button>
          <p class="text-caption text-ink-muted">JPEG/PNG, Max 2MB</p>
          <p id="coverError" class="text-caption text-accent-red"></p>
        </div>
      </div>
      <div class="w-full h-max flex flex-col gap-3 mt-4">
        <div class="w-full flex flex-col gap-2">
          <x-input-label for="name" :value="__('Club Name')" />
          <input type="text" name="name"
            class="w-full rounded-md border border-hairline px-4 py-2 focus:ring-primary focus:border-primary"
            id="name" value="{{ $club->name }}" required>
        </div>
        <div class="w-full flex flex-col gap-2">
          <x-input-label for="category" :value="__('Club Category')" />
          <input type="text" name="category"
            class="w-full lg:w-1/3 rounded-md border border-hairline px-4 py-2 focus:ring-primary focus:border-primary"
            id="category" value="{{ $club->hobby->name }}" readonly>
        </div>
        <div class="w-full flex flex-col gap-2">
          <x-input-label for="description" :value="__('Club Description')" />
          <textarea name="description"
            class="w-full rounded-md border border-hairline px-4 py-2 focus:ring-primary focus:border-primary" id="description"
            rows="4">{{ $club->description }}</textarea>
        </div>

        <button type="submit"
          class="w-max rounded-lg bg-primary text-white py-2 px-4 flex flex-row justify-center items-center gap-2 hover:bg-primary-active cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
            stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-download-icon lucide-download">
            <path d="M12 15V3" />
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <path d="m7 10 5 5 5-5" />
          </svg>
          Update Club
        </button>
      </div>
    </form>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('turbo:load', function() {
        document.getElementById('cover').addEventListener('change', function(e) {
          const file = e.target.files[0];
          const errorEl = document.getElementById('coverError');
          errorEl.classList.add('hidden');

          if (!file) return;

          if (!['image/jpeg', 'image/png'].includes(file.type)) {
            errorEl.textContent = 'Hanya file JPEG dan PNG yang diperbolehkan.';
            errorEl.classList.remove('hidden');
            e.target.value = '';
            return;
          }

          if (file.size > 2 * 1024 * 1024) {
            errorEl.textContent = 'Ukuran file tidak boleh lebih dari 2MB.';
            errorEl.classList.remove('hidden');
            e.target.value = '';
            return;
          }

          const reader = new FileReader();
          reader.onload = function(e) {
            document.getElementById('coverPreview').src = e.target.result;
          };
          reader.readAsDataURL(file);
        });
      })
    </script>
  @endpush
@endsection
