<?php

namespace App\SortingStrategies;

use App\Contracts\SortingStrategy;
use Illuminate\Database\Eloquent\Builder;

class DateSortingStrategy implements SortingStrategy
{
    public static function supports(string $sortBy): bool
    {
        return in_array($sortBy, ['latest', 'oldest', 'recently_updated']);
    }

    public static function apply(Builder $query, string $sortBy): Builder
    {
        switch ($sortBy) {
            case 'latest':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'ASC');
                break;
            case 'recently_updated':
                $query->orderBy('updated_at', 'DESC');
                break;
        }

        return $query;
    }
}
