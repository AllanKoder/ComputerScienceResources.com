<?php

namespace App\Services;

class ModelResolverService
{
    protected $models = [
        'resource' => \App\Models\ComputerScienceResource::class,
        'comment' => \App\Models\Comment::class,
        // Add other model types here
    ];

    /**
     * Finds the model that exists for the given type and id
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

    protected function getModelClass($type)
    {
        return $this->models[$type] ?? null;
    }
}
