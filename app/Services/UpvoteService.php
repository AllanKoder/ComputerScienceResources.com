<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;

class UpvoteService
{
    public function applySort(Builder $query, string $sortBy, string $modelClass): Builder
    {
        $table = $query->getModel()->getTable();

        switch ($sortBy) {
            case 'bottom':
                $this->joinUpvoteSummary($query, $modelClass)
                    ->orderByRaw('COALESCE(upvote_summaries.upvotes, 0) - COALESCE(upvote_summaries.downvotes, 0) ASC');
                break;

            case 'controversial':
                $this->joinUpvoteSummary($query, $modelClass)
                    ->orderByRaw('
                        (COALESCE(upvote_summaries.upvotes, 0) + COALESCE(upvote_summaries.downvotes, 0)) -
                        ABS(COALESCE(upvote_summaries.upvotes, 0) - COALESCE(upvote_summaries.downvotes, 0)) ASC
                     ');
                break;

            case 'mine':
                $query->where("{$table}.user_id", Auth::id())
                    ->orderBy("{$table}.created_at", 'desc');
                break;

            case 'latest':
                $query->orderBy("{$table}.created_at", 'desc');
                break;

            case 'top':
            default:
                $this->joinUpvoteSummary($query, $modelClass)
                    ->orderByRaw('COALESCE(upvote_summaries.upvotes, 0) - COALESCE(upvote_summaries.downvotes, 0) DESC');
                break;
        }

        return $query;
    }
    protected function joinUpvoteSummary(Builder $query, string $modelClass): Builder
    {
        $table = $query->getModel()->getTable();

        $query->leftJoin('upvote_summaries', function (JoinClause $join) use ($modelClass, $table) {
            $join->on('upvote_summaries.upvotable_id', '=', "{$table}.id")
                ->where('upvote_summaries.upvotable_type', '=', $modelClass);
        });

        // Joins would include all fields from all joined tables,
        // But, we only want the original model
        $query->select("{$table}.*");

        return $query;
    }
}
