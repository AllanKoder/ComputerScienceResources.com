<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use App\Http\Requests\Resource\GetResourcesRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourceService
{
    public function filterResources(GetResourcesRequest $request)
    {
        $query = Resource::query()->with("tags");

        if ($request->filled('title')) {
            $searchQuery = $request->input('title');
            $query->whereFullText('title', $searchQuery, ['mode'=> 'boolean']);
        }

        if ($request->filled('description')) {
            $searchDescription = $request->input('description');
            $query->whereFullText('description', $searchDescription);
        }
        
        if ($request->filled('formats')) {
            $categories = $request->input('formats');
            $query->where(function (Builder $query) use ($categories) {
                foreach ($categories as $category) {
                    $query->orWhereJsonContains('formats', $category);
                }
            });
        }

        if ($request->filled('pricing')) {
            $pricings = $request->input('pricing');
            $query->where(function (Builder $query) use ($pricings) {
                foreach ($pricings as $pricing) {
                    $query->orWhere('pricing', '=', $pricing);
                }
            });
        }

        if ($request->filled('difficulty')) {
            $difficulties = $request->input('difficulty');
            $query->where(function (Builder $query) use ($difficulties) {
                foreach ($difficulties as $difficulty) {
                    $query->orWhere('difficulty', '=', $difficulty);
                }
            });
        }

        if ($request->filled('topics')) {
            $topics = $request->input('topics');
            $query->where(function (Builder $query) use ($topics) {
                foreach ($topics as $topic) {
                    $query->orWhereJsonContains('topics', $topic);
                }
            });
        }

        if ($request->filled('community_size')) {
            $community_size = (int) $request->input('community_size');
            $query->whereRaw('community_size_total >= ? * total_reviews', [$community_size]);
        }

        if ($request->filled('teaching_clarity')) {
            $teaching_clarity = (int) $request->input('teaching_clarity');
            $query->whereRaw('teaching_explanation_clarity_total >= ? * total_reviews', [$teaching_clarity]);
        }

        if ($request->filled('technical_depth')) {
            $technical_depth = (int) $request->input('technical_depth');
            $query->whereRaw('technical_depth_total >= ? * total_reviews', [$technical_depth]);
        }

        if ($request->filled('practicality_to_industry')) {
            $practicality_to_industry = (int) $request->input('practicality_to_industry');
            $query->whereRaw('practicality_to_industry_total >= ? * total_reviews', [$practicality_to_industry]);
        }

        if ($request->filled('user_friendliness')) {
            $user_friendliness = (int) $request->input('user_friendliness');
            $query->whereRaw('user_friendliness_total >= ? * total_reviews', [$user_friendliness]);
        }

        if ($request->filled('updates_and_maintenance')) {
            $updates_and_maintenance = (int) $request->input('updates_and_maintenance');
            $query->whereRaw('updates_and_maintenance_total >= ? * total_reviews', [$updates_and_maintenance]);
        }

        if ($request->filled('average_score')) {
            $average_score = (int) $request->input('average_score');
            $query->whereRaw('
                (community_size_total +
                teaching_explanation_clarity_total +
                technical_depth_total +
                practicality_to_industry_total +
                user_friendliness_total +
                updates_and_maintenance_total)
                / 6 >= ? * total_reviews', [$average_score]);
        }

       if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $query->withAnyTags($tags);
        }

        // Calculate average rating and sort by it
        $query->select('*', \DB::raw('
            (community_size_total + teaching_explanation_clarity_total + technical_depth_total + practicality_to_industry_total + user_friendliness_total + updates_and_maintenance_total) / (6 * total_reviews) as average_rating
        '))
        ->orderBy('average_rating', 'desc');

        \Log::debug('fetching resource: ' . json_encode($request->all()));
        \Log::debug('raw request SQL: ' . $query->toSql());
        
        // Limit the results to a maximum of 100 elements
        // Get the first 100 results
        $results = $query->take(1000)->get();

        // Paginate the results with 10 elements per page and append query parameters
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentResults = $results->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedResults = new LengthAwarePaginator($currentResults, $results->count(), $perPage);
        $paginatedResults->setPath($request->url())->appends($request->except('page'));

        return $paginatedResults;

    }
}