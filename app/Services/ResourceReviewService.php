<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use App\Traits\HandlesResourceReviewJoins;

class ResourceReviewService
{
    use HandlesResourceReviewJoins;

    public function applyRatingFilter(Builder $query, string $field, int $minRating): Builder
    {
        if (! in_array($field, $this->getAllowedReviewFields(), true)) {
            throw new InvalidArgumentException("Invalid rating field “{$field}”.");
        }

        if ($minRating < 1 || $minRating > 5) {
            throw new InvalidArgumentException("Rating must be between 1 and 5.");
        }

        $query = $this->ensureReviewSummaryJoined($query);
        $reviewTable = $this->getReviewSummaryTable();

        return $query->whereRaw(
            "{$reviewTable}.{$field} >= ? * {$reviewTable}.review_count",
            [$minRating]
        );
    }
}

