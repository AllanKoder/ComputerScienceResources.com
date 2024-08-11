<?php

namespace App\Services;

use App\Models\Comment;
use App\Helpers\TypeHelper;
use App\Models\CommentHierarchy;

class CommentService
{
    public function createCommentHead(array $data, $commentable_type, int $commentable_id)
    {
        $commentable = $commentable_type::findOrFail($commentable_id);
        $comment = new Comment($data);
        $comment->user_id = auth()->id();
        $commentable->comments()->save($comment);

        CommentHierarchy::create([
            'ancestor' => $comment->id,
            'comment_id' => $comment->id,
        ]);

        return $comment;
    }

    public function createReply(array $data, $comment_id)
    {
        $reply = new Comment( $data);
        $reply->user_id = auth()->id();
        $reply->commentable_id = $comment_id; // Set the parent comment ID
        $reply->commentable_type = Comment::class; // Set the parent comment type
        $reply->save();

        // Find the ancestor ID of the commentable item
        $ancestorID = CommentHierarchy::where('comment_id', $comment_id)->first()->ancestor;
        
        // Create a new closure entry with the found ancestor ID
        CommentHierarchy::create([
            'ancestor' => $ancestorID,
            'comment_id' => $reply->id,
        ]);
        \Log::debug('Created closure with ancestor: ' . $ancestorID . ' id: ' . $reply->id);

        return $reply;
    }
}