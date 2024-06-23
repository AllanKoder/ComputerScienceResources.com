<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;

class Resource extends Model
{
    use HasFactory;
    use HasTags;

    protected $table = 'resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'formats',
        'features',
        'limitations',
        'resource_url',
        'pricing',
        'topics',
        'difficulty',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'features' => 'array',
        'formats' => 'array',
        'limitations' => 'array',
        'topics' => 'array'
    ];

    /**
     * Get the comments for the Resource post.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
