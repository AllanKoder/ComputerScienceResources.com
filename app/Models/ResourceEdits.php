<?php

namespace App\Models;

use App\Services\ResourceEditsService;
use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceEdits extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceEditsFactory> */
    use HasFactory;
    use HasComments;
    use HasVotes;

    protected $guarded = [];

    protected $with = ['votes','upvoteSummary', 'commentsCountRelationship', 'resource'];

    protected $appends = ['user_vote', 'vote_score', 'comments_count', 'can_merge_edits'];

    protected $casts = [
        'topic_tags' => 'array',
        'programming_language_tags' => 'array',
        'general_tags' => 'array',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(ComputerScienceResource::class, 'computer_science_resource_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
     * Attribute to know if the edit can be merged
     *
     * @return Attribute
     */
    protected function canMergeEdits(): Attribute
    {
        return Attribute::make(
            get: fn () => app(ResourceEditsService::class)->canMergeEdits($this) && Auth::id() === $this->user_id,
        );
    }
}
