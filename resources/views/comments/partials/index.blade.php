<div class="container" id="comments-container">
    @foreach ($comments as $comment)
        @include('comments.partials.show', ['comment' => $comment, 'depth' => 0])
    @endforeach
</div>
