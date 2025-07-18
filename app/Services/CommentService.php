<?php

namespace App\Services;

use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommentService
{
    protected $modelResolver;

    public function __construct(ModelResolverService $resolver)
    {
        $this->modelResolver = $resolver;
    }

    /**
     * Get paginated comments with custom logic.
     *
     * @param  string  $commentableType
     */
    public function getPaginatedComments(string $commentableKey, int $commentableId, int $index, int $paginationLimit = -1, string $sortBy = 'top'): array
    {
        if ($paginationLimit == -1) {
            $paginationLimit = config('comment.default_pagination_limit');
        }

        Validator::validate([
            'index' => $index,
            'commentable_key' => $commentableKey,
            'pagination_limit' => $paginationLimit,
        ], [
            'index' => ['required', 'integer', 'min:0'],
            'commentable_key' => ['required', Rule::in(config('comment.commentable_keys'))],
            'pagination_limit' => ['required', 'integer', 'max:'.config('comment.pagination_limit')],
        ]);

        $commentableType = $this->modelResolver->getModelClass($commentableKey);
        Log::debug('Getting paginated comments', [
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'index' => $index,
            'sort_by' => $sortBy,
            'pagination_limit' => $paginationLimit,
        ]);

        // Get the root comments:
        $query = Comment::where([
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'depth' => 1,
        ]);

        // Apply sorting on the comments
        $query = app(GeneralVotesSortingManager::class)->applySort($query, $sortBy, Comment::class);

        $rootComments = $query->get();
        Log::debug('Root comments retrieved', [
            'count' => $rootComments->count(),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
        ]);

        // Initialize variables
        $currentCommentsSum = 0;
        $resultingPaginatedComments = [];
        $currentIndex = 0;
        $hasMoreComments = false;

        foreach ($rootComments as $comment) {
            $childrenCount = $comment->children_count + 1;

            // Handle comments that exceed MAX when alone in a page
            if ($currentCommentsSum + $childrenCount > $paginationLimit) {
                if ($currentCommentsSum === 0) {
                    // Force include oversized comment
                    if ($currentIndex === $index) {
                        $resultingPaginatedComments[] = $comment;
                        $currentCommentsSum += $childrenCount;
                    }
                    $currentIndex++;

                    continue;
                }

                $currentIndex++;
                $currentCommentsSum = 0;
            }

            // Now we know that there exists more comments to load later
            if ($currentIndex > $index) {
                $hasMoreComments = true;
                break;
            }
            // Only add comments for the desired index
            elseif ($currentIndex === $index) {
                $resultingPaginatedComments[] = $comment;
            }
            $currentCommentsSum += $childrenCount;
        }

        $nestedComments = new Collection($resultingPaginatedComments);
        // Lazy eager load the user for the root comment and for each reply.
        $nestedComments->load(['user', 'replies.user']);

        // Flatten the comments and replies into the desired format.
        $flattenedComments = collect();

        foreach ($nestedComments as $comment) {
            // Transform the root comment.
            $flattenedComments->push(
                new CommentResource($comment)
            );

            // Transform any loaded replies.
            if ($comment->relationLoaded('replies')) {
                foreach ($comment->replies as $reply) {
                    $flattenedComments->push(
                        new CommentResource($reply)
                    );
                }
            }
        }

        // Extract unique users into a separate collection.
        $users = collect();

        foreach ($flattenedComments as $comment) {
            if ($comment->relationLoaded('user') && $comment->user) {
                $users->put($comment->user->id, new UserResource($comment->user));
            }
        }

        Log::debug('Returning paginated comments', [
            'comments_count' => $flattenedComments->count(),
            'users_count' => $users->count(),
            'has_more_comments' => $hasMoreComments,
            'current_index' => $index,
        ]);

        return [
            'comments' => $flattenedComments,
            'users' => $users->values(),
            'has_more_comments' => $hasMoreComments,
        ];
    }
}
