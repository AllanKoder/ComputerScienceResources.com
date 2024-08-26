<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class ResourcesTable extends Component
{
    public $resources;

    public function __construct(LengthAwarePaginator $resources)
    {
        $this->resources = $resources;
    }

    public function render()
    {
        return view('components.resources-table');
    }
}
