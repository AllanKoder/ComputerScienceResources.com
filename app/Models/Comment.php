<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_text',
        'comment_title',
        'user_id',
        'commentable_id',
        'commentable_type',
    ];

    /**
     * Get the commentable entity that the comment belongs to.
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that authored the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the votes for the comment.
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    /**
     * Get the reports for the comment.
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    /**
     * Get the upvotes for the comments.
     */
    public static function getUpvotes(Collection $comments): Collection
    {
        // Ensure $comments is a collection
        $comments = collect($comments);
        
        // Check if there are any comments
        if ($comments->isEmpty()) {
            return $comments;
        }
    
        $commentIds = $comments->pluck('id')->toArray();
    
        // Check if there are any comment IDs
        if (empty($commentIds)) {
            return $comments;
        }
    
        $voteTotals = VoteTotal::getTotalVotesForComments($commentIds);
    
        foreach ($comments as $comment) {
            $comment->total_votes = $voteTotals->get($comment->id)->total_votes ?? 0;
        }
    
        return $comments;
    }

    // Get the comment tree with the user and the votes
    public static function getCommentTree($commentableType, $commentableId)
    {
        // Fetch all ancestor comments with their user and votes
        $ancestorComments = self::where('commentable_id', $commentableId)
            ->where('commentable_type', $commentableType)
            ->get();

        // Fetch all replies for each comment using the CommentClosure model
        $ancestorIds = $ancestorComments->pluck('id');
        $allComments = self::whereIn('id', function ($query) use ($ancestorIds) {
            $query->select('comment_id')
                ->from('comment_closures')
                ->whereIn('ancestor', $ancestorIds);
        })->with(['user'])
          ->get();

        // Combine ancestor comments and all replies
        $comments = $ancestorComments->merge($allComments);

        // Fetch total votes for all comments
        $commentIds = $comments->pluck('id');
        $voteTotals = VoteTotal::getTotalVotesForComments($commentIds);

        // Assign total votes to each comment
        foreach ($comments as $comment) {
            $comment->total_votes = $voteTotals->get($comment->id)->total_votes ?? 0;
        }

        // Build the comment tree
        return self::buildCommentTree($comments, $commentableId);
    }

    private static function buildCommentTree($comments, $resourceId)
    {
        $grouped = $comments->groupBy('commentable_id');

        foreach ($comments as $comment) {
            // Ensure a comment is not its own child
            if ($comment->id !== $comment->commentable_id) {
                $comment->comments = $grouped->get($comment->id, collect());
            } else {
                $comment->comments = collect();
            }
        }

        // Top-level comments are those with `commentable_id` equal to the resource ID
        return $grouped->get($resourceId, collect());
    }
}
