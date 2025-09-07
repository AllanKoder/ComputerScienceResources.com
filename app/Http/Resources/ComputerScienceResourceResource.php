<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComputerScienceResourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'page_url' => $this->page_url,
            'image_path' => $this->image_path,
            'platforms' => $this->platforms,
            'difficulties' => $this->difficulties,
            'pricing' => $this->pricing,
            'topic_tags' => $this->topic_tags,
            'programming_language_tags' => $this->programming_language_tags,
            'general_tags' => $this->general_tags,
        ];
    }
}
