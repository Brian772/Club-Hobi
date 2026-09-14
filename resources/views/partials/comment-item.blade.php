<div class="flex gap-2.5 py-1.5 text-xs group" id="comment-{{ $comment->id }}">
    <img src="{{ $comment->user->avatar_full_url ?? 'https://via.placeholder.com/32' }}" 
         alt="{{ $comment->user->name ?? 'User' }}" 
         class="w-7 h-7 rounded-full object-cover shrink-0 mt-0.5">

    <div class="flex-1 min-w-0">
        <div class="leading-relaxed text-neutral-900 break-words">
            <span class="font-bold mr-1.5 hover:underline cursor-pointer">{{ $comment->user->name ?? 'User' }}</span>
            <span class="text-neutral-800 font-normal">{{ $comment->content }}</span>
        </div>

        <div class="flex items-center gap-3 mt-1 text-[11px] text-neutral-400 font-medium">
            <span>{{ $comment->created_at ? $comment->created_at->diffForHumans(null, true) : '1h' }}</span>
            
            <button type="button" 
                    onclick="replyComment('{{ $postId }}', '{{ $comment->id }}', '{{ $comment->user->name ?? 'User' }}')"
                    class="hover:text-neutral-700 font-semibold cursor-pointer">
                Balas
            </button>

            @if($comment->user_id === auth()->id())
                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Hapus komentar ini?')"
                            class="hover:text-red-500 cursor-pointer">
                        Hapus
                    </button>
                </form>
            @endif
        </div>

        @if($comment->replies && $comment->replies->count() > 0)
            <div class="mt-2 pl-3 border-l-2 border-neutral-200 space-y-2">
                @foreach($comment->replies as $reply)
                    @include('partials.comment-item', ['comment' => $reply, 'postId' => $postId])
                @endforeach
            </div>
        @endif
    </div>
</div>