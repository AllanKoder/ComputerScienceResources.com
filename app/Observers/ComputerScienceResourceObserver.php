<?php

namespace App\Observers;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use Illuminate\Support\Facades\Storage;

class ComputerScienceResourceObserver
{
    /**
     * Handle the ComputerScienceResource "created" event.
     */
    public function created(ComputerScienceResource $computerScienceResource): void
    {
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
        // Delete the image
        if ($computerScienceResource->image_path) {
            Storage::disk('public')->delete($computerScienceResource->image_path);
        }
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
