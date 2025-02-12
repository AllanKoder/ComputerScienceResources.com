<?php

namespace App\Http\Controllers;

use App\Models\UpvoteSummary;
use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpvoteController extends Controller
{
    /**
     * Upvote a Model (type, id)
     */
    public function upvote(ModelResolverService $resolver, $type, $id)
    {
        $model = $resolver->resolve($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $user_id = auth()->id();
        $result = $model->upvote($user_id);

        return response()->json([
            'userVote' => $result['userVote'],
            'changeFromVote' => $result['changeFromVote']
        ]);
    }

    /**
     * Downvote a Model (type, id)
     */
    public function downvote(ModelResolverService $resolver, $type, $id)
    {
        $model = $resolver->resolve($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $user_id = auth()->id();
        $result = $model->downvote($user_id);

        return response()->json([
            'userVote' => $result['userVote'],
            'changeFromVote' => $result['changeFromVote']
        ]);
    }
}
