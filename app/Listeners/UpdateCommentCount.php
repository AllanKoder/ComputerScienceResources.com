<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\CommentsCount;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCommentsCount
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        Log::debug("Handling comment created: " . json_encode($event));

        $commentsCount = CommentsCount::firstOrNew(
            [
                'commentable_type' => $event->commentable_type, 
                'commentable_id' => $event->commentable_id
            ]
        );
        
        // Add 1
        $commentsCount->count = $commentsCount->count + 1;

        $commentsCount->save();
    }
}
