<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'comment_id',
        'user_id',
        'resource_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comment associated with the review.
     */
    public function comment()
    {
        return $this->hasOne(Comment::class, 'id', 'comment_id');
    }
}
