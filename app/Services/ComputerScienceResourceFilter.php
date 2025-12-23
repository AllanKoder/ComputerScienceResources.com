<?php

namespace App\Services;

use App\Services\SortingManagers\ResourceSortingManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ComputerScienceResourceFilter
{
    protected ResourceReviewService $reviewService;

    protected ResourceSortingManager $resourceSortingManager;

    public function __construct(ResourceReviewService $reviewService, ResourceSortingManager $resourceSortingManager)
    {
        $this->reviewService = $reviewService;
        $this->resourceSortingManager = $resourceSortingManager;
    }

    public function validate(array $request)
    {
        $rules = [
            'name' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'platforms' => ['nullable', 'array'],
            'platforms.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.platforms'))],
            'difficulties' => ['nullable', 'array'],
            'difficulties.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.difficulties'))],
            'pricing' => ['nullable', 'array'],
            'pricing.*' => ['required', 'distinct', 'string', Rule::in(config('computerScienceResource.pricings'))],
            'topics_tags' => ['nullable', 'array'],
            'topics.*' => ['required', 'distinct', 'string', 'max:50'],
            'general_tags' => ['nullable', 'array'],
            'general_tags.*' => ['required', 'distinct', 'string', 'max:50'],
            'programming_languages_tags' => ['nullable', 'array'],
            'programming_languages_tags.*' => ['required', 'distinct', 'string', 'max:50'],

            // Fixed field names to match frontend
            'overall' => ['nullable', 'integer', 'between:1,4'],
            'community' => ['nullable', 'integer', 'between:1,4'],
            'teaching_clarity' => ['nullable', 'integer', 'between:1,4'],
            'engagement' => ['nullable', 'integer', 'between:1,4'],
            'practicality' => ['nullable', 'integer', 'between:1,4'],
            'user_friendliness' => ['nullable', 'integer', 'between:1,4'],
            'updates' => ['nullable', 'integer', 'between:1,4'],

            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'updated_from' => ['nullable', 'date'],
            'updated_to' => ['nullable', 'date'],

            'sort_by' => ['string'],
            'reverse' => ['string'],
        ];

        $validator = Validator::make($request, $rules);
        $validator->validate();
    }

    public function applyFilters($query, array $filters)
    {
        $this->validate($filters);

        // Eager load relations
        $query->with(['tags', 'votes', 'upvoteSummary', 'reviewSummary', 'commentsCountRelationship']);

        // Fulltext search on name
        if (! empty($filters['name'])) {
            $query->whereFullText('name', $filters['name']);
        }

        // Fulltext search on description
        if (! empty($filters['description'])) {
            $query->whereFullText('description', $filters['description']);
        }

        // Filter by platforms (array)
        if (! empty($filters['platforms'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['platforms'] as $platform) {
                    $q->orWhereRaw('FIND_IN_SET(?, platforms)', [$platform]);
                }
            });
        }

        // Filter by difficulties
        if (! empty($filters['difficulties'])) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters['difficulties'] as $difficulty) {
                    $q->orWhereRaw('FIND_IN_SET(?, difficulties)', [$difficulty]);

                }
            });
        }

        // Filter by pricing
        if (! empty($filters['pricing'])) {
            $query->whereIn('pricing', (array) $filters['pricing']);
        }

        // Filter by topic tags
        if (! empty($filters['topics_tags'])) {
            $query->withAnyTags((array) $filters['topics_tags'], 'topics_tags');
        }

        // Filter by programming languages
        if (! empty($filters['programming_languages_tags'])) {
            $query->withAnyTags((array) $filters['programming_languages_tags'], 'programming_languages_tags');
        }

        // Filter by general tags
        if (! empty($filters['general_tags'])) {
            $query->withAnyTags((array) $filters['general_tags'], 'general_tags');
        }

        // Filter by reviews
        $ratingFilters = [
            'community',
            'teaching_clarity',
            'engagement',
            'practicality',
            'user_friendliness',
            'updates',
            'overall',
        ];

        foreach ($ratingFilters as $field) {
            if (! empty($filters[$field])) {
                $query = $this->reviewService->applyRatingFilter($query, $field, $filters[$field]);
            }
        }

        // Filter by Date posted
        if (! empty($filters['created_from'])) {
            $query->whereDate('computer_science_resources.created_at', '>=', $filters['created_from']);
        }
        if (! empty($filters['created_to'])) {
            $query->whereDate('computer_science_resources.created_at', '<=', $filters['created_to']);
        }

        // Filter by Date updated
        if (! empty($filters['updated_from'])) {
            $query->whereDate('computer_science_resources.updated_at', '>=', $filters['updated_from']);
        }
        if (! empty($filters['updated_to'])) {
            $query->whereDate('computer_science_resources.updated_at', '<=', $filters['updated_to']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'overall';
        $query = $this->resourceSortingManager->applySort($query, $sortBy);

        if (($filters['reverse'] ?? 'false') === 'true') {
            $query = $this->resourceSortingManager->reverse($query);
        }

        return $query;
    }
}
