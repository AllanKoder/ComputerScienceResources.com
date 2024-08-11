@php
    $maxDepth = 10; // Set the maximum depth for recursion
@endphp

<div class="card mb-3 {{ $comment->commentable_id ? 'ml-10' : 'ml-0' }}">
    <p class="p-3 font-bold">{{ $comment->total_votes ?? 0 }}</p>
    <div class="card-header">
        {{ $comment->user->name }}
    </div>
    <div class="card-body">
        <p class="card-text bg-fuchsia-400">{{ $comment->comment_text }}</p>
        <!-- Upvote and Downvote buttons -->
        <form action="{{ route('votes.vote', ['type' => 'comment', 'id' => $comment->id]) }}" method="POST">
            @csrf
            <button type="submit" name="vote_value" value="1" class="bg-blue-500 text-white px-4 py-2 rounded">Upvote</button>
            <button type="submit" name="vote_value" value="-1" class="bg-red-500 text-white px-4 py-2 rounded">Downvote</button>
        </form>

        <!-- Display total votes -->
        <div id="total-votes">
            Total Votes: <span>{{ $comment->total_votes ?? 0 }}</span>
        </div>

        @if(\Auth::id() == $comment->user_id)
            {{-- <form action="{{ route('comments.partials.destroy', $comment) }}" method="POST" style="display: inline-block;" class="bg-red-400">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form> --}}
        @endif
    </div>
    {{$comment->id}}
    <div class="">
        @include('comments.partials.reply', ['comment' => $comment])
    </div>

    <div class="">
        {{-- @include('reports.partials.create', ['type' => 'comment', 'id' => $comment->id]) --}}
    </div>
    <div class="card-footer text-muted">
        Posted {{ $comment->created_at->diffForHumans() }}
    </div>
    @if($comment->comments && $comment->comments->isNotEmpty() && $depth < $maxDepth)
        <div class="ml-10">
            @foreach ($comment->comments as $reply)
                @include('comments.partials.show', ['comment' => $reply, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>
