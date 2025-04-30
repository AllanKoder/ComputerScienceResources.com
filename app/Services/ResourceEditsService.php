<?php

namespace App\Services;

use App\Models\ResourceEdits;

class ResourceEditsService
{
    /**
     * Determines the amount of votes needed to merge the resource edit into the 
     * 
     * Policy: The policy is roughly the formula of: 
     * min(log(total votes of resource)/log(1.25) + 1, -- Log dropoff 
     * total votes of resource) -- Dont want to require more votes than the resource's votes
     * )
     */
    public function requiredVotes(int $totalVotes): int
    {
        // Either the current votes, or the log equation
        $votes = min($totalVotes,
            floor(log($totalVotes, 1.25)) + 1
        );
        return max(3, $votes); // Need to be 3 votes minimum
    }

    /**
     * Handles determining if a resource edit is mergeable, by getting the upvotes for the resource edit
     * 
     */
    public function canMergeEdits(ResourceEdits $edits) : bool
    {
        if (app()->isLocal()) {
            return true;
        }

        $totalVotes = $edits->resource->votes_count;
        $neededApprovals = $this->requiredVotes($totalVotes);
        
        $approvals = $edits->vote_score;

        return $approvals >= $neededApprovals;
    }
}
