{{-- resources/views/comments/index.blade.php --}}

<div class="container">
    <h1>Comments</h1>
    @foreach ($comments as $comment)
        <div class="card mb-3 ml-10">
            
            <div class="card-header">
                {{ $comment->user->name }}
            </div>
            <div class="card-body">
                <p class="card-text bg-fuchsia-400">{{ $comment->comment_text }}</p>
                {{-- <a href="{{ route('comments.edit', $comment) }}" class="btn btn-secondary">Edit</a> --}}
                <form action="{{ route('comment.destroy', $comment) }}" method="POST" style="display: inline-block;" class="bg-red-400">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
            <div class="card-footer text-muted">
                Posted {{ $comment->created_at->diffForHumans() }}
            </div>
            @foreach($comments as $comment)
                <div class="comment">
                    <p>{{ $comment->body }}</p>
                    @if($comment->replies)
                        @include('comments.index', ['comments' => $comment->replies])
                    @endif
                </div>
            @endforeach

            @include('comments.reply', array('parentComment'=>$comment))
        </div>
    @endforeach
</div>