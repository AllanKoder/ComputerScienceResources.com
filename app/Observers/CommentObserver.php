<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\CommentsCount;
use App\Models\UpvoteSummary;
use Illuminate\Support\Facades\Log;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        Log::debug('Handling comment created', [
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
        ]);

        // Create the upvotes summary
        UpvoteSummary::create([
            'upvotable_id' => $comment->id,
            'upvotable_type' => Comment::class,
        ]);

        $commentsCount = CommentsCount::firstOrNew(
            [
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id,
            ]
        );

        // Add 1 to the count (handle null case)
        $commentsCount->count = ($commentsCount->count ?? 0) + 1;

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
        Log::debug('Handling comment deleted', [
            'comment_id' => $comment->id,
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
        ]);

        // Decrease the comment count
        $commentsCount = CommentsCount::where([
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
        ])->first();

        if ($commentsCount) {
            $commentsCount->count = max(0, ($commentsCount->count ?? 1) - 1);
            $commentsCount->save();
        }
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
