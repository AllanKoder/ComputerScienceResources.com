<?php

namespace App\Services;

use App\Models\Comment;
use App\Helpers\TypeHelper;
use App\Models\CommentHierarchy;

class CommentService
{
    public function createCommentHead(array $data, $commentableType, int $commentableId)
    {
        $commentable = $commentableType::findOrFail($commentableId);
        $comment = new Comment($data);
        $comment->user_id = auth()->id();
        $commentable->comments()->save($comment);

        CommentHierarchy::create([
            'ancestor' => $comment->id,
            'comment_id' => $comment->id,
            'depth' => 1,
        ]);

        return $comment;
    }

    public function createReply(array $data, $commentId)
    {
        // Find comment depth
        $parentComment = CommentHierarchy::where('comment_id', $commentId)->first();
        $currentDepth = $parentComment->depth;
        $maxDepth = config('comments')["maximum_depth"];
        // Find the ancestor ID of the commentable item
        $ancestorID = $parentComment->ancestor;
        
        if ($currentDepth >= $maxDepth)
        {
            return false;
        }
        \Log::debug('adding reply with current depth of ' . $currentDepth . ' with a maximum of ' . $maxDepth);
        // Create the comment
        $reply = new Comment($data);
        $reply->user_id = auth()->id();
        $reply->commentable_id = $commentId; // Set the parent comment ID
        $reply->commentable_type = Comment::class; // Set the parent comment type
        $reply->save();
        
        // Create a new closure entry with the found ancestor ID
        CommentHierarchy::create([
            'ancestor' => $ancestorID,
            'comment_id' => $reply->id,
            'depth' => $currentDepth+1,
        ]);
        \Log::debug('Created closure with ancestor: ' . $ancestorID . ' id: ' . $reply->id);
        
        return $reply;
    }
}