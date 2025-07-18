<?php

namespace App\Traits;

use App\Models\ResourceReviewSummary;
use Illuminate\Database\Eloquent\Builder;

trait HandlesResourceReviewJoins
{
    protected function ensureReviewSummaryJoined(Builder $query): Builder
    {
        $reviewTable = (new ResourceReviewSummary)->getTable();

        $joins = $query->getQuery()->joins ?? [];

        $already = collect($joins)->contains(function ($join) use ($reviewTable) {
            return $join->table === $reviewTable;
        });

        if (! $already) {
            $resourceTable = $query->getModel()->getTable();
            $query->leftJoin(
                $reviewTable,
                "{$reviewTable}.computer_science_resource_id",
                '=',
                "{$resourceTable}.id"
            );
        }

        return $query;
    }

    protected function getReviewSummaryTable(): string
    {
        return (new ResourceReviewSummary)->getTable();
    }

    protected function getAllowedReviewFields(): array
    {
        return [
            'community',
            'teaching_clarity',
            'engagement',
            'practicality',
            'user_friendliness',
            'updates',
            'overall',
        ];
    }
}
