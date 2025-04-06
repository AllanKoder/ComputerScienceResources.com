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
        // So little votes, so we only need 1 vote to approve
        if ($totalVotes <= 1) return 1;

        // Dropoff
        return min($totalVotes,
            log($totalVotes, 1.25) + 1
        );
    }

    /**
     * Handles determining if a resource edit is mergeable, by getting the upvotes for the resource edit
     * 
     */
    public function canMergeEdits(ResourceEdits $edits) : bool
    {
        $totalVotes = $edits->resource->votes_count;
        $neededApprovals = $this->requiredVotes($totalVotes);
        
        $approvals = $edits->vote_score;

        //return $approvals >= $neededApprovals;
        return true;
    }
}
