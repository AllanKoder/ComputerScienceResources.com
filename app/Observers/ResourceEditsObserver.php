<?php

namespace App\Observers;

use App\Models\ResourceEdits;

class ResourceEditsObserver
{
    /**
     * Handle the ResourceEdits "created" event.
     */
    public function created(ResourceEdits $resourceEdits): void
    {

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
