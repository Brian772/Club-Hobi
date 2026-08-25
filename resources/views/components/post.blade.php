<div class="flex flex-row mb-4 pb-2 border-b border-hairline">
  <div class="min-w-10">
    <img src="{{ $post->user->avatar_full_url }}" alt="{{ $post->user->name }}" width="40px" height="40px"
      class="w-10 h-10 rounded-full object-cover mb-2">
  </div>
  <div class="w-full ml-2">
    <div class="flex flex-row w-full items-center justify-between mb-2">
      <span class="font-semibold">{{ $post->user->name }}</span>
      <span class="text-caption text-ink-muted">{{ $post->created_at->diffForHumans() }}</span>
    </div>
    <div>
      <p class="text-ink text-body-sm md:text-body-md mb-2">{{ $post->content }}</p>
      @if ($post->media_url)
        <img src="{{ $post->media_url }}" class="w-full rounded-lg max-w-100" alt="">
      @endif
      <div class="flex flex-row gap-4 mt-2">
        <div class="flex flex-row gap-2 items-center">
          <button class="fas fa-heart text-ink-muted text-caption cursor-pointer" aria-label="like"></button>
          <span>12</span>
        </div>
        <div class="flex flex-row gap-2 items-center">
          <button class="fas fa-comment text-ink-muted text-caption cursor-pointer" aria-label="comment"></button>
          <span>8</span>
        </div>
      </div>
    </div>
  </div>
</div>
