<?php

namespace App\Http\Controllers;

use App\Services\ModelResolverService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpvoteController extends Controller
{
    protected $modelResolver;

    public function __construct(ModelResolverService $modelResolver)
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
                'type_key' => ['required', Rule::in(config('upvotes.upvotable_keys'))],
            ]
        )->validate();

        DB::beginTransaction();
        try {
            $model = $this->modelResolver->resolve($typeKey, $id);

            if (! $model) {
                return response()->json(['message' => 'Model not found'], 404);
            }

            $user_id = Auth::id();
            $result = $model->upvote($user_id);

            DB::commit();

            return response()->json([
                'userVote' => $result['userVote'],
                'changeFromVote' => $result['changeFromVote'],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to upvote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type_key' => $typeKey,
                'id' => $id,
                'user_id' => Auth::id(),
            ]);
            return response()->json(['message' => 'Failed to upvote. Please try again.'], 500);
        }
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
                'type_key' => ['required', Rule::in(config('upvotes.upvotable_keys'))],
            ]
        )->validate();

        DB::beginTransaction();
        try {
            $model = $this->modelResolver->resolve($typeKey, $id);

            if (! $model) {
                return response()->json(['message' => 'Model not found'], 404);
            }

            $user_id = Auth::id();
            $result = $model->downvote($user_id);

            DB::commit();

            return response()->json([
                'userVote' => $result['userVote'],
                'changeFromVote' => $result['changeFromVote'],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to downvote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type_key' => $typeKey,
                'id' => $id,
                'user_id' => Auth::id(),
            ]);
            return response()->json(['message' => 'Failed to downvote. Please try again.'], 500);
        }
    }
}
