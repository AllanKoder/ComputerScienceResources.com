<?php

namespace App\Services;

use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CommentService
{
    protected $modelResolver;

    function __construct(ModelResolverService $resolver)
    {
        $this->modelResolver = $resolver;
    }

    /**
     * Get paginated comments with custom logic.
     *
     * @param string $commentableType
     * @param int    $commentableId
     * @param int    $index
     * @return array
     */
    // TODO: Refactor commentable types, and commentable types short for all other objects
    public function getPaginatedComments(string $commentableTypeShort, int $commentableId, int $index, int $paginationLimit = -1, string $sortBy = 'top'): array
    {
        if ($paginationLimit == -1)
        {
            $paginationLimit = config('comment.default_pagination_limit');
        }

        Validator::make([
            'index' => $index,
            'commentable_type_short' => $commentableTypeShort,
            'pagination_limit' => $paginationLimit,
            'sort_by' => $sortBy,
        ], [
            'index' => ['required', 'integer', 'min:0'],
            'commentable_type_short' => ['required', Rule::in(config('comment.commentable_types_shorthand'))],
            'pagination_limit' => ['required', 'integer', 'max:' . config('comment.pagination_limit')],
            'sort_by' => ['required', 'string', Rule::in(config('comment.sortable_options'))],
        ])->validate();

        $commentableType = $this->modelResolver->getModelClass($commentableTypeShort);   
        Log::debug("Request is, commentable_type: {$commentableType}. id: {$commentableId}. index: {$index}");

        // Get the root comments:
        $query = Comment::where([
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'depth' => 1,
        ]);
        
        // Apply sorting on the comments
        app(UpvoteService::class)->applySort($query, $sortBy, Comment::class);

        $rootComments = $query->get();
        Log::debug("Root comments: " . json_encode($rootComments));
            
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
                    // Force include oversized comment if it's the first in page
                    Log::warning("Had to force include for oversized comment tree. Should consider increasing the max commentx in config or lowering the replies size limit.");
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
            else if ($currentIndex === $index) {
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

        Log::debug("Returned comments: " . json_encode($flattenedComments));
        return [
            'comments' => $flattenedComments,
            'users' => $users->values(),
            'has_more_comments' => $hasMoreComments,
        ];
    }
}
