<?php

namespace App\Services\SortingManagers;

use App\SortingStrategies\DateSortingStrategy;
use App\SortingStrategies\ResourceReviewsSortingStrategy;
use App\SortingStrategies\VoteSortingStrategy;

class ResourceSortingManager extends SortingManager
{
    protected array $strategies = [
        DateSortingStrategy::class,
        VoteSortingStrategy::class,
        ResourceReviewsSortingStrategy::class,
    ];
}
