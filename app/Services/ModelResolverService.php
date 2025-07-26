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
     * Finds the model that exists for the given key and id
     *
     * @param  $key,  the colloquial name for the key
     * @param  $id,  the id for the key
     *
     * returns null if no model exists, otherwise, it will return the model
     */
    public function resolve($key, $id)
    {
        $modelClass = $this->getModelClass($key);

        if (! $modelClass) {
            return null;
        }

        return $modelClass::find($id);
    }

    public function getModelClass($key)
    {
        return $this->models[$key] ?? null;
    }
}
