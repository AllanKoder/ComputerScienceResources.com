<?php

namespace App\Traits;

use App\Models\CommentsCount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasComments
{
    /**
     * Define a relationship to the CommentsCount model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function commentsCountRelationship(): HasOne
    {
        return $this->hasOne(CommentsCount::class, 'commentable_id', 'id')
            ->where('commentable_type', static::class);
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
