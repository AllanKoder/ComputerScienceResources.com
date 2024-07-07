<?php

namespace App\Helpers;

class TypeHelper
{
    public static function getModelType($resource)
    {
        // Map resource types to their corresponding model classes
        $types = [
            'resource' => 'App\Models\Resource',
            'comment' => 'App\Models\Comment',
            'report' => 'App\Models\Report',
            'review' => 'App\Models\Review'
            // other resource types
        ];

        return $types[$resource] ?? abort(404);
    }
}
