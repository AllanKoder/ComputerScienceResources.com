<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Tags\HasTags;
use Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComputerScienceResource extends Model
{
    /** @use HasFactory<\Database\Factories\ComputerScienceResourceFactory> */
    use HasFactory;
    use HasTags;
    use HasComments;
    use HasVotes;

    protected $table = "computer_science_resources";

    protected $guarded = [];

    protected $appends = ['topic_tags', 'programming_language_tags', 'general_tags', 'vote_score', 'user_vote', 'comments_count'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review summary relationship.
     */
    public function reviewSummary(): HasOne
    {
        return $this->hasOne(ResourceReviewSummary::class);
    }
    
    /**
     * Get all the reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ResourceReview::class);
    }

    public function edits(): HasMany
    {
        return $this->hasMany(ResourceEdits::class);
    }

    /**
     * Attribute to get and set platforms as an array
     * 
     * @return Attribute
     */
    protected function platforms(): Attribute
    {
        return Attribute::make(
            get: fn($value) => explode(',', $value),
            set: fn($value) => implode(',', $value)
        );
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
}
