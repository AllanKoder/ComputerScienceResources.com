<?php

namespace App\Http\Controllers;

use App\Models\TagFrequency;
use Illuminate\Http\Request;

class TagFrequencyController extends Controller
{
    public function search(string $query = "")
    {
        if (strlen($query) > 50)
        {
            return response()->json(['message' => 'Query too long.'], 422);
        }

        $prefixed_tags = TagFrequency::where('tag', 'like', $query.'%')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        return response()->json([
            'tags' => $prefixed_tags
        ]);
    }
}
