<?php

namespace App\Models;

use App\Observers\ResourceReviewObserver;
use App\Traits\HasComments;
use App\Traits\HasVotes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([ResourceReviewObserver::class])]
class ResourceReview extends Model
{
    use HasComments;

    /** @use HasFactory<\Database\Factories\ResourceReviewFactory> */
    use HasFactory;

    use HasVotes;

    protected $guarded = [];

    protected $with = ['votes', 'upvoteSummary', 'commentsCountRelationship'];

    protected $appends = ['average_score', 'comments_count', 'vote_score', 'user_vote'];

    protected $casts = [
        'pros' => 'array',
        'cons' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the average review score.
     */
    protected function averageScore(): Attribute
    {
        return Attribute::make(
            get: function () {
                $fields = [
                    $this->community,
                    $this->teaching_clarity,
                    $this->engagement,
                    $this->practicality,
                    $this->user_friendliness,
                    $this->updates,
                ];

                // Filter out any null or non-numeric values to prevent errors
                $numericFields = array_filter($fields, 'is_numeric');

                $sum = array_sum($numericFields);

                return round($sum / 6, 2); // round to 2 decimal places
            },
        );
    }
}
