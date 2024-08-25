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

        if ($request->filled('tags')) {
            $tags = $request->input('tags');
            $query->withAnyTags($tags);
        }

        \Log::debug('fetching resource: ' . json_encode($request->all()));
        \Log::debug('raw request SQL: ' . $query->toSql());

        return $query->get();
    }
}