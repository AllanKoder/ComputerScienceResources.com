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
        Log::debug("Handling ResourceReviewProcessed", [
            'resource_id' => $event->resource,
            'has_old_review' => $event->oldReview !== null,
            'has_new_review' => $event->newReview !== null,
            'old_review_id' => $event->oldReview?->id,
            'new_review_id' => $event->newReview?->id
        ]);

        if ($event->oldReview == null && $event->newReview == null) {
            Log::critical("Update Resource Review Summary Listener reached impossible condition", [
                'resource_id' => $event->resource,
                'error' => 'Both oldReview and newReview are null'
            ]);
            return;
        }

        $review_summary = ResourceReviewSummary::firstOrNew(
            ['computer_science_resource_id' => $event->resource],
        );

        $fields = [
            'community',
            'teaching_clarity',
            'engagement',
            'practicality',
            'user_friendliness',
            'updates',
        ];

        foreach ($fields as $field) {
            $old = $event->oldReview[$field] ?? 0;
            $new = $event->newReview[$field] ?? 0;
            $review_summary->$field += ($new - $old);
        }

        if ($event->oldReview === null) {
            // It's a new review, so increase the count
            $review_summary->review_count += 1;
        }

        $review_summary->save();
    }
}
