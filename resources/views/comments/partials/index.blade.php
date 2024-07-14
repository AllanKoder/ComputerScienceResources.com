<div class="container" id="comments-container">
    @if(count($comments) > 0)
        @foreach ($comments as $comment)
            @include('comments.partials.show', ['comment' => $comment, 'depth' => 0])
        @endforeach
    @else
        <p> There are no comments </p>
    @endif
</div>
