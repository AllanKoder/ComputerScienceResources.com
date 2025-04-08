<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CommentService
{
    protected $maxPerPage;

    public function __construct()
    {
        $this->maxPerPage = config('comment.max_comment_query', 10);
    }

    /**
     * Get paginated comments with custom logic.
     *
     * @param string $commentableType
     * @param int    $commentableId
     * @param int    $index
     * @return array
     */
    public function getPaginatedComments(string $commentableType, int $commentableId, int $index): array
    {
        Log::debug("Request is, commentable_type: {$commentableType}. id: {$commentableId}. index: {$index}");

        // Get the root comments:
        $rootComments = Comment::where([
            'commentable_type' => $commentableType,
            'commentable_id'   => $commentableId,
            'depth'            => 1,
        ])
            ->orderBy('created_at')
            ->get();

        Log::debug("Root comments: " . json_encode($rootComments));

        // Initialize variables
        $currentCommentsSum = 0;
        $commentsToReturn = [];
        $currentIndex = 0;
        $hasMoreComments = false;

        foreach ($rootComments as $comment) {
            $childrenCount = $comment->children_count + 1;

            // Handle comments that exceed MAX when alone in a page
            if ($currentCommentsSum + $childrenCount > $this->maxPerPage) {
                if ($currentCommentsSum === 0) {
                    // Force include oversized comment if it's the first in page
                    Log::warning("Had to force include for oversized comment tree. Should consider increasing the max commentx in config or lowering the replies size limit.");
                    if ($currentIndex === $index) {
                        $commentsToReturn[] = $comment;
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
                $commentsToReturn[] = $comment;
            }
            $currentCommentsSum += $childrenCount;
        }

        return [
            'comments' => new Collection($commentsToReturn),
            'has_more_comments' => $hasMoreComments,
        ];
    }
}
