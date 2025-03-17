<?php

namespace App\Traits;

use App\Models\CommentsCount;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasComments
{
    /**
     * Define a relationship to the CommentsCount model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function commentsCount(): HasOne
    {
        return $this->hasOne(CommentsCount::class, 'commentable_id', 'id')
            ->where('commentable_type', static::class);
    }

    /**
     * Define the comments count accessor.
     *
     * @return int
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->commentsCount?->count ?? 0;
    }
}
