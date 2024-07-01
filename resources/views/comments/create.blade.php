{{-- resources/views/comments/create.blade.php --}}

<div class="container">
    <h1>Create a New Comment</h1>
    <form action="{{ route('comment.comment', ['resource' => $resource, 'id' => $id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="comment_text">Comment</label>
            <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
