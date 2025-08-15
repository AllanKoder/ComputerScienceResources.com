<?php

namespace App\Models;

use App\Observers\ComputerScienceResourceObserver;
use App\Traits\HasComments;
use App\Traits\HasVotes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Tags\HasTags;

#[ObservedBy([ComputerScienceResourceObserver::class])]
class ComputerScienceResource extends Model
{
    use HasComments;
    use HasFactory;
    use HasTags;
    use HasVotes;
    use LogsActivity;
    use Sluggable;

    protected $table = 'computer_science_resources';

    protected $guarded = [];

    protected $appends = ['topic_tags', 'programming_language_tags', 'general_tags', 'vote_score', 'user_vote', 'comments_count', 'image_url'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Attribute to get the image_url
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_path ? Storage::disk('public')->url($this->image_path) : null,
        );
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
     */
    protected function platforms(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => explode(',', $value),
            set: fn ($value) => implode(',', $value)
        );
    }

    /**
     * Accessor to get topic tags.
     */
    protected function topicTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('topics')->pluck('name')->toArray(),
            set: fn (array $value) => $this->syncTagsWithType($value, 'topics')
        );
    }

    /**
     * Accessor to get programming language tags.
     */
    protected function programmingLanguageTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('programming_languages')->pluck('name')->toArray(),
            set: fn (array $value) => $this->syncTagsWithType($value, 'programming_languages')
        );
    }

    /**
     * Accessor to get general tags.
     */
    protected function generalTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('general_tags')->pluck('name')->toArray(),
            set: fn (array $value) => $this->syncTagsWithType($value, 'general_tags')
        );
    }

    public function tagCounter(): array
    {
        $tag_collection = collect([$this->topic_tags, $this->programming_language_tags, $this->general_tags]);

        return $tag_collection->flatten()->countBy()->toArray();
    }
}
