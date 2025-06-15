<?php

namespace App\SortingStrategies;

use App\Contracts\SortingStrategy;
use Illuminate\Database\Eloquent\Builder;

class VoteSortingStrategy implements SortingStrategy
{
    public static function supports(string $sortBy): bool
    {
        return in_array($sortBy, ['top', 'bottom', 'controversial', 'total_votes', 'hot']);
    }

    public static function apply(Builder $query, string $sortBy): Builder
    {
        $table = $query->getModel()->getTable();
        $modelClass = get_class($query->getModel());

        // Join on polymorphic relationship
        $query->join('upvote_summaries', function ($join) use ($table, $modelClass) {
            $join->on('upvote_summaries.upvotable_id', '=', "{$table}.id")
                 ->where('upvote_summaries.upvotable_type', '=', $modelClass);
        })->select("{$table}.*");

        switch ($sortBy) {
            case 'top':
                $query->orderBy('upvote_summaries.score', 'DESC');
                break;

            case 'bottom':
                $query->orderBy('upvote_summaries.score', 'ASC');
                break;

            case 'controversial':
                $query->orderBy('upvote_summaries.controversy', 'DESC');
                break;

            case 'total_votes':
                $query->orderBy('upvote_summaries.total_votes', 'DESC');
                break;

            case 'hot':
                $query->orderByRaw("
                    (upvote_summaries.score) /
                    POWER(TIMESTAMPDIFF(HOUR, {$table}.created_at, NOW()) + 2, 1.5) DESC
                ");
                break;
        }

        return $query;
    }
}
