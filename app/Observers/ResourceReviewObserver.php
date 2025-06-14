<?php

namespace App\Observers;

use App\Models\ResourceReview;

class ResourceReviewObserver
{
    /**
     * Handle the ResourceReview "created" event.
     */
    public function created(ResourceReview $resourceReview): void
    {
        //
    }

    /**
     * Handle the ResourceReview "updated" event.
     */
    public function updated(ResourceReview $resourceReview): void
    {
        //
    }

    /**
     * Handle the ResourceReview "deleted" event.
     */
    public function deleted(ResourceReview $resourceReview): void
    {
        //
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
