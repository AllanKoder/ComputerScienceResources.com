{{-- resources/views/comments/reply.blade.php --}}

{{-- Reply form --}}
<form action="{{ route('comment.reply', ['comment' => $comment->id]) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="reply_text">Your Reply</label>
        <textarea class="form-control" id="reply_text" name="comment_text" rows="2" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Post Reply</button>
</form>
