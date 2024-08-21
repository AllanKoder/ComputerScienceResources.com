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

        // Fetch all replies for each comment using the CommentHierarchy model
        $ancestorIds = $ancestorComments->pluck('id');

        $replyComments = self::whereIn('id', function ($query) use ($ancestorIds) {
            $query->select('comment_id')
                ->from('comment_hierarchies')
                ->whereIn('ancestor', $ancestorIds);
        })->get();
        
        // Combine ancestor comments and all replies
        $comments = $ancestorComments->merge($replyComments);

        // Loading all the users to stop a N+1 query problem
        $userIds = $comments->pluck('user_id')->unique();

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        // Attach users to their respective comments
        foreach ($comments as $comment) {
            $comment->user = $users->get($comment->user_id);
        }

        // Fetch total votes for all comments
        $commentIds = $comments->pluck('id');
        $voteTotals = VoteTotal::getTotalVotesForComments($commentIds);

        // Assign total votes to each comment
        foreach ($comments as $comment) {
            $comment->total_votes = $voteTotals->get($comment->id)->total_votes ?? 0;
        }

        // Build the comment tree
        return self::buildCommentTree($comments, $commentableId, $commentableType);
    }

    private static function buildCommentTree($comments, $commentableId, $commentableType)
    {
        // Group comments by a composite key of commentable_id and commentable_type (what is being commented)
        $grouped = $comments->groupBy(function ($comment) {
            return $comment->commentable_id . '-' . $comment->commentable_type;
        });
        
        // Only comments here
        foreach ($comments as $comment) {
            // get the comments, which is (what is being commented on) being this comment
            // I hope this is not confusing:
            // the replies are found by searching for the comments that commented on this specific comment.
            $compositeKey = $comment->id . '-' . Comment::class;
            $comment->comments = $grouped->get($compositeKey, collect());
        }

        // get the top comments
        $topLevelKey = $commentableId . '-' . $commentableType;
        return $grouped->get($topLevelKey, collect());
    }
    
}
