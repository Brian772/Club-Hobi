<div class="text-xs bg-neutral-50 p-2.5 rounded-lg border border-neutral-100 my-1">
    <div class="flex justify-between items-center">
        <div>
            <span class="font-bold text-neutral-900">{{ $comment->user->name ?? 'User' }}:</span>
            <span class="text-neutral-700 ml-1">{{ $comment->content }}</span>
        </div>
        <button onclick="replyComment('{{ $postId }}', '{{ $comment->id }}', '{{ $comment->user->name }}')"
            class="text-blue-600 font-semibold text-[11px] hover:underline">
            Balas
        </button>
    </div>

    {{-- Rekursif untuk memuat balasan dari balasan --}}
    @if($comment->replies && $comment->replies->count() > 0)
        <div class="ml-4 mt-2 pl-2 border-l-2 border-neutral-200 space-y-1">
            @foreach($comment->replies as $reply)
                @include('partials.comment-item', ['comment' => $reply, 'postId' => $postId])
            @endforeach
        </div>
    @endif
</div>