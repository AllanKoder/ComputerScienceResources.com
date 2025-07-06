<?php

namespace App\Observers;

use App\Models\ResourceEdits;
use App\Models\UpvoteSummary;

class ResourceEditsObserver
{
    /**
     * Handle the ResourceEdits "created" event.
     */
    public function created(ResourceEdits $resourceEdits): void
    {
        // Create the upvotes summary
        UpvoteSummary::create([
            'upvotable_id' => $resourceEdits->id,
            'upvotable_type' => ResourceEdits::class,
        ]);
    }

    /**
     * Handle the ResourceEdits "updated" event.
     */
    public function updated(ResourceEdits $resourceEdits): void
    {
        //
    }

    /**
     * Handle the ResourceEdits "deleted" event.
     */
    public function deleted(ResourceEdits $resourceEdits): void
    {
        //
    }

    /**
     * Handle the ResourceEdits "restored" event.
     */
    public function restored(ResourceEdits $resourceEdits): void
    {
        //
    }

    /**
     * Handle the ResourceEdits "force deleted" event.
     */
    public function forceDeleted(ResourceEdits $resourceEdits): void
    {
        //
    }
}
