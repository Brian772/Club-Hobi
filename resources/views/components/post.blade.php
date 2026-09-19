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

    $mediaList = $mediaCollection
        ->map(function ($item) {
            $filePath = is_object($item) ? $item->file_path ?? ($item->url ?? '') : $item;
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $filename = basename($filePath);
            $mediaId = is_object($item) ? $item->id ?? null : null;

            $mediaUrl = Str::startsWith($filePath, ['http://', 'https://']) ? $filePath : asset('storage/' . $filePath);

            $downloadUrl = $mediaId ? route('media.download', $mediaId) : $mediaUrl;

            return [
                'id' => $mediaId,
                'url' => $mediaUrl,
                'download_url' => $downloadUrl,
                'ext' => $extension,
                'filename' => $filename,
            ];
        })
        ->values();
  @endphp

  @if ($mediaCollection->isNotEmpty())
    <div class="relative group w-full bg-black overflow-hidden rounded-none -mt-1 my-2"
      id="carousel-{{ $post->id }}">
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
                  <source src="{{ $item['url'] }}"
                    type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
                </video>
              </div>
            @else
              <div class="p-4 bg-white">
                <div onclick='openMediaModal({{ json_encode($mediaList) }}, {{ $index }})'
                  class="flex items-center gap-3 p-3 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 rounded-lg cursor-pointer transition-colors">
                  @if ($isAudio)
                    <div
                      class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                      <i class="fa-solid fa-music text-lg"></i>
                    </div>
                  @else
                    <div
                      class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
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

        <div class="absolute bottom-2 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-10"
          id="dots-{{ $post->id }}">
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
      <button type="button" id="likeBtn-{{ $post->id }}" onclick="toggleLike('{{ $post->id }}')"
        data-liked="{{ $isLiked ? 'true' : 'false' }}" data-like-url="{{ route('posts.like', $post->id) }}"
        class="flex items-center gap-1.5 hover:text-red-500 transition-colors">
        <i id="likeIcon-{{ $post->id }}"
          class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-base"></i>
        <span><span id="likeCount-{{ $post->id }}">{{ $post->likes_count ?? 0 }}</span> Suka</span>
      </button>

      <button type="button" onclick="openCommentModal('commentModal-{{ $post->id }}')"
        class="flex items-center gap-1.5 hover:text-blue-600 transition-colors">
        <i class="fa-regular fa-comment text-base"></i>
        <span id="commentsCount-{{ $post->id }}">{{ $post->comments_count ?? 0 }}</span>
        <span>Komentar</span>
      </button>
    </div>

    @if ($post->title)
      <h3 class="text-sm font-bold text-neutral-900 mb-1">{{ $post->title }}</h3>
    @endif
    <p class="text-neutral-800 text-sm leading-relaxed">{{ $post->content }}</p>
  </div>
</div>

<div id="commentModal-{{ $post->id }}"
  class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10"
  onclick="closeCommentModal('commentModal-{{ $post->id }}')">

  <div
    class="relative bg-white text-neutral-900 rounded-xl overflow-hidden w-full max-w-5xl h-[85vh] flex flex-col md:flex-row shadow-2xl border border-neutral-200"
    onclick="event.stopPropagation()">

    <button onclick="closeCommentModal('commentModal-{{ $post->id }}')"
      class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-600 hover:text-red-500 text-xl font-bold z-20 cursor-pointer">&times;</button>

    <div
      class="w-full md:w-1/2 bg-black flex items-center justify-center relative overflow-hidden h-64 md:h-full border-b md:border-b-0 md:border-r border-neutral-200">
      @if ($mediaList->isNotEmpty())
        <div class="relative group w-full h-full flex items-center justify-center bg-black">
          <div class="flex w-full h-full transition-transform duration-300 ease-in-out"
            id="modal-slides-{{ $post->id }}">
            @foreach ($mediaList as $item)
              @php
                $isImg = in_array($item['ext'], ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                $isVid = in_array($item['ext'], ['mp4', 'mov', 'webm']);
              @endphp
              <div class="w-full h-full shrink-0 flex items-center justify-center">
                @if ($isImg)
                  <img src="{{ $item['url'] }}" class="w-full h-full object-cover" alt="Post Media">
                @elseif($isVid)
                  <video controls class="w-full h-full object-contain">
                    <source src="{{ $item['url'] }}"
                      type="video/{{ $item['ext'] === 'mov' ? 'quicktime' : $item['ext'] }}">
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

          @if ($mediaList->count() > 1)
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

          @if (!empty($post->title))
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
                    @if ($comment->user_id === auth()->id())
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

              @if ($allReplies->isNotEmpty())
                <div class="ml-9 space-y-2.5 border-l-2 border-neutral-100 pl-3 replies-container">
                  @foreach ($allReplies as $reply)
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
                          @if ($reply->user_id === auth()->id())
                            <button type="button"
                              onclick="deleteComment('{{ $reply->id }}', '{{ $post->id }}')"
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
          <button type="button" id="likeBtnModal-{{ $post->id }}" onclick="toggleLike('{{ $post->id }}')"
            class="hover:text-red-500 transition-colors">
            <i id="likeIconModal-{{ $post->id }}"
              class="{{ $isLiked ? 'fa-solid text-red-500' : 'fa-regular' }} fa-heart text-xl"></i>
          </button>
          <i class="fa-regular fa-comment text-xl text-neutral-700"></i>
        </div>
        <p class="text-xs font-bold text-neutral-900 mb-1">
          <span id="likeCountModal-{{ $post->id }}">{{ $post->likes_count ?? 0 }}</span> Suka
        </p>
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
            <input type="text" name="content" id="inputContent-{{ $post->id }}"
              placeholder="Tambahkan komentar..." required
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
