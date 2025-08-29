<?php

namespace App\Observers;

use App\Models\ComputerScienceResource;
use App\Models\ResourceReviewSummary;
use Illuminate\Support\Facades\Storage;

class ComputerScienceResourceObserver
{
    /**
     * Handle the ComputerScienceResource "created" event.
     */
    public function created(ComputerScienceResource $computerScienceResource): void
    {
        ResourceReviewSummary::create(
            ['computer_science_resource_id' => $computerScienceResource->id],
        );
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
    public function deleted(ComputerScienceResource $computerScienceResource): void {}

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

        // Delete the image
        if ($computerScienceResource->image_path) {
            Storage::disk('public')->delete($computerScienceResource->image_path);
        }
    }

    /**
     * Handle the ComputerScienceResource "force deleted" event.
     */
    public function forceDeleted(ComputerScienceResource $computerScienceResource): void {}
}
