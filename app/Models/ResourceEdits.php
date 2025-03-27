<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasVotes;
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

    protected $with = ['votes','upvoteSummary'];

    protected $appends = ['user_vote', 'total_votes'];

    protected $casts = [
        'topic_tags' => 'array',
        'programming_language_tags' => 'array',
        'general_tags' => 'array',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(ComputerScienceResource::class, 'computer_science_resource_id', 'id');
    }
}