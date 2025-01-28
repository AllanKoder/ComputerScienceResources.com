<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class ComputerScienceResource extends Model
{
    /** @use HasFactory<\Database\Factories\ComputerScienceResourceFactory> */
    use HasFactory;
    use HasTags;

    protected $table = "computer_science_resources";

    /**
     * Accessor to get topic tags.
     *
     * @return Attribute
     */
    protected function topicTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('topics')->pluck('name')
        );
    }

    /**
     * Accessor to get programming language tags.
     *
     * @return Attribute
     */
    protected function programmingLanguageTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('programming_languages')->pluck('name')
        );
    }

    /**
     * Accessor to get general tags.
     *
     * @return Attribute
     */
    protected function generalTags(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tagsWithType('tags')->pluck('name')
        );
    }

    // Append calculated fields for JSON representation
    protected $appends = ['topic_tags', 'programming_language_tags', 'general_tags'];
}
