<?php

namespace App\Services\SortingManagers;

use Illuminate\Database\Eloquent\Builder;
use App\Contracts\SortStrategy;

class SortingManager
{
    // Array of SortStrategy
    protected array $strategies = [];

    public function applySort(Builder $query, string $sortBy): Builder
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy::supports($sortBy)) {
                return $strategy::apply($query, $sortBy);
            }
        }

        return $query; // fallback: no sort applied
    }
}
