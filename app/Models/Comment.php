<?php

namespace App\Models;

use App\Observers\CommentObserver;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

#[ObservedBy([CommentObserver::class])]
class Comment extends Model
{
    use CascadesDeletes;

    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    use HasVotes;

    protected $cascadeDeletes = ['votes', 'upvoteSummary'];

    protected $with = ['votes', 'upvoteSummary'];

    protected $appends = ['vote_score', 'user_vote'];

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'root_comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
