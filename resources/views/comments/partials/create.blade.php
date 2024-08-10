{{-- resources/views/comments/create.blade.php --}}

<div class="container">
    <h1>Create a New Comment</h1>
    <form action="{{ route('comment.store', ['type' => $type, 'id' => $id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="comment_text">Title</label>
            <input type="text" class="form-control" id="comment_title" name="comment_title" required>
            <label for="comment_text">Comment</label>
            <textarea class="form-control" id="comment_text" name="comment_text" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
