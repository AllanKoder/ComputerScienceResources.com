<?php

namespace App\Observers;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\UpvoteSummary;

class ComputerScienceResourceObserver
{
    /**
     * Handle the ComputerScienceResource "created" event.
     */
    public function created(ComputerScienceResource $computerScienceResource): void
    {
        // Create the upvotes summary
        UpvoteSummary::create([
            'upvotable_id' => $computerScienceResource->id,
            'upvotable_type' => ComputerScienceResource::class,
        ]);

        // TagFrequencyChanged is in store ComputerScienceResource controller
    }

    /**
     * Handle the ComputerScienceResource "updated" event.
     */
    public function updated(ComputerScienceResource $computerScienceResource): void
    {
        //
    }

    /**
     * Handle the ComputerScienceResource "deleted" event.
     */
    public function deleted(ComputerScienceResource $computerScienceResource): void
    {
        //
    }

    /**
     * Handle the ComputerScienceResource "restored" event.
     */
    public function restored(ComputerScienceResource $computerScienceResource): void
    {
        //
    }

    /**
     * Handle the ComputerScienceResource "force deleted" event.
     */
    public function forceDeleted(ComputerScienceResource $computerScienceResource): void
    {
        //
    }
}
