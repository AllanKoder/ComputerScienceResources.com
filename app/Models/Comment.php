<?php

namespace App\Models;

use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    use HasVotes;

    protected $with = ['votes', 'upvoteSummary'];

    protected $appends = ['total_votes', 'user_vote'];

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'root_comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
