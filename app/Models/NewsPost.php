<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class NewsPost extends Model
{
    use Sluggable;

    protected $fillable = ['title', 'thumbnail_path', 'excerpt', 'content'];

    protected $appends = ['thumbnail_url'];

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->thumbnail_path)
        );
    }


    /**
     * Return the sluggable configuration array for this model.
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }
}
