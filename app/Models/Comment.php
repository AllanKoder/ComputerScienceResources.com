<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_text',
        'user_id',
        'parent_id'
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
     * Get the head comment if this is a reply.
     */
    public function commentHead(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
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
        return $this->morphMany(Vote::class, 'reportable');
    }

    public function addTotalVotesToComments($comments)
    {
        $voteTotalModel = new VoteTotal();
        foreach ($comments as $comment) {
            $comment->total_votes = $voteTotalModel->getTotalVotes($comment->id, Comment::class);
            if ($comment->replies->isNotEmpty()) {
                $comment->replies = $this->addTotalVotesToComments($comment->replies);
            }
        }
        return $comments;
    }
}
