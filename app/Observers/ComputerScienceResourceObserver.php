<?php

namespace App\Observers;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\TagFrequency;
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

    }

    /**
     * Handle the ComputerScienceResource "restored" event.
     */
    public function restored(ComputerScienceResource $computerScienceResource): void
    {
        //
    }

    public function deleting(ComputerScienceResource $computerScienceResource): void
    {
        $computerScienceResource->topic_tags = [];
        $computerScienceResource->programming_language_tags = [];
        $computerScienceResource->general_tags = [];
    }

    /**
     * Handle the ComputerScienceResource "force deleted" event.
     */
    public function forceDeleted(ComputerScienceResource $computerScienceResource): void
    {
        // Delete the image
        if ($computerScienceResource->image_path) {
            Storage::disk('public')->delete($computerScienceResource->image_path);
        }
    }
}
