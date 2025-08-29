<?php

namespace App\Observers;

use App\Events\ResourceReviewProcessed;
use App\Models\ResourceReview;

class ResourceReviewObserver
{
    /**
     * Handle the ResourceReview "created" event.
     */
    public function created(ResourceReview $resourceReview): void
    {
        ResourceReviewProcessed::dispatch(
            $resourceReview->computer_science_resource_id,
            null,
            $resourceReview->attributesToArray()
        );
    }

    /**
     * Handle the ResourceReview "updated" event.
     */
    public function updated(ResourceReview $resourceReview): void
    {
        ResourceReviewProcessed::dispatch(
            $resourceReview->computer_science_resource_id,
            $resourceReview->getOriginal(),
            $resourceReview->attributesToArray()
        );
    }

    /**
     * Handle the ResourceReview "deleting" event.
     */
    public function deleting(ResourceReview $resourceReview): void
    {
        ResourceReviewProcessed::dispatch(
            $resourceReview->computer_science_resource_id,
            $resourceReview->attributesToArray(),
            null,
        );
    }

    /**
     * Handle the ResourceReview "restored" event.
     */
    public function restored(ResourceReview $resourceReview): void
    {
        //
    }

    /**
     * Handle the ResourceReview "force deleted" event.
     */
    public function forceDeleted(ResourceReview $resourceReview): void
    {
        //
    }
}
