<?php

namespace App\Services;

class ModelResolverService
{
    protected $models = [
        'resource' => \App\Models\ComputerScienceResource::class,
        // Add other model types here
    ];

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
