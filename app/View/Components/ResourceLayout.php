<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ResourceLayout extends Component
{
    public $title;
    public $description;
    public $features;
    public $limitations;
    public $resourceUrl;
    public $cost;
    public $topics;
    public $difficulty;

    public function __construct($title, $description, $features, $limitations, $resourceUrl, $cost, $topics, $difficulty)
    {
        $this->title = $title;
        $this->description = $description;
        $this->features = $features;
        $this->limitations = $limitations;
        $this->resourceUrl = $resourceUrl;
        $this->cost = $cost;
        $this->topics = $topics;
        $this->difficulty = $difficulty;
    }

    public function render()
    {
        return view('components.resource-layout');
    }
}
