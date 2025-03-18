<?php

namespace App\Models;

use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Tags\HasTags;
use Auth;

class ComputerScienceResource extends Model
{
    /** @use HasFactory<\Database\Factories\ComputerScienceResourceFactory> */
    use HasFactory;
    use HasTags;
    use HasVotes;

    protected $table = "computer_science_resources";

    protected $guarded = [];

    protected $with = ['tags', 'votes', 'upvoteSummary'];

    /**
     * Get the review summary relationship.
     */
    public function reviewSummary(): HasOne
    {
        return $this->hasOne(ResourceReviewSummary::class);
    }
    
    /**
     * Accessor to get topic tags.
     *
     * @return Attribute
     */
    protected function topicTags(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tagsWithType('topics')->pluck('name')->toArray(),
            set: fn(array $value) => $this->syncTagsWithType($value, 'topics')
        );
    }

    /**
     * Accessor to get programming language tags.
     *
     * @return Attribute
     */
    protected function programmingLanguageTags(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tagsWithType('programming_languages')->pluck('name')->toArray(),
            set: fn(array $value) => $this->syncTagsWithType($value, 'programming_languages')
        );
    }

    /**
     * Accessor to get general tags.
     *
     * @return Attribute
     */
    protected function generalTags(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tagsWithType('general_tags')->pluck('name')->toArray(),
            set: fn(array $value) => $this->syncTagsWithType($value, 'general_tags')
        );
    }

    protected $appends = ['topic_tags', 'programming_language_tags', 'general_tags', 'total_votes', 'user_vote'];
}
