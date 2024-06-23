<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'comment_id',
        'vote_type', // 1 for upvote, -1 for downvote
    ];

    /**
     * Get the user that cast the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comment that was voted on.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
