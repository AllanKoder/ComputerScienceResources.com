<?php

namespace App\Services\SortingManagers;

use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Reverses the direction (ASC <-> DESC) of the *last* order clause in the query.
     */
    public function reverse(Builder $query): Builder
    {
        $orders = $query->getQuery()->orders;

        if (empty($orders)) {
            return $query;
        }

        $lastOrder = array_pop($orders);

        if (isset($lastOrder['direction'])) {
            $lastOrder['direction'] = strtolower($lastOrder['direction']) === 'asc' ? 'desc' : 'asc';
        }

        $orders[] = $lastOrder;
        $query->getQuery()->orders = $orders;

        return $query;
    }
}
