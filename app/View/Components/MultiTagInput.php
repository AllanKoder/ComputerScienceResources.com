<?php

namespace App\View\Components;

use Illuminate\View\Component;

class MultiTagInput extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $name,
    ) {
        $this->name = $name;
    }

    public function getURL(): string
    {
        return url()->current();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.multi-tag-input');
    }
}
