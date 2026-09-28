<div class="flex flex-col mb-4 pb-2 border-b border-hairline">
  <div class="min-w-10 flex flex-row items-start gap-4">
    <img src="{{ $post->user->avatar_full_url }}" alt="{{ $post->user->name }}" width="40px" height="40px"
      class="w-10 h-10 rounded-full object-cover mb-2">

    <div class="flex flex-col w-full items-start gap-1 mb-4">
      <div>
        <span class="font-semibold">{{ $post->user->name }}</span>
        @if (Route::is('dashboard'))
          <span class="text-ink-muted text-caption md:text-body-sm"> • {{ $post->club->name }}</span>
        @endif
      </div>
      <span class="text-caption text-ink-muted">{{ $post->created_at->diffForHumans() }}</span>
    </div>
  </div>
  <div class="w-full ml-4">
    <div>
      <p class="text-ink text-body-sm md:text-body-md mb-2">{{ $post->content }}</p>
      @if ($post->media_url)
        <img src="{{ $post->media_url }}" class="w-full rounded-lg max-w-100 overflow-hidden" alt="">
      @endif
      <div class="flex flex-row gap-6 mt-4">
        <div class="flex flex-row gap-1 items-center">
          <button class="cursor-pointer" aria-label="like">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-heart-icon lucide-heart">
              <path
                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
            </svg>
          </button>
          <span>12</span>
        </div>
        <div class="flex flex-row gap-1 items-center">
          <button class="cursor-pointer" aria-label="comment">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-message-circle-icon lucide-message-circle">
              <path
                d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719" />
            </svg>
          </button>
          <span>8</span>
        </div>
      </div>
    </div>
  </div>
</div>
