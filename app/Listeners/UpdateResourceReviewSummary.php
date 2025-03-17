<?php

namespace App\Listeners;

use App\Events\ResourceReviewProcessed;
use App\Models\ResourceReviewSummary;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateResourceReviewSummary
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
    public function handle(ResourceReviewProcessed $event): void
    {
        Log::debug("Handling ResourceReviewProcessed: " . json_encode($event));

        if ($event->oldReview == null && $event->newReview == null) {
            Log::critical("Update Resource Review Summary Listener reached impossible condition, null oldReview and null newReview");
            return;
        }

        // We are creating a review
        else if ($event->oldReview == null && $event->newReview) {
            $review_summary = ResourceReviewSummary::firstOrNew(
                ['computer_science_resource_id' => $event->resource],
            );

            $review_summary->community = $review_summary->community + $event->newReview['community'];
            $review_summary->teaching_clarity = $review_summary->teaching_clarity + $event->newReview['teaching_clarity'];
            $review_summary->engagement = $review_summary->engagement + $event->newReview['engagement'];
            $review_summary->practicality = $review_summary->practicality + $event->newReview['practicality'];
            $review_summary->user_friendliness = $review_summary->user_friendliness + $event->newReview['user_friendliness'];
            $review_summary->updates = $review_summary->updates + $event->newReview['updates'];

            $review_summary->review_count = $review_summary->review_count + 1;
            $review_summary->save();

            Log::debug("Saved the update for the review summary: " . json_encode($review_summary));
            return;
        }

        // Implement more logic when needed

    }
}
