<?php

namespace App\Services\SortingManagers;

use App\SortingStrategies\DateSortingStrategy;
use App\SortingStrategies\VoteSortingStrategy;

class GeneralVotesSortingManager extends SortingManager
{
    protected array $strategies = [DateSortingStrategy::class, VoteSortingStrategy::class];
}
