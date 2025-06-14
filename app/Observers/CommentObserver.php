<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\CommentsCount;
use Illuminate\Support\Facades\Log;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        Log::debug("Handling comment created", [
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id
        ]);

        $commentsCount = CommentsCount::firstOrNew(
            [
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id
            ]
        );

        // Add 1
        $commentsCount->count = $commentsCount->count + 1;

        $commentsCount->save();
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "restored" event.
     */
    public function restored(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     */
    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
