<?php

namespace App\Services;

use App\Models\ResourceEdit;
use App\Models\Resource;
use App\Models\ProposedEdit;
use App\Models\VoteTotal;
use Illuminate\Support\Facades\Log;

class ResourceEditService
{
    public function createResourceEdit($validatedData, $resourceId)
    {
        $validatedData['resource_id'] = $resourceId;
        $validatedData['user_id'] = \Auth::id();

        $originalResource = Resource::where('id', $resourceId)->with('tags')->first();
        $resourceEdit = ResourceEdit::create($validatedData);
        $changesDetected = false;

        foreach ($validatedData as $field => $value) {
            if (in_array($field, Resource::getResourceAttributes()) && $originalResource->$field != $value) {
                ProposedEdit::create([
                    'resource_edit_id' => $resourceEdit->id,
                    'field_name' => $field,
                    'new_value' => json_encode($value),
                ]);
                $changesDetected = true;
            }
        }

        if (!$changesDetected) {
            // No changes were made, delete the model
            $resourceEdit->delete();
            return false;
        }

        return $resourceEdit;
    }

    public function getNewResourceFromEdits(ResourceEdit $resourceEdit)
    {
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();
        return (object) array_merge($resourceEdit->resource->toArray(), $proposedEditsArray);
    }

    public function canMergeApprovals(ResourceEdit $resourceEdit)
    {
        $totalUpvotesResource = VoteTotal::getVotesTotalModel($resourceEdit->resource->id, Resource::class);
        $totalVotesResource = $totalUpvotesResource?->total_votes ?? 0;

        $totalUpvotesEdit = VoteTotal::getVotesTotalModel($resourceEdit->id, ResourceEdit::class);
        $totalVotesEdit = $totalUpvotesEdit?->total_votes ?? 0;

        $requiredApprovals = config("approvalscores");

        foreach (array_reverse($requiredApprovals, true) as $resourceUpvotes => $requiredEditUpvotes) {
            if ($totalVotesResource >= $resourceUpvotes && $totalVotesEdit >= $requiredEditUpvotes) {
                return true;
            }
        }
        return false;
    }

    public function canMergeTime(ResourceEdit $resourceEdit)
    {
        # must wait 24 hours
        return now()->diffInHours($resourceEdit->created_at) >= 24;
    }

    public function mergeResourceEdit(ResourceEdit $resourceEdit)
    {
        //Approve merging the resource
        $proposedEditsArray = $resourceEdit->getProposedEditsArray();
        $resource = $resourceEdit->resource;

        // Get the fillable attributes
        $fillableAttributes = $resource->getFillable();
        $mutatorAttributes = [];

        // set the fillable attributes
        foreach ($proposedEditsArray as $attribute => $editedValue) {
            if (in_array($attribute, $fillableAttributes)) {
                $resource->$attribute = $editedValue;
            } else {
                $mutatorAttributes[$attribute] = $editedValue;
            }
        }
        // Save the resource without mutators (UPDATE sql)
        $resource->save();

        // Handle mutator attributes separately to trigger the mutators
        foreach ($mutatorAttributes as $attribute => $editedValue) {
            \Log::debug("mutator " . json_encode($attribute) . " to new value " . json_encode($editedValue));
            $resource->$attribute = $editedValue;
        }

        $resourceEdit->delete();
        
        return true;
    }
}