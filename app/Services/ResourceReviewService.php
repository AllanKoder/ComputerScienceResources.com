<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use App\Models\ResourceReviewSummary;

class ResourceReviewService
{
    /**
     * These are the only rating fields we allow filtering on.
     */
    protected array $allowedFields = [
        'community',
        'teaching_clarity',
        'engagement',
        'practicality',
        'user_friendliness',
        'updates',
        // Special case
        'overall_rating',
    ];

    /**
     * Apply an average‐rating filter to a query for resources.
     *
     * @param  Builder  $query      The query on your Resource model.
     * @param  string   $field      One of the allowed rating fields.
     * @param  int      $minRating  Minimum average rating (1–5).
     * @return Builder
     *
     * @throws InvalidArgumentException
     */
    public function applyRatingFilter(Builder $query, string $field, int $minRating): Builder
    {
        if (!in_array($field, $this->allowedFields, true)) {
            throw new InvalidArgumentException("Invalid rating field “{$field}”.");
        }

        if ($minRating < 1 || $minRating > 5) {
            throw new InvalidArgumentException("Rating must be between 1 and 5.");
        }

        $resourceTable = $query->getModel()->getTable();
        $reviewTable   = (new ResourceReviewSummary())->getTable();

        return $query
            ->join($reviewTable, "{$reviewTable}.computer_science_resource_id", '=', "{$resourceTable}.id")
            ->select("{$resourceTable}.*")
            ->whereRaw("{$reviewTable}.{$field} >= ? * {$reviewTable}.review_count", [$minRating]);
        }
}
