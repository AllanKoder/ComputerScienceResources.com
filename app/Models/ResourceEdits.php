<?php

namespace App\Models;

use App\Observers\ResourceEditsObserver;
use App\Services\ResourceEditsService;
use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Support\Facades\Storage;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

#[ObservedBy([ResourceEditsObserver::class])]
class ResourceEdits extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceEditsFactory> */
    use HasFactory;
    use CascadesDeletes;
    use HasComments;
    use HasVotes;

    protected $cascadeDeletes = ['votes', 'upvoteSummary', 'comments', 'commentsCountRelationship'];

    protected $guarded = [];

    protected $with = ['votes','upvoteSummary', 'commentsCountRelationship', 'resource'];

    protected $appends = ['user_vote', 'vote_score', 'comments_count', 'can_merge_edits', 'image_url'];

    protected $casts = [
        'proposed_changes' => 'array',
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
     * Attribute to get the image_url
     *
     * @return Attribute
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::url($this->image_path),
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
