<?php

namespace App\Http\Controllers;

use App\Models\UpvoteSummary;
use App\Services\ModelResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UpvoteController extends Controller
{
    protected $modelResolver;

    function __construct(ModelResolverService $modelResolver)
    {
        $this->modelResolver = $modelResolver;
    }

    /**
     * Upvote a Model
     */
    public function upvote($typeKey, $id)
    {
        validator(
            [
                'type_key' => $typeKey,
            ],
            [
                'type_key' => ['required', Rule::in(config('upvotes.upvotable_keys'))]
            ]
        )->validate();

        $model = $this->modelResolver->resolve($typeKey, $id);

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
    public function downvote($typeKey, $id)
    {
        validator(
            [
                'type_key' => $typeKey,
            ],
            [
                'type_key' => ['required', Rule::in(config('upvotes.upvotable_keys'))]
            ]
        )->validate();

        $model = $this->modelResolver->resolve($typeKey, $id);

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
