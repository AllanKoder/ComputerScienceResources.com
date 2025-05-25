<?php

namespace App\SortingStrategies;

use App\Contracts\SortingStrategy;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HandlesResourceReviewJoins;

class ResourceReviewsSortingStrategy implements SortingStrategy
{
    use HandlesResourceReviewJoins;

    public static function supports(string $sortBy): bool
    {
        return in_array($sortBy, (new self)->getAllowedReviewFields(), true);
    }

    public static function apply(Builder $query, string $sortBy): Builder
    {
        $instance = new self();
        $resourceTable = $query->getModel()->getTable();
        $query = $instance->ensureReviewSummaryJoined($query);
        $reviewTable = $instance->getReviewSummaryTable();

        return $query
            ->orderByDesc("{$reviewTable}.{$sortBy}_rating")
            ->addSelect("{$resourceTable}.*");
    }
}
