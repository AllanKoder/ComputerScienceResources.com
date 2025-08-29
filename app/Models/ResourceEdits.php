<?php

namespace App\Models;

use App\Observers\ResourceEditsObserver;
use App\Services\ResourceEditsService;
use App\Traits\HasComments;
use App\Traits\HasVotes;
use App\Utilities\UrlUtilities;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy([ResourceEditsObserver::class])]
class ResourceEdits extends Model
{
    use CascadesDeletes;
    use HasComments;

    /** @use HasFactory<\Database\Factories\ResourceEditsFactory> */
    use HasFactory;

    use HasVotes;
    use LogsActivity;
    use Sluggable;
    use SoftDeletes;

    protected $cascadeDeletes = ['votes', 'upvoteSummary', 'comments', 'commentsCountRelationship'];

    protected $guarded = [];

    protected $with = ['votes', 'upvoteSummary', 'commentsCountRelationship', 'computerScienceResource'];

    protected $appends = ['user_vote', 'vote_score', 'comments_count', 'can_merge_edits'];

    protected $casts = [
        'proposed_changes' => 'array',
    ];

    protected static $recordEvents = ['created', 'updated'];

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
                'source' => 'edit_title',
            ],
        ];
    }

    public function computerScienceResource(): BelongsTo
    {
        return $this->belongsTo(ComputerScienceResource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function proposedChanges(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $changes = json_decode($value, true);

                if (array_key_exists('image_path', $changes)) {
                    $changes['image_url'] = null;
                    if ($changes['image_path']) {
                        $changes['image_url'] = Storage::disk('public')->url($changes['image_path']);
                    }
                }

                return $changes;
            },
            set: function ($value) {
                // TODO: HANDLE THIS IN THE REQUEST FORM.
                if (array_key_exists('page_url', $value) && is_string($value['page_url'])) {
                    $value['page_url'] = UrlUtilities::normalize($value['page_url']);
                }

                return json_encode($value);
            }
        );
    }

    /**
     * Attribute to know if the edit can be merged
     */
    protected function canMergeEdits(): Attribute
    {
        return Attribute::make(
            get: fn () => app(ResourceEditsService::class)->canMergeEdits($this) && Auth::id() === $this->user_id,
        );
    }
}
