<?php

namespace App\Http\Controllers;

use App\Models\UpvoteSummary;
use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpvoteController extends Controller
{
    protected $modelResolver;
    protected $upvotableTypes = 'review,resource,comment,edit';

    function __construct(ModelResolverService $modelResolver)
    {
        $this->modelResolver = $modelResolver;
    }

    /**
     * Upvote a Model (type, id)
     */
    public function upvote($type, $id)
    {
        validator(
            [
                'type' => $type,
            ],
            [
                'type' => 'required|in:' . $this->upvotableTypes,
            ]
        )->validate(); 

        $model = $this->modelResolver->resolve($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $user_id = Auth::id();
        $result = $model->upvote($user_id);

        return response()->json([
            'userVote' => $result['userVote'],
            'changeFromVote' => $result['changeFromVote']
        ]);
    }

    /**
     * Downvote a Model (type, id)
     */
    public function downvote($type, $id)
    {
        validator(
            [
                'type' => $type,
            ],
            [
                'type' => 'required|in:' . $this->upvotableTypes,
            ]
        )->validate(); 

        $model = $this->modelResolver->resolve($type, $id);

        if (!$model) {
            return response()->json(['message' => 'Model not found'], 404);
        }

        $user_id = Auth::id();
        $result = $model->downvote($user_id);

        return response()->json([
            'userVote' => $result['userVote'],
            'changeFromVote' => $result['changeFromVote']
        ]);
    }
}
