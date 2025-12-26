<?php

namespace App\Traits;

use App\Models\Comment;
use App\Models\CommentsCount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasComments
{
    /** Get all of the comments for the HasComments
     *
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'commentable_id', 'id')
            ->where('commentable_type', $this->getMorphClass());
    }

    /**
     * Define a relationship to the CommentsCount model.
     */
    public function commentsCountRelationship(): HasOne
    {
        return $this->hasOne(CommentsCount::class, 'commentable_id', 'id')
            ->where('commentable_type', $this->getMorphClass());
    }

    /**
     * Define the comments count accessor.
     *
     * @return int
     */
    protected function commentsCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->commentsCountRelationship?->count ?? 0;
            }
        );
    }
}
