@extends('layouts.app')

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
    <h2 class="text-heading-2 text-ink-secondary">Ajukan Klub</h2>
  </header>

  <main class="max-w-4xl">
    <form action="{{ route('clubs.request.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="flex flex-col lg:flex-row gap-4 w-full">
        <div class="w-full lg:w-1/2 flex flex-col gap-2">
          <img src="data:," alt="Club Cover" id="coverPreview"
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
            upload cover
          </button>
          <p class="text-caption text-ink-muted">JPEG/PNG, Max 2MB</p>
          <p id="coverError" class="text-caption text-accent-red"></p>
        </div>
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
  </main>

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
