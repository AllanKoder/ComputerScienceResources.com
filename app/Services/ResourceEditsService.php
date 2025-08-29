<?php

namespace App\Services;

use App\Models\ResourceEdits;
use App\Models\ComputerScienceResource;
use App\Utilities\UrlUtilities;

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
        if ($totalVotes == 0) {
            return 1;
        }
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

        $totalVotes = $edits->computerScienceResource->votes_count;
        $neededApprovals = $this->requiredVotes($totalVotes);

        $approvals = $edits->vote_score;

        return $approvals >= $neededApprovals;
    }

    /**
     * Calculate the actual differences between the proposed changes and the original resource.
     */
    public function calculateChanges(ComputerScienceResource $resource, array $proposedChanges): array
    {
        $actualChanges = [];
        $normalizedProposed = $this->normalize($proposedChanges);
        $normalizedOriginal = $this->normalize($resource->toArray());

        foreach ($normalizedProposed as $key => $value) {
            if (! array_key_exists($key, $normalizedOriginal) || $normalizedOriginal[$key] !== $value) {
                // Use the original value from the request, not the normalized one, for file uploads.
                $actualChanges[$key] = $proposedChanges[$key];
            }
        }

        return $actualChanges;
    }

    /**
     * Normalize an array by sorting keys and values for consistent comparison, including page_url normalization.
     */
    public function normalize(array $array): array
    {
        ksort($array);

        // Normalize page_url if present
        if (array_key_exists('page_url', $array) && is_string($array['page_url'])) {
            $array['page_url'] = UrlUtilities::normalize($array['page_url']);
        }

        foreach ($array as &$value) {
            if (is_array($value)) {
                sort($value);
            }
        }

        return $array;
    }
}
