{{-- resources/views/comments/index.blade.php --}}

<div class="container" id="comments-container">
    @foreach ($comments as $comment)
        <div class="card mb-3 ml-10">
            <p class="p-3 font-bold"> {{$comment->upvotes }} </p>
            <div class="card-header">
                {{ $comment->user->name }}
            </div>
            <div class="card-body">
                <p class="card-text bg-fuchsia-400">{{ $comment->comment_text }}</p>
                <!-- Upvote and Downvote buttons -->
                <form action="{{ route('votes.vote', ['type'=>'comment', 'id'=>$comment->id]) }}" method="POST">
                    @csrf
                    <button type="submit" name="vote_value" value="1" class="bg-blue-500 text-white px-4 py-2 rounded">Upvote</button>
                    <button type="submit" name="vote_value" value="-1" class="bg-red-500 text-white px-4 py-2 rounded">Downvote</button>
                </form>

                <!-- Display total votes -->
                <div id="total-votes">
                    Total Votes: <span>{{ $comment->total_votes ?? 0 }}</span>
                </div


                @if(auth()->id() == $comment->user_id)
                    <form action="{{ route('comment.destroy', $comment) }}" method="POST" style="display: inline-block;" class="bg-red-400">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                @endif
            </div>

            <div class="">
                @include('comments.reply', array('parentComment'=>$comment))
            </div>

            <div class="">
                @include('reports.create', array('type'=>'comment', 'id'=>$comment->id))
            </div>
            <div class="card-footer text-muted">
                Posted {{ $comment->created_at->diffForHumans() }}
            </div>
            @if($comment->replies->isNotEmpty())
                @include('comments.index', ['comments' => $comment->replies])
            @endif
        </div>
    @endforeach
</div>
