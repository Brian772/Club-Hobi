@extends('layouts.app')

@section('styles')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-title lg:text-heading-2 font-bold text-neutral-900">Halo, {{ auth()->user()->name }} </h1>
    <a href="{{ route('posts.create') }}"
      class="hidden lg:inline-flex items-center gap-2 bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-md hover:bg-primary-active">
      + Buat Postingan
    </a>
  </div>

  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold text-neutral-900">Club yang anda ikuti</h2>
    <a href="{{ route('clubs.index') }}" class="text-sm text-neutral-500 hover:text-neutral-800">Lihat selengkapnya →</a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 border-b border-hairline pb-4">
    @if ($joinedClub->isNotEmpty())
      @foreach ($joinedClub as $club)
        <a href="{{ route('clubs.show', $club->id) }}"
          class="bg-white rounded-xl border border-neutral-200 overflow-hidden">
          {{-- <img src="{{ $club->cover_url }}" alt="{{ $club->name }}" class="w-full h-32 object-cover"> --}}
          <img src="{{ $club->cover_url ? Storage::url($club->cover_url) : '' }}" alt="{{ $club->name }}"
            class="w-full h-48 object-cover">
          <div class="p-4">
            <span class="text-caption text-ink-muted">{{ $club->hobby->name ?? 'Kategori Tidak Diketahui' }}</span>
            <h3 class="text-lg font-semibold mb-2">{{ $club->name }}</h3>
            <p class="text-caption text-ink-muted mb-2 line-clamp-2">{{ $club->description }}</p>
            <p class="text-caption text-ink-muted">{{ $club->members_count }} Anggota</p>
          </div>
        </a>
      @endforeach
    @else
      <section id="alreadyJoin" class="flex w-full">
        <div class="mb-12 flex justify-center w-full">
          <p class="text-caption text-ink-muted">Anda belum bergabung ke klub manapun.</p>
        </div>
      </section>
    @endif
  </div>

  <div class="max-w-[400px] space-y-4">
    @foreach ($feedPosts as $post)
      <div class="bg-white rounded-xl border border-neutral-200 overflow-hidden shadow-sm">

        <div class="flex items-center justify-between p-3.5 border-b border-neutral-100">
          <div class="flex items-center gap-3">
            @php
              $postAuthor = $post->author ?? $post->user;
            @endphp

            <img src="{{ $postAuthor->avatar_full_url ?? 'https://via.placeholder.com/40' }}"
              class="w-8 h-8 rounded-full object-cover" alt="{{ $postAuthor->name ?? 'User' }}">
            <div>
              <span
                class="text-xs font-semibold text-neutral-900 block leading-tight">{{ $postAuthor->name ?? 'Anonim' }}</span>
              <span class="text-[11px] text-neutral-400">memposting di <span
                  class="font-medium text-neutral-700">{{ $post->club->name ?? 'Umum' }}</span></span>
            </div>
          </div>
          <span class="text-[11px] text-neutral-400">{{ $post->created_at->diffForHumans() }}</span>
        </div>

        @php
          $mediaCollection = collect();

          if ($post->relationLoaded('media') && $post->media->isNotEmpty()) {
            $mediaCollection = $post->media;
          } elseif (!empty($post->file_path)) {
            $mediaCollection = collect([(object) ['file_path' => $post->file_path, 'id' => null]]);
          } elseif (!empty($post->media_url)) {
            $mediaCollection = collect([(object) ['file_path' => $post->media_url, 'id' => null]]);
          }

          $mediaList = $mediaCollection->map(function ($item) {
            $filePath = is_object($item) ? ($item->file_path ?? $item->url ?? '') : $item;
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $filename = basename($filePath);
            $mediaId = is_object($item) ? ($item->id ?? null) : null;

            $mediaUrl = Str::startsWith($filePath, ['http://', 'https://'])
              ? $filePath
              : asset('storage/' . $filePath);

            $downloadUrl = $mediaId
              ? route('media.download', $mediaId)
              : $mediaUrl;

            return [
              'id' => $mediaId,
              'url' => $mediaUrl,
              'download_url' => $downloadUrl,
              'ext' => $extension,
              'filename' => $filename
            ];
          })->values();
        @endphp

        @if ($mediaCollection->isNotEmpty())
          <div class="relative group w-full bg-black overflow-hidden rounded-none -mt-1 my-2" id="carousel-{{ $post->id }}">
            <div class="flex transition-transform duration-300 ease-in-out" id="slides-{{ $post->id }}">
              @foreach ($mediaList as $index => $item)
                @php
                  $isImage = in_array($item['ext'], ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                  $isVideo = in_array($item['ext'], ['mp4', 'mov', 'webm']);
                  $isAudio = in_array($item['ext'], ['mp3', 'wav', 'ogg', 'm4a']);
                @endphp

                <div class="w-full shrink-0">
                  @if ($isImage)
                    <div class="cursor-pointer flex items-center justify-center bg-black"
                      onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'>
                      <img src="{{ $item['url'] }}" class="w-full max-h-[400px] object-cover" alt="Post Media">
                    </div>
                  @elseif ($isVideo)
                    <div class="w-full max-h-[380px] bg-black flex items-center justify-center cursor-pointer"
                      onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'>
                      <video class="w-full max-h-[380px] object-contain pointer-events-none" controls preload="metadata">
                        <source src="{{ $item['url'] }}" type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
                      </video>
                    </div>
                  @else
                    <div class="p-4 bg-white">
                      <div onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'
                        class="flex items-center gap-3 p-3 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 rounded-lg cursor-pointer transition-colors">
                        @if ($isAudio)
                          <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-music text-lg"></i>
                          </div>
                        @else
                          <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-lines text-lg"></i>
                          </div>
                        @endif
                        <div class="flex-1 min-w-0">
                          <p class="text-xs font-semibold text-neutral-800 truncate">{{ $item['filename'] }}</p>
                          <span class="text-[10px] uppercase font-bold text-neutral-400">{{ $item['ext'] }} File</span>
                        </div>
                        <i class="fa-solid fa-expand text-xs text-neutral-400"></i>
                      </div>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>

            @if ($mediaList->count() > 1)
              <button type="button" onclick="moveSlide('{{ $post->id }}', -1)"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/50 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer">
                ‹
              </button>
              <button type="button" onclick="moveSlide('{{ $post->id }}', 1)"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 bg-black/50 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity z-10 cursor-pointer">
                ›
              </button>

              <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-10" id="dots-{{ $post->id }}">
                @foreach ($mediaList as $idx => $item)
                  <span
                    class="w-1.5 h-1.5 rounded-full bg-white/50 transition-all {{ $idx === 0 ? '!bg-white w-2.5' : '' }}"></span>
                @endforeach
              </div>
            @endif
          </div>
        @endif

        <div class="p-3.5">
          <div class="flex items-center gap-4 text-neutral-600 text-xs font-medium mb-2.5">
            @php
              $isLiked = $post->likes->contains('user_id', auth()->id());
            @endphp
            <form action="{{ route('posts.like', $post->id) }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="flex items-center gap-1.5 hover:text-red-500 transition-colors">
                <i class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-base"></i>
                <span>{{ $post->likes_count ?? 0 }} Suka</span>
              </button>
            </form>

            <button type="button" onclick="openCommentModal('commentModal-{{ $post->id }}')"
              class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
              <i class="fa-regular fa-comment text-base"></i>
              <span id="commentsCount-{{ $post->id }}">{{ $post->comments_count ?? 0 }}</span>
              <span>Komentar</span>
            </button>
          </div>

          @if ($post->title)
            <h3 class="text-xs font-bold text-neutral-900 mb-1">{{ $post->title }}</h3>
          @endif
          <p class="text-neutral-800 text-xs leading-relaxed">{{ $post->content }}</p>
        </div>
      </div>

      <div id="commentModal-{{ $post->id }}"
        class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10"
        onclick="closeCommentModal('commentModal-{{ $post->id }}')">

        <button onclick="closeCommentModal('commentModal-{{ $post->id }}')"
          class="absolute top-4 right-4 text-white hover:text-neutral-300 text-3xl font-bold z-50 cursor-pointer">&times;</button>

        <div
          class="bg-white text-neutral-900 rounded-xl overflow-hidden w-full max-w-5xl h-[85vh] flex flex-col md:flex-row shadow-2xl border border-neutral-200"
          onclick="event.stopPropagation()">

          <div
            class="w-full md:w-1/2 bg-black flex items-center justify-center relative overflow-hidden h-64 md:h-full border-b md:border-b-0 md:border-r border-neutral-200">
            @if($mediaList->isNotEmpty())
              <div class="relative group w-full h-full flex items-center justify-center bg-black">
                <div class="flex w-full h-full transition-transform duration-300 ease-in-out"
                  id="modal-slides-{{ $post->id }}">
                  @foreach ($mediaList as $item)
                    @php
                      $isImg = in_array($item['ext'], ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                      $isVid = in_array($item['ext'], ['mp4', 'mov', 'webm']);
                    @endphp
                    <div class="w-full h-full shrink-0 flex items-center justify-center">
                      @if($isImg)
                        <img src="{{ $item['url'] }}" class="w-full h-full object-cover" alt="Post Media">
                      @elseif($isVid)
                        <video controls class="w-full h-full object-contain">
                          <source src="{{ $item['url'] }}" type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
                        </video>
                      @else
                        <div class="p-6 text-center text-white">
                          <i class="fa-solid fa-file-lines text-5xl mb-3 text-neutral-400"></i>
                          <p class="text-xs font-semibold truncate max-w-xs">{{ $item['filename'] }}</p>
                        </div>
                      @endif
                    </div>
                  @endforeach
                </div>

                @if($mediaList->count() > 1)
                  <button type="button" onclick="moveModalSlide('{{ $post->id }}', -1)"
                    class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 text-white rounded-full flex items-center justify-center text-sm z-10 cursor-pointer">‹</button>
                  <button type="button" onclick="moveModalSlide('{{ $post->id }}', 1)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 text-white rounded-full flex items-center justify-center text-sm z-10 cursor-pointer">›</button>
                @endif
              </div>
            @else
              <div class="max-w-md text-center p-6 text-white">
                @if ($post->title)
                  <h3 class="text-base font-bold mb-2">{{ $post->title }}</h3>
                @endif
                <p class="text-sm leading-relaxed italic">"{{ $post->content }}"</p>
              </div>
            @endif
          </div>

          <div class="w-full md:w-1/2 flex flex-col h-full bg-white min-w-0">

            <div class="px-4 py-3 border-b border-neutral-100 shrink-0 flex gap-3">
              <img src="{{ $postAuthor->avatar_full_url ?? 'https://via.placeholder.com/40' }}"
                class="w-8 h-8 rounded-full object-cover border border-neutral-200 shrink-0" alt="Author">

              <div class="flex-1 min-w-0">
                <p class="text-xs text-neutral-900 truncate leading-tight">
                  <span class="font-bold">{{ $postAuthor->name ?? 'Anonim' }}</span>
                  <span class="text-neutral-500 font-normal"> di </span>
                  <span class="font-medium text-neutral-700">{{ $post->club->name ?? 'Umum' }}</span>
                </p>

                @if(!empty($post->title))
                  <h4 class="text-sm font-bold text-neutral-900 mt-0.5 leading-tight break-words">
                    {{ $post->title }}
                  </h4>
                @endif

                <p class="text-xs text-neutral-700 mt-1 whitespace-pre-line leading-snug break-words">
                  {{ $post->content }}
                </p>

                <span class="text-[10px] text-neutral-400 mt-1 block leading-none">
                  {{ $post->created_at->diffForHumans() }}
                </span>
              </div>
            </div>

            <div class="flex-1 p-4 overflow-y-auto space-y-4">
              <div class="space-y-4">
                @forelse ($post->comments->whereNull('parent_id') as $comment)
                  <div class="space-y-2" data-comment-id="{{ $comment->id }}">
                    <div class="flex gap-3">
                      <img src="{{ $comment->user->avatar_full_url ?? 'https://via.placeholder.com/32' }}"
                        class="w-7 h-7 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                      <div class="flex-1 min-w-0">
                        <p class="text-xs text-neutral-800 leading-snug break-words">
                          <span class="font-bold text-neutral-900 mr-1.5">{{ $comment->user->name ?? 'User' }}</span>
                          <span>{{ $comment->content }}</span>
                        </p>
                        <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                          <span>{{ $comment->created_at->diffForHumans() }}</span>
                          <button type="button"
                            onclick="replyComment('{{ $post->id }}', '{{ $comment->id }}', '{{ $comment->user->name ?? 'User' }}')"
                            class="hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                          @if($comment->user_id === auth()->id())
                            <button type="button" onclick="deleteComment('{{ $comment->id }}', '{{ $post->id }}')"
                              class="hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                          @endif
                        </div>
                      </div>
                    </div>

                    @php
                      $getReplies = function ($parentId) use (&$getReplies, $post) {
                        $directReplies = $post->comments->where('parent_id', $parentId);
                        $children = collect();
                        foreach ($directReplies as $reply) {
                          $children->push($reply);
                          $children = $children->merge($getReplies($reply->id));
                        }
                        return $children;
                      };

                      $allReplies = $getReplies($comment->id);
                    @endphp

                    @if($allReplies->isNotEmpty())
                      <div class="ml-9 space-y-2.5 border-l-2 border-neutral-100 pl-3 replies-container">
                        @foreach($allReplies as $reply)
                          <div class="flex gap-2.5" data-comment-id="{{ $reply->id }}">
                            <img src="{{ $reply->user->avatar_full_url ?? 'https://via.placeholder.com/28' }}"
                              class="w-6 h-6 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                            <div class="flex-1 min-w-0">
                              <p class="text-xs text-neutral-800 leading-snug break-words">
                                <span class="font-bold text-neutral-900 mr-1.5">{{ $reply->user->name ?? 'User' }}</span>
                                <span>{{ $reply->content }}</span>
                              </p>
                              <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                                <span>{{ $reply->created_at->diffForHumans() }}</span>
                                <button type="button"
                                  onclick="replyComment('{{ $post->id }}', '{{ $reply->id }}', '{{ $reply->user->name ?? 'User' }}')"
                                  class="hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                                @if($reply->user_id === auth()->id())
                                  <button type="button" onclick="deleteComment('{{ $reply->id }}', '{{ $post->id }}')"
                                    class="hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                                @endif
                              </div>
                            </div>
                          </div>
                        @endforeach
                      </div>
                    @endif
                  </div>
                @empty
                  <p class="text-xs text-neutral-400 text-center py-8">Belum ada komentar.</p>
                @endforelse
              </div>
            </div>

            <div class="p-4 border-t border-neutral-100 bg-white shrink-0">
              <div class="flex items-center gap-4 text-neutral-700 mb-2">
                <form action="{{ route('posts.like', $post->id) }}" method="POST" class="inline">
                  @csrf
                  <button type="submit" class="hover:text-red-500 transition-colors">
                    <i class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-xl"></i>
                  </button>
                </form>
                <i class="fa-regular fa-comment text-xl text-neutral-700"></i>
              </div>
              <p class="text-xs font-bold text-neutral-900 mb-1">{{ $post->likes_count ?? 0 }} Suka</p>
              <span
                class="text-[10px] text-neutral-400 uppercase block mb-3 font-semibold">{{ $post->created_at->format('M d, Y') }}</span>

              <form id="commentForm-{{ $post->id }}" onsubmit="submitComment(event, '{{ $post->id }}')"
                class="flex flex-col border-t border-neutral-100 pt-2">
                @csrf
                <input type="hidden" name="parent_id" id="parentId-{{ $post->id }}" value="">

                <div id="replyIndicator-{{ $post->id }}"
                  class="hidden flex items-center justify-between text-[11px] text-neutral-500 bg-neutral-100 px-2 py-1 rounded mb-2">
                  <span>Membalas <strong id="replyTarget-{{ $post->id }}" class="text-neutral-800"></strong></span>
                  <button type="button" onclick="cancelReply('{{ $post->id }}')"
                    class="text-neutral-400 hover:text-red-500 font-bold">&times;</button>
                </div>

                <div class="flex items-center gap-2">
                  <input type="text" name="content" id="inputContent-{{ $post->id }}" placeholder="Tambahkan komentar..."
                    required
                    class="flex-1 bg-transparent text-xs text-neutral-800 placeholder-neutral-400 focus:outline-none">
                  <button type="submit" class="text-blue-600 hover:text-blue-700 text-xs font-bold cursor-pointer">
                    Kirim
                  </button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    @endforeach

    <div id="mediaModal" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center p-4"
      onclick="closeMediaModal()">
      <button onclick="closeMediaModal()"
        class="absolute top-4 right-4 text-white text-3xl font-bold z-50 cursor-pointer">&times;</button>

      <button id="modalPrevBtn" onclick="event.stopPropagation(); changeModalSlide(-1)"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-xl z-50 cursor-pointer">‹</button>

      <button id="modalNextBtn" onclick="event.stopPropagation(); changeModalSlide(1)"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-xl z-50 cursor-pointer">›</button>

      <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center justify-center"
        onclick="event.stopPropagation()">
        <div id="modalCounter" class="absolute -top-8 text-white/80 text-xs font-semibold"></div>
        <div id="modalContent" class="w-full flex flex-col items-center justify-center"></div>
      </div>
    </div>
  </div>

  <script>
    function openCommentModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
    }

    function closeCommentModal(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.querySelectorAll('video, audio').forEach(media => media.pause());
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    }

    async function submitComment(event, postId) {
      event.preventDefault();

      const form = document.getElementById(`commentForm-${postId}`);
      const inputContent = document.getElementById(`inputContent-${postId}`);
      const submitBtn = form.querySelector('button[type="submit"]');

      if (!inputContent.value.trim()) return;

      submitBtn.disabled = true;
      submitBtn.innerText = 'Mengirim...';

      try {
        const response = await fetch(`/posts/${postId}/comments`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: new FormData(form)
        });

        if (!response.ok) {
          let errorMsg = 'Gagal mengirim komentar.';
          try {
            const errData = await response.json();
            errorMsg = errData.message || errorMsg;
          } catch (e) { }
          throw new Error(errorMsg);
        }

        const data = await response.json();
        appendNewComment(postId, data);
        updateCommentsCount(postId, 1);
        inputContent.value = '';
        cancelReply(postId);

      } catch (error) {
        console.error('Comment Error:', error);
        alert(error.message || 'Terjadi kesalahan saat mengirim komentar.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Kirim';
      }
    }

    function updateCommentsCount(postId, diff) {
      const commentsCount = document.getElementById(`commentsCount-${postId}`);
      if (commentsCount) {
        const currentCount = parseInt(commentsCount.innerText) || 0;
        commentsCount.innerText = Math.max(0, currentCount + diff);
      }
    }

    function appendNewComment(postId, commentData) {
      const modal = document.getElementById(`commentModal-${postId}`);
      if (!modal) return;

      const commentsContainer = modal.querySelector('.overflow-y-auto .space-y-4');
      if (!commentsContainer) return;

      const emptyText = commentsContainer.querySelector('p.text-center');
      if (emptyText) emptyText.remove();

      const userName = commentData.user?.name || 'User';
      const userAvatar = commentData.user?.avatar_full_url || 'https://via.placeholder.com/32';

      const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      };

      if (commentData.parent_id) {
        const parentCommentElement = modal.querySelector(`[data-comment-id="${commentData.parent_id}"]`);
        if (parentCommentElement) {
          let repliesContainer = parentCommentElement.querySelector('.replies-container');
          if (!repliesContainer) {
            repliesContainer = document.createElement('div');
            repliesContainer.className = 'ml-9 space-y-2.5 border-l-2 border-neutral-100 pl-3 replies-container mt-2';
            parentCommentElement.appendChild(repliesContainer);
          }

          const replyWrapper = document.createElement('div');
          replyWrapper.className = 'flex gap-2.5';
          replyWrapper.setAttribute('data-comment-id', commentData.id);
          replyWrapper.innerHTML = `
                <img src="${userAvatar}" class="w-6 h-6 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-neutral-800 leading-snug break-words">
                    <span class="font-bold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                  </div>
                </div>`;

          replyWrapper.querySelector('.reply-button').addEventListener('click', () => {
            replyComment(postId, commentData.id, userName);
          });
          replyWrapper.querySelector('.delete-button').addEventListener('click', () => {
            deleteComment(commentData.id, postId);
          });

          repliesContainer.appendChild(replyWrapper);
        }
      } else {
        const newCommentWrapper = document.createElement('div');
        newCommentWrapper.className = 'space-y-2';
        newCommentWrapper.setAttribute('data-comment-id', commentData.id);
        newCommentWrapper.innerHTML = `
              <div class="flex gap-3">
                <img src="${userAvatar}" class="w-7 h-7 rounded-full object-cover shrink-0 border border-neutral-200" alt="User">
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-neutral-800 leading-snug break-words">
                    <span class="font-bold text-neutral-900 mr-1.5">${escapeHtml(userName)}</span>
                    <span>${escapeHtml(commentData.content)}</span>
                  </p>
                  <div class="flex items-center gap-3 mt-1 text-[10px] text-neutral-400 font-medium">
                    <span>Baru saja</span>
                    <button type="button" class="reply-button hover:text-blue-600 cursor-pointer font-semibold">Balas</button>
                    <button type="button" class="delete-button hover:text-red-500 cursor-pointer font-semibold">Hapus</button>
                  </div>
                </div>
              </div>`;

        newCommentWrapper.querySelector('.reply-button').addEventListener('click', () => {
          replyComment(postId, commentData.id, userName);
        });
        newCommentWrapper.querySelector('.delete-button').addEventListener('click', () => {
          deleteComment(commentData.id, postId);
        });

        commentsContainer.appendChild(newCommentWrapper);
      }

      const scrollableArea = modal.querySelector('.overflow-y-auto');
      if (scrollableArea) {
        requestAnimationFrame(() => {
          scrollableArea.scrollTop = scrollableArea.scrollHeight;
        });
      }
    }

    function replyComment(postId, commentId, username) {
      const parentInput = document.getElementById(`parentId-${postId}`);
      const replyIndicator = document.getElementById(`replyIndicator-${postId}`);
      const replyTarget = document.getElementById(`replyTarget-${postId}`);
      const inputContent = document.getElementById(`inputContent-${postId}`);

      if (parentInput) parentInput.value = commentId;
      if (replyTarget) replyTarget.textContent = `@${username}`;
      if (replyIndicator) replyIndicator.classList.remove('hidden');
      if (inputContent) inputContent.focus();
    }

    function cancelReply(postId) {
      const parentInput = document.getElementById(`parentId-${postId}`);
      const replyIndicator = document.getElementById(`replyIndicator-${postId}`);

      if (parentInput) parentInput.value = '';
      if (replyIndicator) replyIndicator.classList.add('hidden');
    }

    async function deleteComment(commentId, postId) {
      if (!confirm('Hapus komentar ini?')) return;

      const modal = document.getElementById(`commentModal-${postId}`);
      if (!modal) {
        alert('Komentar tidak ditemukan.');
        return;
      }

      const commentElement = modal.querySelector(`[data-comment-id="${commentId}"]`);
      if (!commentElement) {
        alert('Komentar tidak ditemukan.');
        return;
      }

      if (commentElement.dataset.deleting === 'true') return;
      commentElement.dataset.deleting = 'true';

      const parent = commentElement.parentNode;
      const nextSibling = commentElement.nextSibling;
      const deletedCount = 1 + commentElement.querySelectorAll('[data-comment-id]').length;

      const commentsContainer = modal.querySelector('.overflow-y-auto .space-y-4');
      const emptyState = commentsContainer?.querySelector('p.text-center');

      if (emptyState) {
        emptyState.remove();
      }

      commentElement.remove();
      updateCommentsCount(postId, -deletedCount);

      if (
        commentsContainer &&
        !commentsContainer.querySelector('[data-comment-id]')
      ) {
        commentsContainer.insertAdjacentHTML(
          'beforeend',
          '<p class="text-xs text-neutral-400 text-center py-8" data-empty-comments="true">Belum ada komentar.</p>'
        );
      }

      try {
        const response = await fetch(`{{ url('/comments') }}/${encodeURIComponent(commentId)}`, {
          method: 'DELETE',
          credentials: 'same-origin',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const currentEmptyState = commentsContainer?.querySelector('[data-empty-comments="true"]');
        if (
          currentEmptyState &&
          commentsContainer.querySelector('[data-comment-id]')
        ) {
          currentEmptyState.remove();
        }
      } catch (error) {
        if (parent) {
          if (nextSibling && nextSibling.parentNode === parent) {
            parent.insertBefore(commentElement, nextSibling);
          } else {
            parent.appendChild(commentElement);
          }
        }

        commentElement.dataset.deleting = 'false';
        updateCommentsCount(postId, deletedCount);

        const currentEmptyState = commentsContainer?.querySelector('[data-empty-comments="true"]');
        if (currentEmptyState) {
          currentEmptyState.remove();
        }

        console.error('Delete Comment Error:', error);
        alert(error.message || 'Terjadi kesalahan saat menghapus komentar.');
      }
    }

    const slideIndices = {};
    function moveSlide(postId, direction) {
      if (!(postId in slideIndices)) slideIndices[postId] = 0;
      const slidesContainer = document.getElementById(`slides-${postId}`);
      const totalSlides = slidesContainer.children.length;

      slideIndices[postId] = (slideIndices[postId] + direction + totalSlides) % totalSlides;
      const currentIndex = slideIndices[postId];
      slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;

      const dots = document.querySelectorAll(`#dots-${postId} span`);
      dots.forEach((dot, idx) => {
        dot.classList.toggle('!bg-white', idx === currentIndex);
        dot.classList.toggle('w-2.5', idx === currentIndex);
      });
    }

    const modalSlideIndices = {};
    function moveModalSlide(postId, direction) {
      if (!(postId in modalSlideIndices)) modalSlideIndices[postId] = 0;
      const slidesContainer = document.getElementById(`modal-slides-${postId}`);
      const totalSlides = slidesContainer.children.length;

      modalSlideIndices[postId] = (modalSlideIndices[postId] + direction + totalSlides) % totalSlides;
      slidesContainer.style.transform = `translateX(-${modalSlideIndices[postId] * 100}%)`;
    }

    let currentModalMediaList = [];
    let currentModalIndex = 0;

    function openMediaModal(mediaList, index = 0) {
      currentModalMediaList = mediaList;
      currentModalIndex = index;
      const modal = document.getElementById('mediaModal');
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      renderModalContent();
    }

    async function downloadFileAsync(downloadUrl, filename, btnElement) {
      const originalText = btnElement.innerHTML;
      btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengunduh...';
      btnElement.style.pointerEvents = 'none';

      try {
        const response = await fetch(downloadUrl, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (response.status === 401 || response.status === 419) {
          alert('Sesi login telah berakhir. Silakan login kembali.');
          window.location.reload();
          return;
        }

        if (!response.ok) {
          throw new Error('Gagal mengunduh file.');
        }

        const blob = await response.blob();
        const blobUrl = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = filename || 'download';
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(blobUrl);
      } catch (error) {
        console.error('Download error:', error);
        alert(error.message || 'Terjadi kesalahan saat mengunduh file.');
      } finally {
        btnElement.innerHTML = originalText;
        btnElement.style.pointerEvents = 'auto';
      }
    }

    function renderModalContent() {
      const content = document.getElementById('modalContent');
      const counter = document.getElementById('modalCounter');
      const prevBtn = document.getElementById('modalPrevBtn');
      const nextBtn = document.getElementById('modalNextBtn');

      if (!currentModalMediaList || currentModalMediaList.length === 0) return;

      const item = currentModalMediaList[currentModalIndex];
      const { url, download_url, ext, filename } = item;

      if (currentModalMediaList.length > 1) {
        prevBtn.classList.remove('hidden');
        nextBtn.classList.remove('hidden');
        counter.classList.remove('hidden');
        counter.textContent = `${currentModalIndex + 1} / ${currentModalMediaList.length}`;
      } else {
        prevBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
        counter.classList.add('hidden');
      }

      const downloadButtonHtml = `
            <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow transition-colors cursor-pointer">
              <i class="fa-solid fa-download"></i> Unduh File
            </button>`;

      if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
        content.innerHTML = `
            <div class="relative flex flex-col items-center">
              <img src="${url}" class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-lg">
              ${downloadButtonHtml}
            </div>`;
      } else if (['mp4', 'mov', 'webm'].includes(ext)) {
        const mimeType = ext === 'mov' ? 'video/quicktime' : `video/${ext}`;
        content.innerHTML = `
            <div class="relative flex flex-col items-center">
              <video controls autoplay class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-lg">
                <source src="${url}" type="${mimeType}">
              </video>
              ${downloadButtonHtml}
            </div>`;
      } else if (['mp3', 'wav', 'ogg', 'm4a'].includes(ext)) {
        content.innerHTML = `
            <div class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center gap-4 min-w-[320px] max-w-md w-full">
              <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                <i class="fa-solid fa-music text-2xl"></i>
              </div>
              <p class="text-sm font-semibold text-neutral-800 text-center truncate w-full">${filename}</p>
              <audio controls autoplay class="w-full"><source src="${url}" type="audio/${ext}"></audio>
              <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg text-center transition-colors cursor-pointer flex items-center justify-center gap-2">
                <i class="fa-solid fa-download"></i> Unduh File
              </button>
            </div>`;
      } else {
        content.innerHTML = `
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl w-full max-w-3xl h-[78vh] flex flex-col">
              <div class="flex items-center justify-between p-3.5 bg-neutral-100 border-b border-neutral-200">
                <span class="text-xs font-semibold text-neutral-700 truncate max-w-[70%]">${filename}</span>
                <button type="button" onclick="downloadFileAsync('${download_url}', '${filename}', this)" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors cursor-pointer flex items-center gap-2">
                  <i class="fa-solid fa-download"></i> Unduh File
                </button>
              </div>
              <iframe src="${url}" class="w-full h-full border-none"></iframe>
            </div>`;
      }
    }

    function changeModalSlide(direction) {
      if (currentModalMediaList.length <= 1) return;
      currentModalIndex = (currentModalIndex + direction + currentModalMediaList.length) % currentModalMediaList.length;
      renderModalContent();
    }

    function closeMediaModal() {
      const modal = document.getElementById('mediaModal');
      const content = document.getElementById('modalContent');
      modal.classList.add('hidden');
      document.body.style.overflow = 'auto';
      content.innerHTML = '';
      currentModalMediaList = [];
      currentModalIndex = 0;
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' || event.key === 'Esc') {
        closeMediaModal();
        document.querySelectorAll('[id^="commentModal-"]').forEach(m => closeCommentModal(m.id));
      } else if (event.key === 'ArrowLeft') {
        changeModalSlide(-1);
      } else if (event.key === 'ArrowRight') {
        changeModalSlide(1);
      }
    });
  </script>
@endsection