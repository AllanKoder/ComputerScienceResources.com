<?php

namespace App\Services;

use App\Models\ResourceEdits;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PDO;

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
    public function requiredVotes(int $vote_score)
    {
        // So little votes, so we only need 1 vote to approve
        if ($vote_score <= 1) return 1;

        // Dropoff
        return min($vote_score,
            log($vote_score, 1.25) + 1
        );
    }

    /**
     * Handles determining if a resource edit is mergeable, by getting the upvotes for the resource edit
     * 
     */
    public function canMergeEdits(ResourceEdits $edits)
    {
       $vote_score = $edits->resource->vote_score;
        $approvals = $edits->upvoteSummary
    }

}
