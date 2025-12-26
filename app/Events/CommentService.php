<?php

namespace App\Services;

use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Services\SortingManagers\GeneralVotesSortingManager;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentService
{
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
            'sort_by' => $sortBy,
        ], [
            'index' => ['required', 'integer', 'min:0'],
            'commentable_key' => ['required', Rule::in(config('comment.commentable_keys'))],
            'pagination_limit' => ['required', 'integer', 'max:'.config('comment.pagination_limit')],
            'sort_by' => ['required', 'string'],
        ]);

        $commentableType = Relation::getMorphedModel($commentableKey);
        Log::debug('Getting paginated comments', [
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'index' => $index,
            'sort_by' => $sortBy,
            'pagination_limit' => $paginationLimit,
        ]);

        // Get the root comments:
        $query = Comment::where([
            'commentable_type' => $commentableKey,
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

    /**
     * Create and save a comment
     *
     * @throws Exception
     */
    public function createComment(array $validatedData): Comment
    {
        DB::beginTransaction();

        $comment = new Comment;
        $comment->content = $validatedData['content'];
        $comment->user_id = Auth::id();

        $commentableKey = $validatedData['commentable_key'];
        $commentableModel = Relation::getMorphedModel($commentableKey);
        $commentableId = $validatedData['commentable_id'];

        // Ensure that the model exists
        $model = $commentableModel::find($commentableId);
        if (! $model) {
            throw new NotFoundHttpException;
        }

        // Set the commentable type
        $comment->commentable_type = $commentableKey;
        $comment->commentable_id = $commentableId;

        // Top level comment
        $parentCommentId = $validatedData['parent_comment_id'];
        if (! $parentCommentId) {
            $comment->parent_comment_id = null;
            $comment->depth = 1;
            $comment->children_count = 0;
        }
        // Is reply to a comment
        else {
            $parent = Comment::find($parentCommentId);
            $new_comment_depth = $parent->depth + 1;

            // Check if the parent is the root comment
            if ($parent->depth == 1) {
                $root_comment = $parent;
                $root_comment_id = $parent->id;
            } else {
                // If not, fetch the root comment
                $root_comment_id = $parent->root_comment_id;
                $root_comment = Comment::find($root_comment_id);
            }

            $replies_count = $root_comment->children_count ?? 0;

            // Ensure that they are commenting to the same root
            // And the depth is not exceeded
            Validator::validate(
                [
                    'commentable_id' => $commentableId,
                    'commentable_type' => $commentableKey,
                    'depth' => $new_comment_depth,
                    'replies_count' => $replies_count,
                ],
                [
                    'commentable_id' => [
                        'required',
                        Rule::in([$parent->commentable_id]),
                    ],
                    'commentable_type' => [
                        'required',
                        Rule::in([$parent->commentable_type]),
                    ],
                    // Cannot exceed the max depth
                    'depth' => [
                        'required',
                        'integer',
                        'lte:'.(config('comment.max_depth')),
                    ],
                    // Cannot exceed max replies
                    'replies_count' => [
                        'required',
                        'integer',
                        'lt:'.(config('comment.max_replies')),
                    ],
                ]
            );

            // Set the parent id
            $comment->parent_comment_id = $parentCommentId;

            // Set the parent's root as this comment's root, unless it is the root itself.
            $comment->root_comment_id = $root_comment_id;

            // Set the new depth
            $comment->depth = $new_comment_depth;

            // Update the children count for root
            $root_comment->children_count = $root_comment->children_count + 1;
            $root_comment->save();
        }

        $comment->save();


        Log::debug('New comment saved', [
            'comment_id' => $comment->id,
            'user_id' => $comment->user_id,
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
            'depth' => $comment->depth,
        ]);

        DB::commit();

        return $comment;
    }
}
