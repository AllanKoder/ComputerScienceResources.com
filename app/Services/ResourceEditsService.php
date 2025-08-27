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
        if ($totalVotes == 0) return 1;
        // Take the minimum of total votes OR the logarithmic calculation
        $votes = min($totalVotes, floor(log($totalVotes, 1.25)) + 1);

        // Ensure minimum of 3 votes is always required
        return max(3, $votes);
    }

    /**
     * Handles determining if a resource edit is mergeable, by getting the upvotes for the resource edit
     */
    public function canMergeEdits(ResourceEdits $edits): bool
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
