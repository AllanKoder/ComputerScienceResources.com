<?php

namespace App\Services;

use App\Services\ModelResolverService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class UpvoteService
{
    public function __construct(protected ModelResolverService $modelResolver) {}

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
                Log::warning('Upvote failed: Model not found', [
                    'user_id' => Auth::id(),
                    'type_key' => $typeKey,
                    'id' => $id,
                ]);

                return ['error' => 'Model not found', 'status' => 404];
            }

            $user_id = Auth::id();
            $result = $model->upvote($user_id);

            DB::commit();

            Log::debug('User upvoted model', [
                'user_id' => $user_id,
                'type_key' => $typeKey,
                'id' => $id,
                'result' => $result,
            ]);

            return [
                'userVote' => $result['userVote'],
                'changeFromVote' => $result['changeFromVote'],
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to upvote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type_key' => $typeKey,
                'id' => $id,
                'user_id' => Auth::id(),
            ]);

            return ['error' => 'Failed to upvote. Please try again.', 'status' => 500];
        }
    }

    /**
     * Downvote a Model
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
                Log::warning('Downvote failed: Model not found', [
                    'user_id' => Auth::id(),
                    'type_key' => $typeKey,
                    'id' => $id,
                ]);

                return ['error' => 'Model not found', 'status' => 404];
            }

            $user_id = Auth::id();
            $result = $model->downvote($user_id);

            DB::commit();

            Log::debug('User downvoted model', [
                'user_id' => $user_id,
                'type_key' => $typeKey,
                'id' => $id,
                'result' => $result,
            ]);

            return [
                'userVote' => $result['userVote'],
                'changeFromVote' => $result['changeFromVote'],
            ];
        } catch (Throwable $e) {
            DB::rollBack();
            Log::critical('Failed to downvote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'type_key' => $typeKey,
                'id' => $id,
                'user_id' => Auth::id(),
            ]);

            return ['error' => 'Failed to downvote. Please try again.', 'status' => 500];
        }
    }
}
