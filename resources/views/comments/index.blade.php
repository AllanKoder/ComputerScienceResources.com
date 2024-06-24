{{-- resources/views/comments/index.blade.php --}}

<div class="container" id="comments-container">
    @foreach ($comments as $comment)
        <div class="card mb-3 ml-10">
            <div class="card-header">
                {{ $comment->user->name }}
            </div>
            <div class="card-body">
                <p class="card-text bg-fuchsia-400">{{ $comment->comment_text }}</p>
                <form action="{{ route('comment.destroy', $comment) }}" method="POST" hx-post="{{ route('comment.destroy', $comment) }}" hx-target="#comments-container" hx-swap="outerHTML" style="display: inline-block;" class="bg-red-400">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <div class="card-footer text-muted">
                Posted {{ $comment->created_at->diffForHumans() }}
            </div>
            @if($comment->replies->isNotEmpty())
                @include('comments.index', ['comments' => $comment->replies])
            @endif
            @include('comments.reply', array('parentComment'=>$comment))
        </div>
    @endforeach
</div>
