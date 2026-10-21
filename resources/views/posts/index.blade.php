@extends('layouts.app')

@section('title', 'Orbii | Posts')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Override khusus mobile (desktop tidak terpengaruh) --}}
    <style>
        @media (max-width: 767px) {
            .post-page {
                width: 100%;
                max-width: 100%;
                overflow-x: clip;
            }

            .post-statistics {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .stat-card {
                min-width: 0 !important;
                width: auto !important;
            }

            .post-card {
                min-width: 0;
                max-width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="post-page w-full min-w-0 max-w-7xl mx-auto px-0 sm:px-4 py-4 sm:py-6">
        <div class="content-header flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5 sm:mb-6">
            {{-- Bagian Kiri: Judul & Subtitle --}}
            <div class="min-w-0">
                <h1 class="text-2xl font-bold text-neutral-900">Riwayat Konten</h1>
                <p class="text-xs text-neutral-500">Kelola semua postingan yang kamu buat di berbagai club.</p>
            </div>

            {{-- Bagian Kanan: Input Search & Tombol Aksi --}}
            <div class="flex flex-col items-start md:items-end gap-3 w-full md:w-auto min-w-0">
                {{-- Input Search --}}
                <div class="w-full md:w-64">
                    <div class="relative">
                        <input type="text" id="postSearchInput" oninput="filterClubPosts()" onkeydown="handleSearchKeyDown(event)"
                            placeholder="Cari postingan..."
                            class="w-full pl-9 pr-4 py-1.5 text-xs bg-white border border-neutral-300 rounded-full focus:outline-none focus:border-blue-600 text-neutral-800 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-search absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="content-actions flex flex-wrap items-center gap-2.5 w-full md:w-auto justify-start md:justify-end">
                    <a href="{{ route('posts.archived') }}"
                        class="trash-button border border-neutral-300 bg-white text-neutral-700 px-3.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-neutral-100 flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-box-archive"></i> Arsip
                    </a>
                    <a href="{{ route('posts.trash') }}"
                        class="trash-button border border-neutral-300 bg-white text-neutral-700 px-3.5 py-1.5 rounded-lg text-xs font-semibold hover:bg-neutral-100 flex items-center gap-1.5 shadow-sm">
                        <i class="fa-regular fa-trash-can"></i> Sampah
                    </a>
                    <a href="{{ route('posts.create') }}"
                        class="create-post-button bg-blue-600 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-700 flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-plus"></i> Buat Postingan
                    </a>
                </div>
            </div>
        </div>

        <div class="post-statistics grid grid-cols-2 gap-3 sm:gap-4 mb-5 sm:mb-6">
            <div class="stat-card min-w-0 bg-white p-3 sm:p-4 rounded-xl border border-neutral-200">
                <span class="text-xs text-neutral-500 block mb-1 truncate">Total Postingan</span>
                <strong class="text-xl font-bold text-neutral-900">{{ $totalPosts }}</strong>
            </div>
            <div class="stat-card min-w-0 bg-white p-3 sm:p-4 rounded-xl border border-neutral-200">
                <span class="text-xs text-neutral-500 block mb-1 truncate">Total Suka</span>
                <strong class="text-xl font-bold text-neutral-900">{{ $totalLikes ?? 0 }}</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 min-w-0">
            @forelse ($posts as $post)
                <div class="post-card min-w-0 bg-white rounded-xl border border-neutral-200 overflow-hidden flex flex-col justify-between shadow-sm club-post-item"
                    data-search-text="{{ Str::lower(($post->title ?? '') . ' ' . ($post->content ?? '') . ' ' . ($post->club->name ?? '')) }}">
                    <div>
                        <div class="post-header p-3 border-b border-neutral-100 flex items-center justify-between gap-2">
                            <span
                                class="post-club-badge bg-blue-50 text-blue-600 text-[10px] font-semibold px-2 py-0.5 rounded-full truncate">{{ $post->club->name }}</span>
                            <span class="post-date text-[10px] text-neutral-400 shrink-0">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="post-body p-3.5 space-y-2">
                            <h2 class="text-sm font-bold text-neutral-900 line-clamp-1">{{ $post->title }}</h2>
                            <p class="text-xs text-neutral-600 leading-relaxed line-clamp-2 break-words">{{ $post->content }}</p>

                            @php
                                $mediaItems = collect();
                                if (isset($post->media) && count($post->media) > 0) {
                                    $mediaItems = $post->media;
                                } elseif (!empty($post->media_url)) {
                                    $mediaItems = collect([(object) ['file_path' => $post->media_url]]);
                                }
                            @endphp

                            @if ($mediaItems->isNotEmpty())
                                @php
                                    $firstMedia = $mediaItems->first();
                                    $filePath = $firstMedia->file_path ?? $firstMedia;
                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                    $mediaUrl = asset('storage/' . $filePath);
                                @endphp

                                <div class="post-media-preview h-40 w-full rounded-lg overflow-hidden bg-neutral-100 relative mt-2">
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']))
                                        <img src="{{ $mediaUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                    @elseif (in_array($extension, ['mp4', 'mov', 'webm']))
                                        <video class="w-full h-full object-cover" muted>
                                            <source src="{{ $mediaUrl }}" type="video/{{ $extension }}">
                                        </video>
                                    @else
                                        <a href="{{ $mediaUrl }}" target="_blank"
                                            class="w-full h-full flex flex-col items-center justify-center gap-1 text-xs text-neutral-600 hover:text-blue-600">
                                            <i class="fa-solid fa-paperclip text-xl"></i> Lampiran File
                                        </a>
                                    @endif

                                    @if ($mediaItems->count() > 1)
                                        <span
                                            class="absolute top-2 right-2 bg-black/70 text-white text-[10px] px-2 py-0.5 rounded-full font-medium">
                                            +{{ $mediaItems->count() - 1 }} media
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div
                        class="post-footer p-3 bg-neutral-50 border-t border-neutral-100 flex items-center justify-between text-xs">
                        <div class="post-engagement flex items-center gap-3 text-neutral-500">
                            <span><i class="fa-regular fa-heart mr-1"></i>{{ $post->likes_count ?? 0 }}</span>
                            <span><i class="fa-regular fa-comment mr-1"></i>{{ $post->comments_count ?? 0 }}</span>
                        </div>

                        <div class="post-control flex items-center gap-2">
                            <form action="{{ route('posts.archive', $post) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" onclick="return confirm('Arsipkan postingan ini? Postingan tidak akan tampil di beranda.')" title="Arsipkan" class="text-neutral-500 hover:text-amber-600 p-1">
                                    <i class="fa-solid fa-box-archive"></i>
                                </button>
                            </form>
                            <a href="{{ route('posts.edit', $post) }}" title="Edit"
                                class="text-neutral-500 hover:text-blue-600 p-1">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Pindahkan postingan ke sampah?')" title="Hapus"
                                    class="text-neutral-500 hover:text-red-600 p-1">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-post col-span-full text-center py-12 bg-white rounded-xl border border-neutral-200">
                    <p class="text-xs text-neutral-500 mb-3">Belum ada postingan yang dibuat.</p>
                    <a href="{{ route('posts.create') }}" class="text-xs text-blue-600 font-semibold hover:underline">+ Buat
                        Postingan Pertama</a>
                </div>
            @endforelse

            {{-- Pesan jika pencarian tidak ditemukan --}}
            <div id="noPostFound" class="hidden col-span-full text-center py-12 bg-white rounded-xl border border-neutral-200">
                <p class="text-xs text-neutral-500 font-medium">Postingan not found</p>
            </div>
        </div>
    </div>

    {{-- Tombol Navigasi Search Up/Down --}}
    <div id="searchNavButtons" class="fixed bottom-6 right-6 z-40 hidden flex-col gap-2">
        <button type="button" onclick="navigateSearchResult('up')"
            class="p-3 rounded-full bg-white/80 backdrop-blur-md border border-neutral-200 shadow-lg text-neutral-700 hover:bg-blue-600 hover:text-white transition-all cursor-pointer"
            title="Postingan Sebelumnya">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m18 15-6-6-6 6" />
            </svg>
        </button>
        <button type="button" onclick="navigateSearchResult('down')"
            class="p-3 rounded-full bg-white/80 backdrop-blur-md border border-neutral-200 shadow-lg text-neutral-700 hover:bg-blue-600 hover:text-white transition-all cursor-pointer"
            title="Postingan Selanjutnya">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6" />
            </svg>
        </button>
    </div>

    <script>
        let currentSearchIndex = -1;
        let matchedPosts = [];

        function filterClubPosts() {
            const inputEl = document.getElementById('postSearchInput');
            if (!inputEl) return;

            const query = inputEl.value.toLowerCase().trim();
            const posts = document.querySelectorAll('.club-post-item');
            const noFoundMsg = document.getElementById('noPostFound');
            const navButtons = document.getElementById('searchNavButtons');

            matchedPosts = [];
            currentSearchIndex = -1;

            posts.forEach(post => {
                const textData = (post.getAttribute('data-search-text') || '').toLowerCase();

                if (query === '' || textData.includes(query)) {
                    post.style.display = 'flex';
                    if (query !== '') {
                        matchedPosts.push(post);
                    }
                } else {
                    post.style.display = 'none';
                }
            });

            if (noFoundMsg) {
                if (query !== '' && matchedPosts.length === 0) {
                    noFoundMsg.classList.remove('hidden');
                } else {
                    noFoundMsg.classList.add('hidden');
                }
            }

            if (navButtons) {
                if (query !== '' && matchedPosts.length > 1) {
                    navButtons.classList.remove('hidden');
                    navButtons.classList.add('flex');
                } else {
                    navButtons.classList.add('hidden');
                    navButtons.classList.remove('flex');
                }
            }
        }

        function handleSearchKeyDown(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                filterClubPosts();

                if (matchedPosts.length > 0) {
                    currentSearchIndex = 0;
                    scrollToPost(matchedPosts[currentSearchIndex]);
                }
            }
        }

        function navigateSearchResult(direction) {
            if (matchedPosts.length === 0) return;

            if (direction === 'down') {
                currentSearchIndex++;
                if (currentSearchIndex >= matchedPosts.length) {
                    currentSearchIndex = 0;
                }
            } else if (direction === 'up') {
                currentSearchIndex--;
                if (currentSearchIndex < 0) {
                    currentSearchIndex = matchedPosts.length - 1;
                }
            }

            scrollToPost(matchedPosts[currentSearchIndex]);
        }

        function scrollToPost(element) {
            if (!element) return;
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });

            element.classList.add('ring-2', 'ring-blue-600', 'transition-all', 'duration-300');
            setTimeout(() => {
                element.classList.remove('ring-2', 'ring-blue-600');
            }, 1500);
        }
    </script>
@endsection