<?php

namespace App\Exceptions\Resources;

use Exception;

class ResourceAlreadyCreatedException extends Exception
{
    public $resource;

    public function __construct($resource)
    {
        parent::__construct('Resource already exists');
        $this->resource = $resource;
    }
}
