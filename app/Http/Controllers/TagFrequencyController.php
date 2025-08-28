<?php

namespace App\Http\Controllers;

use App\Models\TagFrequency;

class TagFrequencyController extends Controller
{
    public function search(string $type, string $query = '')
    {
        if (strlen($query) > 50) {
            return response()->json(['message' => 'Query too long'], 422);
        }

        if (! in_array($type, ['topics_tags', 'programming_languages_tags', 'general_tags'])) {
            return response()->json(['message' => 'Not a valid type'], 422);
        }

        $prefixed_tags = TagFrequency::where('tag', 'like', $query.'%')
            ->where('type', $type)
            ->orderByDesc('count')
            ->limit(30)
            ->get();

        return response()->json([
            'tags' => $prefixed_tags,
        ]);
    }
}
