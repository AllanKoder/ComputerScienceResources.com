<?php

namespace App\Listeners;

use App\Events\UpvoteProcessed;
use App\Models\UpvoteSummary;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUpvoteSummary
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
    public function handle(UpvoteProcessed $event): void
    {
        Log::debug("Handling UpvoteProcessed", [
            'upvotable_id' => $event->id,
            'upvotable_type' => $event->type,
            'previous_value' => $event->previousValue,
            'new_value' => $event->newValue
        ]);

        $summary = UpvoteSummary::firstOrNew([
            'upvotable_id' => $event->id,
            'upvotable_type' => $event->type
        ]);

        // Remove the past value
        if ($event->previousValue > 0)
        {
            $summary->upvotes -= $event->previousValue;
        }
        elseif ($event->previousValue < 0)
        {
            $summary->downvotes -= abs($event->previousValue);
        }

        // Add the new value
        if ($event->newValue > 0)
        {
            $summary->upvotes += $event->newValue;
        }
        elseif ($event->newValue < 0)
        {
            $summary->downvotes += abs($event->newValue);
        }

        // Save the updated summary
        $summary->save();
    }
}
