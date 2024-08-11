<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected $appends = ['tag_names'];

    public static function getResourceAttributes(): array
    {
        return [
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
            'tag_names',
        ];
    }
    
    protected $casts = [
        'features' => 'array',
        'formats' => 'array',
        'limitations' => 'array',
        'topics' => 'array',
    ];

    // Define the accessor and mutator for 'tag_names'
    protected function tagNames(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tags->pluck('name')->toArray(),
            set: function (array $tagNames) {
                $this->syncTags($tagNames);
            },
        );
    }


    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

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
}
