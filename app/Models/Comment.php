<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'comment_text',
        'user_id',
        'comment_head_id',
        'reports',
        'upvotes'
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
        return $this->belongsTo(Comment::class, 'comment_head_id');
    }

    /**
     * Get the replies for the comment.
     */
    public function commentChildren(): HasMany
    {
        return $this->hasMany(Comment::class, 'comment_head_id');
    }

    public function votedByUsers()
    {
        return $this->belongsToMany(User::class, 'votes')
                    ->withPivot('vote_type')
                    ->withTimestamps();
    }
}
