<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ResourceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_size',
        'teaching_explanation_clarity',
        'technical_depth',
        'practicality_to_industry',
        'user_friendliness',
        'updates_and_maintenance',
        'review_title',
        'review_description',
        'user_id',
        'resource_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the resource review.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
