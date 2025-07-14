<?php

namespace App\Services;

class ModelResolverService
{
    protected $models = [
        'resource' => \App\Models\ComputerScienceResource::class,
        'review' => \App\Models\ResourceReview::class,
        'comment' => \App\Models\Comment::class,
        'edit' => \App\Models\ResourceEdits::class,
        // Add other model types here
    ];

    /**
     * Finds the model that exists for the given type and id
     *
     * @param $type, the colloquial name for the type
     * @param $id, the id for the type
     *
     * returns null if no model exists, otherwise, it will return the model
     */
    public function resolve($type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($id);
    }

    public function getModelClass($type)
    {
        return $this->models[$type] ?? null;
    }
}
