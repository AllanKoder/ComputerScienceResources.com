<?php

namespace Tests\TestResources;

use App\Models\ResourceReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Event;

class ResourceReviewTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description'=> $this->description,
            'community'=> $this->community,
            'teaching_clarity'=> $this->teaching_clarity,
            'engagement'=> $this->engagement,
            'practicality'=> $this->practicality,
            'user_friendliness'=> $this->user_friendliness,
            'updates'=> $this->updates,
            'pros'=> $this->pros,
            'cons'=> $this->cons,
        ];
    }

    public static function fake(): array
    {
        // Create the model with disabled events
        $model = Event::fakeFor(fn() => ResourceReview::factory()->create());
    
        // Transform it to API form
        $formData = (new self($model))->toArray(request());
    
        // Delete after getting the array to avoid polluting the DB
        $model->delete();
    
        return $formData;
    }
}
