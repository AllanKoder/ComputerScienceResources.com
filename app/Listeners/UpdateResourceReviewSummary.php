<?php

namespace App\Listeners;

use App\Events\ResourceReviewProcessed;
use App\Models\ResourceReviewSummary;
use Illuminate\Support\Facades\Log;

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
        Log::debug('Handling ResourceReviewProcessed', [
            'resource_id' => $event->resource,
            'old_review' => $event->oldReview,
            'new_review' => $event->newReview,
        ]);

        if ($event->oldReview == null && $event->newReview == null) {
            Log::critical('Update Resource Review Summary Listener reached impossible condition', [
                'resource_id' => $event->resource,
                'error' => 'Both oldReview and newReview are null',
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
