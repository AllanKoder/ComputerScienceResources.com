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
            'resource_id' => $event->resource_id,
            'old_review' => $event->oldReview,
            'new_review' => $event->newReview,
        ]);

        if ($event->oldReview == null && $event->newReview == null) {
            Log::critical('Update Resource Review Summary Listener reached impossible condition', [
                'resource_id' => $event->resource_id,
                'error' => 'Both oldReview and newReview are null',
            ]);

            return;
        }

        $reviewSummary = ResourceReviewSummary::where(
            'computer_science_resource_id',
            $event->resource_id
        )->first();

        if (! $reviewSummary) {
            // The resource review summary is created by the resource observer
            return;
        }

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
            $reviewSummary->$field += ($new - $old);
        }

        $reviewSummary->review_count = $reviewSummary->review_count ?? 0;
        if ($event->oldReview === null) {
            $reviewSummary->review_count++;
        } elseif ($event->newReview === null) {
            $reviewSummary->review_count--;
        }

        $reviewSummary->save();
    }
}
