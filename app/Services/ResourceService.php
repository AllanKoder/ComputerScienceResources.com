<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\VoteTotal;
use App\Models\Comment;
use App\Http\Requests\Resource\GetResourcesRequest;

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
            $query->WhereRaw('community_size_total >= ? * total_reviews', [$community_size]);
        }

        if ($request->filled('teaching_clarity')) {
            $teaching_clarity = (int) $request->input('teaching_clarity');
            $query->WhereRaw('teaching_explanation_clarity_total >= ? * total_reviews', [$teaching_clarity]);
        }

        if ($request->filled('technical_depth')) {
            $technical_depth = (int) $request->input('technical_depth');
            $query->WhereRaw('technical_depth_total >= ? * total_reviews', [$technical_depth]);
        }

        if ($request->filled('practicality_to_industry')) {
            $practicality_to_industry = (int) $request->input('practicality_to_industry');
            $query->WhereRaw('practicality_to_industry_total >= ? * total_reviews', [$practicality_to_industry]);
        }

        if ($request->filled('user_friendliness')) {
            $user_friendliness = (int) $request->input('user_friendliness');
            $query->WhereRaw('user_friendliness_total >= ? * total_reviews', [$user_friendliness]);
        }

        if ($request->filled('updates_and_maintenance')) {
            $updates_and_maintenance = (int) $request->input('updates_and_maintenance');
            $query->WhereRaw('updates_and_maintenance_total >= ? * total_reviews', [$updates_and_maintenance]);
        }

        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $query->withAnyTags($tags);
        }

        \Log::debug('fetching resource: ' . json_encode($request->all()));
        \Log::debug('raw request SQL: ' . $query->toSql());

        return $query->get();
    }
}