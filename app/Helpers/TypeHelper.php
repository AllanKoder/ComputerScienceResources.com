<?php

namespace App\Helpers;

use App\Models\Resource;
use App\Models\ResourceEdit;
use App\Models\Comment;
use App\Models\Report;
use App\Models\ResourceReview;

class TypeHelper
{
    public static function getModelType($resource)
    {
        // Map resource types to their corresponding model classes
        $types = [
            'resource' => Resource::class,
            'resourceEdit' => ResourceEdit::class,
            'comment' => Comment::class,
            'report' => Report::class,
            'resourceReview' => ResourceReview::class,
            // other resource types
        ];

        return $types[$resource] ?? abort(404);
    }
}
