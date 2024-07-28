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
        'user_id'
    ];

    protected $casts = [
        'features' => 'array',
        'formats' => 'array',
        'limitations' => 'array',
        'topics' => 'array',
    ];

    public static function createFiltered($request)
    {
        $features = array_filter($request->input('features', []), function ($value) {
            return !is_null($value) && $value !== '';
        });
        $limitations = array_filter($request->input('limitations', []), function ($value) {
            return !is_null($value) && $value !== '';
        });

        $request->merge([
            'features' => array_values($features),
            'limitations' => array_values($limitations),
            'user_id' => \Auth::id(),
        ]);

        $resource = new self($request->except('tags'));
        $resource->save();

        if ($request->filled('tags')) {
            $resource->attachTags($request->tags);
        }

        return $resource;
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports()
    {
        return $this->morphMany(Vote::class, 'reportable');
    }
}
