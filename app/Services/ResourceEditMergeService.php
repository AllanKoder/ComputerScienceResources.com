<?php

namespace App\Services;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use App\Models\ResourceEdits;
use App\Http\Resources\ComputerScienceResourceResource;

class ResourceEditMergeService
{
    public function __construct(
        private DataNormalizationService $dataNormalizationService
    ) {}

    /**
     * Check if the edit data is different from the original resource
     */
    public function hasChanges(ComputerScienceResource $resource, array $editData): bool
    {
        $originalData = (new ComputerScienceResourceResource($resource))->resolve();

        // Remove edit-specific fields that don't exist in the original resource
        $cleanEditData = collect($editData)
            ->except(['edit_title', 'edit_description'])
            ->toArray();

        return !$this->dataNormalizationService->arraysAreEqual($originalData, $cleanEditData);
    }

    /**
     * Merge resource edits into the original resource
     */
    public function mergeEdits(ResourceEdits $resourceEdits): ComputerScienceResource
    {
        $resource = ComputerScienceResource::findOrFail($resourceEdits->computer_science_resource_id);
        $oldTagCounter = $resource->tagCounter();

        // Update resource fields
        $this->updateResourceFields($resource, $resourceEdits);
        $resource->save();

        // Update tags
        $this->updateResourceTags($resource, $resourceEdits);

        // Dispatch tag frequency change event
        $this->dispatchTagFrequencyEvent($oldTagCounter, $resourceEdits);

        // Clean up the edit record
        $resourceEdits->delete();

        return $resource;
    }

    /**
     * Update the resource fields from the edit
     */
    private function updateResourceFields(ComputerScienceResource $resource, ResourceEdits $resourceEdits): void
    {
        $fieldsToUpdate = [
            'name', 'description', 'image_url', 'page_url',
            'platforms', 'difficulty', 'pricing'
        ];

        foreach ($fieldsToUpdate as $field) {
            $resource->{$field} = $resourceEdits->{$field};
        }
    }

    /**
     * Update the resource tags from the edit
     */
    private function updateResourceTags(ComputerScienceResource $resource, ResourceEdits $resourceEdits): void
    {
        $resource->setAttribute('topic_tags', $resourceEdits->topic_tags);
        $resource->setAttribute('programming_language_tags', $resourceEdits->programming_language_tags);
        $resource->setAttribute('general_tags', $resourceEdits->general_tags);
    }

    /**
     * Dispatch tag frequency change event
     */
    private function dispatchTagFrequencyEvent(array $oldTagCounter, ResourceEdits $resourceEdits): void
    {
        $newTags = collect([
            $resourceEdits->topic_tags,
            $resourceEdits->programming_language_tags,
            $resourceEdits->general_tags
        ])->flatten()->countBy()->toArray();

        TagFrequencyChanged::dispatch($oldTagCounter, $newTags);
    }
}
