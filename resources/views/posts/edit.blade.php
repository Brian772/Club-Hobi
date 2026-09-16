@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')
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
                Edit Postingan
            </h1>
            <p>Perbarui detail postingan kamu.</p>
        </div>

        <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Input tersembunyi untuk ID media lama yang dihapus --}}
            <div id="deletedMediaInputs"></div>

            <div class="form-group">
                <label for="club_id">Pilih Club</label>
                <select name="club_id" id="club_id" required>
                    <option value="">-- Pilih Club --</option>
                    @foreach ($clubs as $club)
                        <option value="{{ $club->id }}" @selected(old('club_id', $post->club_id) == $club->id)>
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
                <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required>
                @error('title')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="content">Isi Postingan</label>
                <textarea name="content" id="content" required>{{ old('content', $post->content) }}</textarea>
                @error('content')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Lampiran Media</label>

                {{-- Input file form yang dikirim ke server --}}
                <input type="file" name="media[]" id="mediaInput" multiple class="hidden">

                {{-- Input file dialog picker sementara --}}
                <input type="file" id="mediaPicker" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" multiple
                    class="hidden" onchange="addNewFiles(this.files)">

                {{-- Container Horizontal Blok Media --}}
                <div class="flex items-center gap-3 overflow-x-auto pb-2" id="mediaContainer">

                    {{-- Render Media Lama (Mendukung $post->media maupun $post->media_url) --}}
                    @php
                        $existingMediaList = collect();
                        if (isset($post->media) && count($post->media) > 0) {
                            $existingMediaList = $post->media;
                        } elseif (!empty($post->media_url)) {
                            $existingMediaList = collect([(object) ['id' => 'single', 'file_path' => $post->media_url]]);
                        }
                    @endphp

                    @foreach ($existingMediaList as $item)
                        @php
                            $filePath = $item->file_path ?? $item;
                            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            $itemUrl = asset('storage/' . $filePath);
                            $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                            $isVideo = in_array($ext, ['mp4', 'mov', 'webm']);
                            $isAudio = in_array($ext, ['mp3', 'wav', 'ogg']);
                        @endphp
                        <div class="existing-media-block relative w-24 h-24 shrink-0 rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100 group"
                            data-id="{{ $item->id ?? '' }}">

                            {{-- Tombol Hapus (X) saat hover --}}
                            <button type="button" onclick="removeExistingMedia('{{ $item->id ?? '' }}', this)"
                                class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer hover:bg-red-600">
                                &times;
                            </button>

                            @if ($isImg)
                                <img src="{{ $itemUrl }}" class="w-full h-full object-cover">
                            @elseif ($isVideo)
                                <div class="w-full h-full flex flex-col items-center justify-center p-1 text-center">
                                    <i class="fa-solid fa-file-video text-2xl text-blue-500 mb-1"></i>
                                    <span class="text-[9px] text-neutral-600 truncate w-full px-1">{{ basename($filePath) }}</span>
                                </div>
                            @elseif ($isAudio)
                                <div class="w-full h-full flex flex-col items-center justify-center p-1 text-center">
                                    <i class="fa-solid fa-file-audio text-2xl text-purple-500 mb-1"></i>
                                    <span class="text-[9px] text-neutral-600 truncate w-full px-1">{{ basename($filePath) }}</span>
                                </div>
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center p-1 text-center">
                                    <i class="fa-solid fa-file-lines text-2xl text-amber-500 mb-1"></i>
                                    <span class="text-[9px] text-neutral-600 truncate w-full px-1">{{ basename($filePath) }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    {{-- Tombol Tambah File (+ di Paling Kanan) --}}
                    <div onclick="document.getElementById('mediaPicker').click()"
                        class="w-24 h-24 shrink-0 rounded-xl border-2 border-dashed border-neutral-300 hover:border-blue-500 bg-neutral-50 hover:bg-blue-50/50 flex flex-col items-center justify-center cursor-pointer transition-colors group">
                        <i
                            class="fa-solid fa-plus text-xl text-neutral-400 group-hover:text-blue-600 transition-colors"></i>
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
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        let newFiles = [];

        function removeExistingMedia(id, btnElement) {
            if (id && id !== 'single') {
                const inputContainer = document.getElementById('deletedMediaInputs');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_media[]';
                input.value = id;
                inputContainer.appendChild(input);
            }
            btnElement.closest('.existing-media-block').remove();
        }

        function addNewFiles(files) {
            if (!files || files.length === 0) return;

            Array.from(files).forEach(file => {
                newFiles.push(file);
            });

            renderNewPreviews();
            updateNewFileInput();

            // Reset PICKER (bukan mediaInput) agar onchange terpanggil jika memilih file yang sama
            const picker = document.getElementById('mediaPicker');
            if (picker) picker.value = '';
        }

        function removeNewFile(index) {
            newFiles.splice(index, 1);
            renderNewPreviews();
            updateNewFileInput();
        }

        function renderNewPreviews() {
            const container = document.getElementById('mediaContainer');

            document.querySelectorAll('.new-media-block').forEach(el => el.remove());

            const addButton = container.lastElementChild;

            newFiles.forEach((file, index) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'new-media-block relative w-24 h-24 shrink-0 rounded-xl overflow-hidden border border-neutral-200 bg-neutral-100 group';

                const deleteBtn = document.createElement('button');
                deleteBtn.type = 'button';
                deleteBtn.className = 'absolute top-1 right-1 w-6 h-6 rounded-full bg-black/70 text-white text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer hover:bg-red-600';
                deleteBtn.innerHTML = '&times;';
                deleteBtn.onclick = (e) => {
                    e.stopPropagation();
                    removeNewFile(index);
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
                container.insertBefore(wrapper, addButton);
            });
        }

        function updateNewFileInput() {
            const input = document.getElementById('mediaInput');
            const dataTransfer = new DataTransfer();

            newFiles.forEach(file => {
                dataTransfer.items.add(file);
            });

            input.files = dataTransfer.files;
        }
    </script>
@endsection