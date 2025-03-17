<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\CommentsCount;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateCommentCount
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

        $commentCount = CommentsCount::firstOrNew(
            [
                'commentable_type' => $event->commentable_type, 
                'commentable_id' => $event->commentable_id
            ]
        );
        
        // Add 1
        $commentCount->count = $commentCount->count + 1;

        $commentCount->save();
    }
}
