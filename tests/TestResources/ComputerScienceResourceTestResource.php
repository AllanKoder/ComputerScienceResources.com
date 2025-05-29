<?php

namespace Tests\TestResources;

use App\Events\TagFrequencyChanged;
use App\Models\ComputerScienceResource;
use Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComputerScienceResourceTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'page_url' => $this->page_url,
            'platforms' => $this->platforms,
            'difficulty' => $this->difficulty,
            'pricing' => $this->pricing,
            'topic_tags' => $this->topic_tags,
            'programming_language_tags' => $this->programming_language_tags,
            'general_tags' =>  $this->general_tags,
        ];
    }

    public static function fake(array $overrides = []): array
    {
        // Create the model with disabled events
        $model = Event::fakeFor(function () {
            return ComputerScienceResource::factory()->create();
        }, [TagFrequencyChanged::class]);

        // Transform it to API form
        $formData = (new self($model))->toArray(request());

        // Delete after getting the array to avoid polluting the DB
        $model->delete();

        // Merge and return
        return array_merge($formData, $overrides);
    }
}
